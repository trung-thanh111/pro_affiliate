<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;
use Carbon\Carbon;

class ImportPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:posts 
                            {file : Path to the JSON file containing posts and categories} 
                            {--chunk=200 : Number of posts to process in a single database transaction} 
                            {--force-update : Force update existing categories and posts if they have duplicate canonical slugs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a large volume of articles and categories from a nested JSON file in high-performance chunks.';

    /**
     * Existing post slugs cache: [canonical => post_id]
     *
     * @var array
     */
    protected $existingPosts = [];

    /**
     * Existing category slugs cache: [canonical => category_id]
     *
     * @var array
     */
    protected $existingCategories = [];

    /**
     * Statistics for the import run.
     *
     * @var array
     */
    protected $stats = [
        'categories_created' => 0,
        'categories_updated' => 0,
        'posts_created' => 0,
        'posts_updated' => 0,
        'posts_skipped' => 0,
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Set unlimited memory and execution time for large imports
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $filePath = $this->argument('file');
        if (!file_exists($filePath)) {
            $this->error("❌ File not found at path: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("🔄 Reading and parsing JSON file (this may take a few moments)...");
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("❌ Invalid JSON format: " . json_last_error_msg());
            return Command::FAILURE;
        }

        $this->info("⚡ Pre-loading existing slug mappings from database to optimize queries...");
        $this->loadSlugsCache();

        $this->info("📁 Processing categories and structuring article data...");
        $allPosts = [];
        $this->processCategoriesRecursive($data, 0, $allPosts);

        $totalPosts = count($allPosts);
        $this->info("📝 Found " . $this->stats['categories_created'] . " new categories, " . $this->stats['categories_updated'] . " updated categories.");
        $this->info("🚀 Found a total of {$totalPosts} articles to process.");

        if ($totalPosts === 0) {
            $this->warn("⚠️ No articles found to import.");
            return Command::SUCCESS;
        }

        $chunkSize = (int) $this->option('chunk');
        $forceUpdate = $this->option('force-update');

        $this->info("⏳ Importing articles in chunks of {$chunkSize} inside atomic transactions...");
        $bar = $this->output->createProgressBar($totalPosts);
        $bar->start();

        $chunks = array_chunk($allPosts, $chunkSize);
        foreach ($chunks as $chunk) {
            DB::beginTransaction();
            try {
                foreach ($chunk as $postData) {
                    $slug = $postData['canonical'];

                    if (isset($this->existingPosts[$slug])) {
                        if ($forceUpdate) {
                            $this->updatePost($this->existingPosts[$slug], $postData);
                            $this->stats['posts_updated']++;
                        } else {
                            $this->stats['posts_skipped']++;
                        }
                    } else {
                        $postId = $this->createPost($postData);
                        $this->existingPosts[$slug] = $postId; // Cache newly created slug
                        $this->stats['posts_created']++;
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Import chunk failed: " . $e->getMessage(), ['exception' => $e]);
                $this->error("\n❌ Error in chunk import: " . $e->getMessage());
                if ($this->confirm('Do you want to continue importing subsequent chunks?', true)) {
                    continue;
                } else {
                    $bar->finish();
                    return Command::FAILURE;
                }
            }
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->info("\n\n⚡ Re-calculating Category Nested Set indices (lft, rgt, level) using high-performance Nestedsetbie...");
        try {
            $nestedset = new Nestedsetbie([
                'table' => 'post_catalogues',
                'foreignkey' => 'post_catalogue_id',
                'language_id' => 1,
            ]);
            $nestedset->Get('level ASC, order ASC');
            $nestedset->Recursive(0, $nestedset->Set());
            $nestedset->Action();
            $this->info("✅ Category Nested Set indices recalculated successfully.");
        } catch (\Exception $e) {
            $this->error("⚠️ Nested Set calculation failed: " . $e->getMessage());
        }

        $this->info("\n🎉 IMPORT PROCESS COMPLETED!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['New Categories Created', $this->stats['categories_created']],
                ['Existing Categories Updated', $this->stats['categories_updated']],
                ['New Articles Imported', $this->stats['posts_created']],
                ['Existing Articles Updated', $this->stats['posts_updated']],
                ['Duplicate Articles Skipped', $this->stats['posts_skipped']],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Load existing canonical slug mappings to optimize indexing lookups.
     *
     * @return void
     */
    protected function loadSlugsCache()
    {
        // Cache post slugs mapping to post IDs
        $this->existingPosts = DB::table('routers')
            ->join('posts', 'posts.id', '=', 'routers.module_id')
            ->where('controllers', 'App\Http\Controllers\Frontend\PostController')
            ->whereNull('posts.deleted_at')
            ->pluck('module_id', 'canonical')
            ->toArray();

        // Cache category slugs mapping to category IDs
        $this->existingCategories = DB::table('routers')
            ->join('post_catalogues', 'post_catalogues.id', '=', 'routers.module_id')
            ->where('controllers', 'App\Http\Controllers\Frontend\PostCatalogueController')
            ->whereNull('post_catalogues.deleted_at')
            ->pluck('module_id', 'canonical')
            ->toArray();
    }

    /**
     * Process nested categories recursively and build a flat list of articles.
     *
     * @param array $categories
     * @param int $parentId
     * @param array $allPosts
     * @return void
     */
    protected function processCategoriesRecursive(array $categories, int $parentId, array &$allPosts)
    {
        foreach ($categories as $cat) {
            $slug = Str::slug($cat['canonical'] ?? Str::slug($cat['name']));
            $catId = null;

            $catPayload = [
                'parent_id' => $parentId,
                'image' => $cat['image'] ?? null,
                'publish' => $cat['publish'] ?? 2,
                'follow' => $cat['follow'] ?? 1,
                'order' => $cat['order'] ?? 0,
                'user_id' => $cat['user_id'] ?? 1,
                'short_name' => $cat['short_name'] ?? $cat['name'],
                'updated_at' => Carbon::now(),
            ];

            $catLanguagePayload = [
                'language_id' => 1,
                'name' => $cat['name'],
                'canonical' => $slug,
                'meta_title' => $cat['meta_title'] ?? $cat['name'],
                'meta_keyword' => $cat['meta_keyword'] ?? null,
                'meta_description' => $cat['meta_description'] ?? null,
                'description' => $cat['description'] ?? null,
                'content' => $cat['content'] ?? '',
                'updated_at' => Carbon::now(),
            ];

            if (isset($this->existingCategories[$slug])) {
                $catId = $this->existingCategories[$slug];
                if ($this->option('force-update')) {
                    DB::table('post_catalogues')->where('id', $catId)->update($catPayload);
                    DB::table('post_catalogue_language')
                        ->where('post_catalogue_id', $catId)
                        ->where('language_id', 1)
                        ->update($catLanguagePayload);
                    
                    // Update router in case slug was changed or path was updated
                    DB::table('routers')
                        ->where('module_id', $catId)
                        ->where('controllers', 'App\Http\Controllers\Frontend\PostCatalogueController')
                        ->update(['canonical' => $slug]);

                    $this->stats['categories_updated']++;
                }
            } else {
                $catPayload['created_at'] = Carbon::now();
                $catId = DB::table('post_catalogues')->insertGetId($catPayload);

                $catLanguagePayload['created_at'] = Carbon::now();
                $catLanguagePayload['post_catalogue_id'] = $catId;
                DB::table('post_catalogue_language')->insert($catLanguagePayload);

                DB::table('routers')->updateOrInsert(
                    ['canonical' => $slug],
                    [
                        'module_id' => $catId,
                        'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
                        'language_id' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );

                $this->existingCategories[$slug] = $catId; // Cache newly created category
                $this->stats['categories_created']++;
            }

            // Extract articles under this category
            if (isset($cat['posts']) && is_array($cat['posts'])) {
                foreach ($cat['posts'] as $post) {
                    $postSlug = Str::slug($post['canonical'] ?? Str::slug($post['name']));
                    
                    // Construct full data object for insertion
                    $allPosts[] = [
                        'post_catalogue_id' => $catId,
                        'image' => $post['image'] ?? null,
                        'album' => !empty($post['album']) ? (is_array($post['album']) ? json_encode($post['album']) : $post['album']) : '',
                        'publish' => $post['publish'] ?? 2,
                        'follow' => $post['follow'] ?? 1,
                        'order' => $post['order'] ?? 0,
                        'user_id' => $post['user_id'] ?? 1,
                        'video' => $post['video'] ?? '',
                        'template' => $post['template'] ?? 'default',
                        'viewed' => $post['viewed'] ?? 0,
                        'status_menu' => $post['status_menu'] ?? 0,
                        'short_name' => $post['short_name'] ?? '',
                        'logo' => $post['logo'] ?? '',
                        'extra' => '', // Skipped source_url
                        'comments' => $post['comments'] ?? 0,
                        'rate' => $post['rate'] ?? 5,
                        'recommend' => $post['recommend'] ?? ($post['is_featured'] ?? 0),
                        'post_type' => $post['post_type'] ?? 'text',
                        'released_at' => !empty($post['released_at']) ? Carbon::parse($post['released_at']) : Carbon::now(),
                        'files' => $post['files'] ?? '',
                        'is_review' => $post['is_review'] ?? 0,
                        'product_id' => $post['product_id'] ?? null,
                        
                        // Translation data
                        'name' => $post['name'],
                        'canonical' => $postSlug,
                        'meta_title' => $post['meta_title'] ?? $post['name'],
                        'meta_keyword' => $post['meta_keyword'] ?? null,
                        'meta_description' => $post['meta_description'] ?? null,
                        'description' => $post['description'] ?? null,
                        'content' => $post['content'] ?? '',
                    ];
                }
            }

            // Recursive call for nested children categories
            if (isset($cat['children']) && is_array($cat['children'])) {
                $this->processCategoriesRecursive($cat['children'], $catId, $allPosts);
            }
        }
    }

    /**
     * High performance single post insertion.
     *
     * @param array $data
     * @return int
     */
    protected function createPost(array $data)
    {
        $now = Carbon::now();

        // 1. Insert into base table 'posts'
        $postId = DB::table('posts')->insertGetId([
            'post_catalogue_id' => $data['post_catalogue_id'],
            'image' => $data['image'],
            'album' => $data['album'],
            'publish' => $data['publish'],
            'follow' => $data['follow'],
            'order' => $data['order'],
            'user_id' => $data['user_id'],
            'video' => $data['video'],
            'template' => $data['template'],
            'viewed' => $data['viewed'],
            'status_menu' => $data['status_menu'],
            'short_name' => $data['short_name'],
            'logo' => $data['logo'],
            'extra' => $data['extra'],
            'comments' => $data['comments'],
            'rate' => $data['rate'],
            'recommend' => $data['recommend'],
            'post_type' => $data['post_type'],
            'released_at' => $data['released_at'],
            'files' => $data['files'],
            'is_review' => $data['is_review'],
            'product_id' => $data['product_id'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Insert into translation pivot 'post_language'
        DB::table('post_language')->insert([
            'post_id' => $postId,
            'language_id' => 1,
            'name' => $data['name'],
            'canonical' => $data['canonical'],
            'meta_title' => $data['meta_title'],
            'meta_keyword' => $data['meta_keyword'],
            'meta_description' => $data['meta_description'],
            'description' => $data['description'],
            'content' => $data['content'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Insert into relationship pivot 'post_catalogue_post'
        DB::table('post_catalogue_post')->insert([
            'post_id' => $postId,
            'post_catalogue_id' => $data['post_catalogue_id'],
        ]);

        // 4. Insert into 'routers'
        DB::table('routers')->updateOrInsert(
            ['canonical' => $data['canonical']],
            [
                'module_id' => $postId,
                'controllers' => 'App\Http\Controllers\Frontend\PostController',
                'language_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return $postId;
    }

    /**
     * High performance single post update.
     *
     * @param int $postId
     * @param array $data
     * @return void
     */
    protected function updatePost(int $postId, array $data)
    {
        $now = Carbon::now();

        // 1. Update base table 'posts'
        DB::table('posts')->where('id', $postId)->update([
            'post_catalogue_id' => $data['post_catalogue_id'],
            'image' => $data['image'],
            'album' => $data['album'],
            'publish' => $data['publish'],
            'follow' => $data['follow'],
            'order' => $data['order'],
            'user_id' => $data['user_id'],
            'video' => $data['video'],
            'template' => $data['template'],
            'short_name' => $data['short_name'],
            'logo' => $data['logo'],
            'extra' => $data['extra'],
            'recommend' => $data['recommend'],
            'post_type' => $data['post_type'],
            'released_at' => $data['released_at'],
            'files' => $data['files'],
            'is_review' => $data['is_review'],
            'product_id' => $data['product_id'],
            'updated_at' => $now,
        ]);

        // 2. Update translation pivot 'post_language'
        DB::table('post_language')
            ->where('post_id', $postId)
            ->where('language_id', 1)
            ->update([
                'name' => $data['name'],
                'meta_title' => $data['meta_title'],
                'meta_keyword' => $data['meta_keyword'],
                'meta_description' => $data['meta_description'],
                'description' => $data['description'],
                'content' => $data['content'],
                'updated_at' => $now,
            ]);

        // 3. Sync relationship pivot 'post_catalogue_post'
        // Check if category relation already exists to prevent duplicate key error
        $exists = DB::table('post_catalogue_post')
            ->where('post_id', $postId)
            ->where('post_catalogue_id', $data['post_catalogue_id'])
            ->exists();

        if (!$exists) {
            DB::table('post_catalogue_post')->insert([
                'post_id' => $postId,
                'post_catalogue_id' => $data['post_catalogue_id'],
            ]);
        }

        // 4. Update 'routers' (in case canonical slug changed or updated)
        DB::table('routers')
            ->where('module_id', $postId)
            ->where('controllers', 'App\Http\Controllers\Frontend\PostController')
            ->update([
                'canonical' => $data['canonical'],
                'updated_at' => $now,
            ]);
    }
}
