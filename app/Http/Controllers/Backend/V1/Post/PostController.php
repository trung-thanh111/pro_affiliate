<?php

namespace App\Http\Controllers\Backend\V1\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Post\PostService;
use App\Repositories\Post\PostRepository;
use App\Repositories\Product\ProductRepository;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Language;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $postService;
    protected $postRepository;
    protected $productRepository;
    protected $languageRepository;
    protected $language;
    protected $nestedset;

    public function __construct(
        PostService $postService,
        PostRepository $postRepository,
        ProductRepository $productRepository,
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $this->language = current_language_id($locale);
            $this->initialize();
            return $next($request);
        });

        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->productRepository = $productRepository;
        $this->initialize();
        
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 

    public function index(Request $request){
        $this->authorize('modules', 'post.index');
        $posts = $this->postService->paginate($request, $this->language);
        $config = [
            'extendJs' => true,
            'model' => 'Post'
        ];
        $config['seo'] = __('messages.post');
        $template = 'backend.post.post.index';
        $dropdown  = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'posts'
        ));
    }

    public function create(){
        $this->authorize('modules', 'post.create');
        $config = $this->configData();
        $config['seo'] = __('messages.post');
        $config['method'] = 'create';
        $dropdown  = $this->nestedset->Dropdown();
        $products = $this->productRepository->all(['languages']);
        $posts = $this->postRepository->all(['languages']);
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'dropdown',
            'config',
            'products',
            'posts'
        ));
    }

    public function store(StorePostRequest $request)
    {
        $success = $this->postService->create($request, $this->language);

        if ($success) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()->back()->with('success', 'Thêm mới bản ghi thành công');
            }
            return redirect()->route('post.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->back()->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'post.update');
        $post = $this->postRepository->getPostById($id, $this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.post');
        $config['method'] = 'edit';
        $dropdown  = $this->nestedset->Dropdown();
        $album = json_decode($post->album);
        $products = $this->productRepository->all(['languages']);
        $posts = $this->postRepository->all(['languages']);
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'post',
            'album',
            'products',
            'posts'
        ));
    }

    public function update($id, UpdatePostRequest $request)
    {
        $queryString = base64_decode($request->getQueryString());

        if ($this->postService->update($id, $request, $this->language)) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()
                    ->route('post.edit', [$id, 'query' => base64_encode($queryString)])
                    ->with('success', 'Cập nhật bản ghi thành công');
            }

            return redirect()
                ->route('post.index', $queryString)
                ->with('success', 'Cập nhật bản ghi thành công');
        }

        return redirect()
            ->back()
            ->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    public function delete($id){
        $this->authorize('modules', 'post.destroy');
        $config['seo'] = __('messages.post');
        $post = $this->postRepository->getPostById($id, $this->language);
        $template = 'backend.post.post.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'post',
            'config',
        ));
    }

    public function destroy($id){
        if($this->postService->destroy($id)){
            return redirect()->route('post.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('post.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData(){
        return [
           'extendJs' => true
        ];
    }

    public function import()
    {
        $this->authorize('modules', 'post.create');
        $config = [
            'extendJs' => true,
        ];
        $config['seo'] = [
            'index' => [
                'title' => 'Import bài viết từ JSON',
            ]
        ];
        $template = 'backend.post.post.import';
        return view('backend.dashboard.layout', compact('template', 'config'));
    }

    public function uploadChunk(Request $request)
    {
        $this->authorize('modules', 'post.create');
        
        $chunk = $request->file('chunk');
        $index = (int) $request->input('index');
        $total = (int) $request->input('total');
        $uniqueId = preg_replace('/[^a-zA-Z0-9_]/', '', $request->input('uniqueId'));

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $filePath = $tempDir . '/' . $uniqueId . '.json';

        // Append chunk to the final file
        $chunkContent = file_get_contents($chunk->getRealPath());
        file_put_contents($filePath, $chunkContent, FILE_APPEND);

        return response()->json([
            'success' => true,
            'message' => 'Upload chunk thành công'
        ]);
    }

    public function processImport(Request $request)
    {
        $this->authorize('modules', 'post.create');
        
        ini_set('memory_limit', '-1');
        set_time_limit(0);

        $uniqueId = preg_replace('/[^a-zA-Z0-9_]/', '', $request->input('uniqueId'));
        $forceUpdate = filter_var($request->input('forceUpdate'), FILTER_VALIDATE_BOOLEAN);

        $filePath = storage_path('app/temp/' . $uniqueId . '.json');
        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File import không tồn tại trên server.'
            ], 404);
        }

        try {
            // Read and parse JSON content (PHP json_decode is extremely fast)
            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON không hợp lệ: " . json_last_error_msg());
            }

            // 1. Process Categories recursively & build mapping
            $existingCategories = DB::table('routers')
                ->join('post_catalogues', 'post_catalogues.id', '=', 'routers.module_id')
                ->where('controllers', 'App\Http\Controllers\Frontend\PostCatalogueController')
                ->whereNull('post_catalogues.deleted_at')
                ->pluck('module_id', 'canonical')
                ->toArray();

            // Load ALL existing canonical slugs from routers table to ensure system-wide uniqueness
            $takenSlugs = DB::table('routers')
                ->pluck('controllers', 'canonical')
                ->toArray();

            $processedCategoriesInRun = [];
            $allPosts = [];

            $stats = [
                'categories_created' => 0,
                'categories_updated' => 0,
                'posts_created' => 0,
                'posts_updated' => 0,
                'posts_skipped' => 0
            ];

            $this->processCategoriesRecursiveWeb($data, 0, $existingCategories, $processedCategoriesInRun, $allPosts, $takenSlugs, $forceUpdate, $stats);

            // 2. Pre-load existing post slugs
            $existingPosts = DB::table('routers')
                ->join('posts', 'posts.id', '=', 'routers.module_id')
                ->where('controllers', 'App\Http\Controllers\Frontend\PostController')
                ->whereNull('posts.deleted_at')
                ->pluck('module_id', 'canonical')
                ->toArray();

            $processedPostsInRun = [];

            // 3. Prepare arrays for Bulk Inserts and Updates
            $postsToInsert = [];
            $postLanguagesToInsert = [];
            $postCataloguePostsToInsert = [];
            $routersToInsert = [];

            $postsToUpdate = [];
            $postLanguagesToUpdate = [];
            $routersToUpdate = [];

            // Get next auto-increment post ID for pre-allocation
            $nextPostId = (DB::table('posts')->max('id') ?? 0) + 1;
            $now = Carbon::now();

            foreach ($allPosts as $post) {
                $slug = Str::slug($post['canonical'] ?? Str::slug($post['name']));

                // Collision Avoidance: Determine if slug is taken by anything other than this post
                $isColliding = isset($processedPostsInRun[$slug]) || 
                               (isset($takenSlugs[$slug]) && $takenSlugs[$slug] !== 'App\Http\Controllers\Frontend\PostController');

                if ($isColliding) {
                    $originalSlug = $slug;
                    $count = 1;
                    while (
                        isset($processedPostsInRun[$slug]) || 
                        isset($takenSlugs[$slug])
                    ) {
                        $slug = $originalSlug . '-' . $count;
                        $count++;
                    }
                }

                $processedPostsInRun[$slug] = true;
                $takenSlugs[$slug] = 'App\Http\Controllers\Frontend\PostController';

                if (isset($existingPosts[$slug])) {
                    $postId = $existingPosts[$slug];
                    if ($forceUpdate) {
                        $postsToUpdate[] = [
                            'id' => $postId,
                            'post_catalogue_id' => $post['post_catalogue_id'],
                            'image' => $post['image'] ?? null,
                            'album' => !empty($post['album']) ? (is_array($post['album']) ? json_encode($post['album']) : $post['album']) : '',
                            'publish' => $post['publish'] ?? 2,
                            'follow' => $post['follow'] ?? 1,
                            'order' => $post['order'] ?? 0,
                            'user_id' => $post['user_id'] ?? 1,
                            'video' => $post['video'] ?? '',
                            'template' => $post['template'] ?? 'default',
                            'short_name' => $post['short_name'] ?? '',
                            'logo' => $post['logo'] ?? '',
                            'extra' => '',
                            'recommend' => $post['recommend'] ?? ($post['is_featured'] ?? 0),
                            'post_type' => $post['post_type'] ?? 'text',
                            'released_at' => !empty($post['released_at']) ? Carbon::parse($post['released_at']) : $now,
                            'files' => $post['files'] ?? '',
                            'is_review' => $post['is_review'] ?? 0,
                            'product_id' => $post['product_id'] ?? null,
                            'updated_at' => $now,
                        ];

                        $postLanguagesToUpdate[] = [
                            'post_id' => $postId,
                            'language_id' => $this->language,
                            'name' => $post['name'],
                            'canonical' => $slug,
                            'meta_title' => $post['meta_title'] ?? $post['name'],
                            'meta_keyword' => $post['meta_keyword'] ?? null,
                            'meta_description' => $post['meta_description'] ?? null,
                            'description' => $post['description'] ?? null,
                            'content' => $post['content'] ?? '',
                            'updated_at' => $now,
                        ];

                        $routersToUpdate[] = [
                            'module_id' => $postId,
                            'canonical' => $slug,
                            'updated_at' => $now,
                        ];

                        $stats['posts_updated']++;
                    } else {
                        $stats['posts_skipped']++;
                    }
                } else {
                    $postId = $nextPostId++;

                    $postsToInsert[] = [
                        'id' => $postId,
                        'post_catalogue_id' => $post['post_catalogue_id'],
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
                        'extra' => '',
                        'comments' => $post['comments'] ?? 0,
                        'rate' => $post['rate'] ?? 5,
                        'recommend' => $post['recommend'] ?? ($post['is_featured'] ?? 0),
                        'post_type' => $post['post_type'] ?? 'text',
                        'released_at' => !empty($post['released_at']) ? Carbon::parse($post['released_at']) : $now,
                        'files' => $post['files'] ?? '',
                        'is_review' => $post['is_review'] ?? 0,
                        'product_id' => $post['product_id'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $postLanguagesToInsert[] = [
                        'post_id' => $postId,
                        'language_id' => $this->language,
                        'name' => $post['name'],
                        'canonical' => $slug,
                        'meta_title' => $post['meta_title'] ?? $post['name'],
                        'meta_keyword' => $post['meta_keyword'] ?? null,
                        'meta_description' => $post['meta_description'] ?? null,
                        'description' => $post['description'] ?? null,
                        'content' => $post['content'] ?? '',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $postCataloguePostsToInsert[] = [
                        'post_id' => $postId,
                        'post_catalogue_id' => $post['post_catalogue_id'],
                    ];

                    $routersToInsert[] = [
                        'canonical' => $slug,
                        'module_id' => $postId,
                        'controllers' => 'App\Http\Controllers\Frontend\PostController',
                        'language_id' => $this->language,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $stats['posts_created']++;
                }
            }

            // 4. Execute DB transactions in bulk batches
            DB::beginTransaction();
            try {
                // Bulk Insert new records (chunked at 2000 to fit database maximum limits)
                foreach (array_chunk($postsToInsert, 2000) as $chunk) {
                    DB::table('posts')->insert($chunk);
                }
                foreach (array_chunk($postLanguagesToInsert, 2000) as $chunk) {
                    DB::table('post_language')->insert($chunk);
                }
                foreach (array_chunk($postCataloguePostsToInsert, 2000) as $chunk) {
                    DB::table('post_catalogue_post')->insert($chunk);
                }
                foreach (array_chunk($routersToInsert, 2000) as $chunk) {
                    DB::table('routers')->upsert(
                        $chunk,
                        ['canonical'],
                        ['module_id', 'controllers', 'language_id', 'updated_at']
                    );
                }

                // Bulk Updates (if any updates are triggered)
                foreach ($postsToUpdate as $upPost) {
                    DB::table('posts')->where('id', $upPost['id'])->update($upPost);
                }
                foreach ($postLanguagesToUpdate as $upLang) {
                    DB::table('post_language')
                        ->where('post_id', $upLang['post_id'])
                        ->where('language_id', $this->language)
                        ->update($upLang);
                }
                foreach ($routersToUpdate as $upRoute) {
                    DB::table('routers')
                        ->where('module_id', $upRoute['module_id'])
                        ->where('controllers', 'App\Http\Controllers\Frontend\PostController')
                        ->update(['canonical' => $upRoute['canonical'], 'updated_at' => $upRoute['updated_at']]);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            // 5. Recalculate Tree Nested Set Index (Once for all categories)
            try {
                $nestedset = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
                    'language_id' => $this->language,
                ]);
                $nestedset->Get('level ASC, order ASC');
                $nestedset->Recursive(0, $nestedset->Set());
                $nestedset->Action();
            } catch (\Exception $ne) {
                Log::error("Nestedset Index calculation failed: " . $ne->getMessage());
            }

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error("Process Import Failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý import: ' . $e->getMessage()
            ], 500);
        } finally {
            // Secure cleanup of temporary file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    private function processCategoriesRecursiveWeb(array $categories, int $parentId, array &$existingCategories, array &$processedCategoriesInRun, array &$allPosts, array &$takenSlugs, bool $forceUpdate, array &$stats)
    {
        foreach ($categories as $cat) {
            $slug = Str::slug($cat['canonical'] ?? Str::slug($cat['name']));
            $catId = null;

            // Collision Avoidance: Determine if slug is taken by anything other than this category
            $isColliding = isset($processedCategoriesInRun[$slug]) || 
                           (isset($takenSlugs[$slug]) && $takenSlugs[$slug] !== 'App\Http\Controllers\Frontend\PostCatalogueController');

            if ($isColliding) {
                $originalSlug = $slug;
                $count = 1;
                while (
                    isset($processedCategoriesInRun[$slug]) || 
                    isset($takenSlugs[$slug])
                ) {
                    $slug = $originalSlug . '-' . $count;
                    $count++;
                }
            }

            $processedCategoriesInRun[$slug] = true;
            $takenSlugs[$slug] = 'App\Http\Controllers\Frontend\PostCatalogueController';

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
                'language_id' => $this->language,
                'name' => $cat['name'],
                'canonical' => $slug,
                'meta_title' => $cat['meta_title'] ?? $cat['name'],
                'meta_keyword' => $cat['meta_keyword'] ?? null,
                'meta_description' => $cat['meta_description'] ?? null,
                'description' => $cat['description'] ?? null,
                'content' => $cat['content'] ?? '',
                'updated_at' => Carbon::now(),
            ];

            if (isset($existingCategories[$slug])) {
                $catId = $existingCategories[$slug];
                if ($forceUpdate) {
                    DB::table('post_catalogues')->where('id', $catId)->update($catPayload);
                    DB::table('post_catalogue_language')
                        ->where('post_catalogue_id', $catId)
                        ->where('language_id', $this->language)
                        ->update($catLanguagePayload);

                    DB::table('routers')
                        ->where('module_id', $catId)
                        ->where('controllers', 'App\Http\Controllers\Frontend\PostCatalogueController')
                        ->update(['canonical' => $slug]);

                    $stats['categories_updated']++;
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
                        'language_id' => $this->language,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );

                $existingCategories[$slug] = $catId;
                $stats['categories_created']++;
            }

            // Collect posts associated with this category
            if (isset($cat['posts']) && is_array($cat['posts'])) {
                foreach ($cat['posts'] as $post) {
                    $post['post_catalogue_id'] = $catId;
                    $allPosts[] = $post;
                }
            }

            if (isset($cat['children']) && is_array($cat['children'])) {
                $this->processCategoriesRecursiveWeb($cat['children'], $catId, $existingCategories, $processedCategoriesInRun, $allPosts, $takenSlugs, $forceUpdate, $stats);
            }
        }
    }
}
