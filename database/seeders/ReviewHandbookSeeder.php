<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewHandbookSeeder extends Seeder
{
    public function run()
    {
        $catId = 4; // News category
        $languageId = 1; // Vietnamese

        // Delete old posts in this category to clean up
        $oldPostIds = DB::table('post_catalogue_post')->where('post_catalogue_id', $catId)->pluck('post_id');
        DB::table('posts')->whereIn('id', $oldPostIds)->delete();
        DB::table('post_language')->whereIn('post_id', $oldPostIds)->delete();
        DB::table('post_catalogue_post')->where('post_catalogue_id', $catId)->delete();
        
        $posts = [
            [
                'title' => 'DJI Osmo Pocket 4 ra mắt: Cảm biến CMOS 1 inch, hỗ trợ D-Log 10-bit...',
                'desc' => 'DJI Osmo Pocket 4 ra mắt: Cảm biến CMOS 1 inch, hỗ trợ D-Log 10-bit, nhiều tính năng thông minh, giá từ 11.635 triệu đồng',
                'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80&w=1000',
            ],
            [
                'title' => 'Trên tay DJI Osmo Pocket 4: Thiết kế nhỏ gọn, màn hình xoay cực đỉnh',
                'desc' => 'Đánh giá chi tiết DJI Osmo Pocket 4 sau 1 tuần sử dụng thực tế. Liệu có xứng đáng nâng cấp?',
                'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=1000',
            ],
            [
                'title' => 'So sánh DJI Osmo Pocket 4 và Pocket 3: Những cải tiến vượt bậc',
                'desc' => 'Những điểm khác biệt chính giữa 2 thế hệ camera cầm tay hot nhất hiện nay của DJI.',
                'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&q=80&w=1000',
            ],
            [
                'title' => 'Top 5 phụ kiện không thể thiếu cho DJI Osmo Pocket 4',
                'desc' => 'Giúp bạn quay vlog chuyên nghiệp hơn với những phụ kiện đi kèm cực chất.',
                'image' => 'https://images.unsplash.com/photo-1492707892479-7bc8d5a4ee93?auto=format&fit=crop&q=80&w=1000',
            ],
            [
                'title' => 'Hướng dẫn quay Cinematic với DJI Osmo Pocket 4',
                'desc' => 'Bí quyết để có những thước phim điện ảnh chỉ với chiếc camera nhỏ gọn trong lòng bàn tay.',
                'image' => 'https://images.unsplash.com/photo-1504194104404-433180773017?auto=format&fit=crop&q=80&w=1000',
            ],
        ];

        foreach ($posts as $key => $data) {
            $post = Post::create([
                'post_catalogue_id' => $catId,
                'image' => $data['image'],
                'publish' => 2,
                'order' => $key,
                'user_id' => 1,
            ]);

            DB::table('post_language')->insert([
                'post_id' => $post->id,
                'language_id' => $languageId,
                'name' => $data['title'],
                'description' => $data['desc'],
                'content' => $data['desc'],
                'canonical' => Str::slug($data['title']) . '-' . $post->id,
                'meta_title' => $data['title'],
                'meta_description' => $data['desc'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('post_catalogue_post')->insert([
                'post_id' => $post->id,
                'post_catalogue_id' => $catId,
            ]);
        }
    }
}
