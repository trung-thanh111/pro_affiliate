<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class FixPost404Seeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clean up all posts and their related data
        DB::table('posts')->truncate();
        DB::table('post_language')->truncate();
        DB::table('post_catalogue_post')->truncate();
        
        // Clean up routers for posts
        DB::table('routers')
            ->where('controllers', 'App\Http\Controllers\Frontend\PostController')
            ->orWhere('controllers', 'App\Http\Controllers\Frontend\PostCatalogueController')
            ->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Now run the detailed review seeder again
        $this->call(DetailedReviewSeeder::class);
    }
}
