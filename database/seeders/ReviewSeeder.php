<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        $catId = 4; // News/Review category
        $languageId = 1;

        $posts = [
            [
                'name' => 'Đánh giá chi tiết DJI Osmo Pocket 4: Đỉnh cao quay vlog chuyên nghiệp',
                'description' => 'DJI Osmo Pocket 4 mang đến cảm biến 1 inch mạnh mẽ, hỗ trợ quay 4K/120fps và hệ thống lấy nét cực nhanh, biến nó thành công cụ không thể thiếu cho các vlogger.',
                'content' => 'Sau một thời gian trải nghiệm thực tế, DJI Osmo Pocket 4 thực sự là một bước tiến lớn. Với kích thước nhỏ gọn nhưng mang trong mình sức mạnh của những chiếc máy ảnh chuyên nghiệp, máy cho chất lượng hình ảnh sắc nét ngay cả trong điều kiện thiếu sáng. Hệ thống gimbal 3 trục hoạt động cực kỳ ổn định, giúp mọi khung hình trở nên mượt mà hơn bao giờ hết.',
                'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=1000',
            ],
            [
                'name' => 'Top 10 món đồ công nghệ không thể thiếu cho góc làm việc 2024',
                'description' => 'Cập nhật danh sách những thiết bị công nghệ giúp tối ưu hóa hiệu suất làm việc và tạo không gian sáng tạo hiện đại.',
                'content' => 'Một góc làm việc lý tưởng không chỉ cần sự ngăn nắp mà còn cần những thiết bị hỗ trợ thông minh. Từ bàn phím cơ không dây, chuột công thái học đến màn hình 4K sắc nét, mỗi món đồ đều đóng vai trò quan trọng trong việc nâng cao trải nghiệm làm việc hàng ngày của bạn.',
                'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1000',
            ],
            [
                'name' => 'So sánh iPhone 15 Pro Max và Samsung Galaxy S24 Ultra: Kẻ tám lạng người nửa cân',
                'description' => 'Cuộc đối đầu kịch tính giữa hai siêu phẩm hàng đầu thế giới smartphone hiện nay. Đâu là sự lựa chọn tốt nhất cho bạn?',
                'content' => 'iPhone 15 Pro Max với khung viền titan nhẹ nhàng và chip A17 pro mạnh mẽ đối đầu trực diện với Galaxy S24 Ultra sở hữu bút S-Pen đa năng và màn hình Dynamic AMOLED rực rỡ. Cả hai đều mang đến những trải nghiệm đỉnh cao, nhưng mỗi máy lại có những ưu điểm riêng biệt phù hợp với từng đối tượng người dùng.',
                'image' => 'https://images.unsplash.com/photo-1591333139245-2b4115e2916c?q=80&w=1000',
            ],
            [
                'name' => 'Bí quyết chọn mua tai nghe chống ồn phù hợp với túi tiền',
                'description' => 'Hướng dẫn chi tiết cách phân loại và lựa chọn tai nghe chống ồn (ANC) dựa trên nhu cầu sử dụng và ngân sách cá nhân.',
                'content' => 'Tai nghe chống ồn ngày càng trở nên phổ biến, nhưng không phải chiếc tai nghe nào cũng mang lại hiệu quả như mong đợi. Bạn cần chú ý đến công nghệ ANC chủ động, thời lượng pin và cảm giác đeo thoải mái khi sử dụng trong thời gian dài để có được sự lựa chọn ưng ý nhất.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1000',
            ],
            [
                'name' => 'Đánh giá MacBook Air M3: Hiệu năng vượt trội trong thiết kế siêu mỏng',
                'description' => 'Sự kết hợp hoàn hảo giữa tính di động và sức mạnh xử lý của chip M3 mới nhất từ Apple.',
                'content' => 'MacBook Air M3 không chỉ giữ vững vị thế là chiếc laptop mỏng nhẹ nhất mà còn mang lại hiệu năng ấn tượng, đủ sức gánh vác các tác vụ chỉnh sửa video và đồ họa cơ bản. Với thời lượng pin lên đến 18 tiếng, đây là người bạn đồng hành lý tưởng cho những ai thường xuyên di chuyển.',
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1000',
            ]
        ];

        foreach ($posts as $key => $val) {
            $post = Post::create([
                'image' => $val['image'],
                'publish' => 2,
                'order' => $key,
                'user_id' => 1,
                'recommend' => 2, // Hot topic
                'is_review' => 1,
            ]);

            DB::table('post_language')->insert([
                'post_id' => $post->id,
                'language_id' => $languageId,
                'name' => $val['name'],
                'description' => $val['description'],
                'content' => $val['content'],
                'canonical' => Str::slug($val['name']) . '-' . $post->id,
                'meta_title' => $val['name'],
                'meta_description' => $val['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('post_catalogue_post')->insert([
                'post_id' => $post->id,
                'post_catalogue_id' => $catId,
            ]);

            // Register dynamic route in routers table to prevent 404
            DB::table('routers')->insert([
                'canonical' => Str::slug($val['name']) . '-' . $post->id,
                'module_id' => $post->id,
                'controllers' => 'App\Http\Controllers\Frontend\PostController',
                'language_id' => $languageId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
