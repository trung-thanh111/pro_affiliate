<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DetailedReviewSeeder extends Seeder
{
    public function run()
    {
        $catId = 4; // News/Review category
        $languageId = 1;

        // Clean up previous seeded posts and their routers to prevent 404/duplicates
        $oldPostIds = Post::where('is_review', 1)->pluck('id');
        $oldCanonicals = DB::table('post_language')->whereIn('post_id', $oldPostIds)->pluck('canonical');
        
        DB::table('routers')->whereIn('canonical', $oldCanonicals)->delete();
        DB::table('post_language')->whereIn('post_id', $oldPostIds)->delete();
        DB::table('post_catalogue_post')->whereIn('post_id', $oldPostIds)->delete();
        Post::whereIn('id', $oldPostIds)->delete();

        $reviews = [
            [
                'name' => 'Đánh giá chi tiết DJI Osmo Pocket 4: Đỉnh cao quay vlog chuyên nghiệp 2024',
                'description' => 'Khám phá sức mạnh của cảm biến 1 inch, khả năng quay 4K/120fps và hệ thống chống rung gimbal 3 trục đỉnh cao trên DJI Osmo Pocket 4.',
                'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=1000',
                'tags' => ['DJI', 'Vlog', 'Camera', 'Công nghệ'],
            ],
            [
                'name' => 'iPhone 15 Pro Max sau 6 tháng sử dụng: Liệu khung viền Titan có thực sự bền?',
                'description' => 'Đánh giá thực tế hiệu năng chip A17 Pro, thời lượng pin và chất lượng camera zoom 5x của siêu phẩm nhà Apple.',
                'image' => 'https://images.unsplash.com/photo-1591333139245-2b4115e2916c?q=80&w=1000',
                'tags' => ['Apple', 'iPhone', 'Smartphone', 'iOS'],
            ],
            [
                'name' => 'MacBook Air M3 vs MacBook Pro M3: Lựa chọn nào tối ưu cho dân đồ họa?',
                'description' => 'So sánh chi tiết hiệu năng xử lý, khả năng tản nhiệt và tính di động giữa hai dòng laptop hot nhất hiện nay.',
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1000',
                'tags' => ['Apple', 'MacBook', 'Laptop', 'Đồ họa'],
            ],
            [
                'name' => 'Sony WH-1000XM5: Vua chống ồn vẫn giữ vững ngôi vương?',
                'description' => 'Trải nghiệm chất âm, khả năng chống rung chủ động ANC và sự thoải mái khi đeo trong thời gian dài của Sony XM5.',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1000',
                'tags' => ['Sony', 'Tai nghe', 'Audio', 'ANC'],
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra: Kỷ nguyên AI trên smartphone đã thực sự bắt đầu?',
                'description' => 'Khám phá các tính năng Galaxy AI đột phá, màn hình chống chói Gorilla Armor và camera 200MP siêu sắc nét.',
                'image' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?q=80&w=1000',
                'tags' => ['Samsung', 'Galaxy', 'Android', 'AI'],
            ],
            [
                'name' => 'Dyson V15 Detect: Máy hút bụi không dây thông minh nhất thế giới?',
                'description' => 'Đánh giá khả năng làm sạch với công nghệ laser soi bụi, cảm biến Piezo và lực hút cực mạnh của Dyson V15.',
                'image' => 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?q=80&w=1000',
                'tags' => ['Dyson', 'Gia dụng', 'Thông minh', 'Clean'],
            ],
            [
                'name' => 'Nồi chiên không dầu Philips Air Fryer XXL: Xứng đáng với giá tiền?',
                'description' => 'Thử nghiệm công nghệ Smart Sensing, khả năng tách dầu mỡ và chất lượng món ăn khi chế biến bằng Philips XXL.',
                'image' => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?q=80&w=1000',
                'tags' => ['Philips', 'Nhà bếp', 'Nấu ăn', 'Healthy'],
            ],
            [
                'name' => 'Nintendo Switch OLED: Có nên nâng cấp khi Switch 2 sắp ra mắt?',
                'description' => 'So sánh màn hình OLED rực rỡ với phiên bản tiêu chuẩn và đánh giá thư viện game đỉnh cao cuối vòng đời.',
                'image' => 'https://images.unsplash.com/photo-1578303512597-81e6cc155b3e?q=80&w=1000',
                'tags' => ['Nintendo', 'Gaming', 'Console', 'Switch'],
            ],
            [
                'name' => 'Robot hút bụi Roborock S8 Pro Ultra: Tự động hóa hoàn toàn việc dọn dẹp?',
                'description' => 'Đánh giá trạm sạc đa năng, khả năng lau nhà rung và hệ thống tránh vật cản AI của Roborock S8.',
                'image' => 'https://images.unsplash.com/photo-1518314916301-73c01887233c?q=80&w=1000',
                'tags' => ['Roborock', 'SmartHome', 'Robot', 'Gia dụng'],
            ],
            [
                'name' => 'Logitech MX Master 3S: Chuột công thái học tốt nhất cho lập trình viên?',
                'description' => 'Trải nghiệm nút cuộn MagSpeed, cảm biến 8000 DPI và khả năng tùy biến phím tắt cực mạnh cho công việc.',
                'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?q=80&w=1000',
                'tags' => ['Logitech', 'Phụ kiện', 'Productivity', 'Mouse'],
            ],
            [
                'name' => 'Máy pha sữa thông minh Baby Brezza Formula Pro Advanced có thực sự hữu ích?',
                'description' => 'Đánh giá độ chính xác khi pha sữa, tốc độ và khả năng vệ sinh của chiếc máy pha sữa hot nhất hiện nay.',
                'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=1000',
                'tags' => ['Mẹ và Bé', 'BabyBrezza', 'Tiện dụng', 'Sức khỏe'],
            ],
            [
                'name' => 'Xe đẩy em bé Combi Sugocal Switch: Sự kết hợp hoàn hảo giữa êm ái và linh hoạt',
                'description' => 'Khám phá công nghệ giảm xóc EggShock, trọng lượng siêu nhẹ và khả năng đảo chiều tay cầm thông minh.',
                'image' => 'https://images.unsplash.com/photo-1594913785162-e6786967fcc9?q=80&w=1000',
                'tags' => ['Mẹ và Bé', 'Combi', 'Xe đẩy', 'Du lịch'],
            ],
            [
                'name' => 'Đánh giá khóa cửa vân tay Kaadas K20 Pro: An toàn tối đa cho ngôi nhà của bạn',
                'description' => 'Trải nghiệm mở khóa bằng gương mặt 3D, vân tay siêu nhạy và khả năng quản lý qua app điện thoại.',
                'image' => 'https://images.unsplash.com/photo-1558002038-1055907df827?q=80&w=1000',
                'tags' => ['SmartLock', 'An ninh', 'Home', 'Kaadas'],
            ],
            [
                'name' => 'Máy lọc không khí Xiaomi Smart Air Purifier 4 Pro: Hiệu năng tốt trong tầm giá',
                'description' => 'Đánh giá khả năng lọc bụi mịn PM2.5, khử mùi và độ ồn khi hoạt động của máy lọc không khí Xiaomi.',
                'image' => 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?q=80&w=1000',
                'tags' => ['Xiaomi', 'AirPurifier', 'Sức khỏe', 'Home'],
            ],
            [
                'name' => 'Tủ lạnh Samsung Bespoke: Khi đồ gia dụng trở thành tác phẩm nghệ thuật',
                'description' => 'Khám phá khả năng tùy biến màu sắc, công nghệ Metal Cooling và thiết kế phẳng hiện đại của dòng Bespoke.',
                'image' => 'https://images.unsplash.com/photo-1571175432230-01c24822509e?q=80&w=1000',
                'tags' => ['Samsung', 'Bespoke', 'Gia dụng', 'Design'],
            ]
        ];

        foreach ($reviews as $index => $item) {
            $content = $this->generateDetailedContent($item['name'], $item['description']);
            $canonical = Str::slug($item['name']) . '-' . (1000 + $index); // Use temporary suffix to ensure unique

            $post = Post::create([
                'post_catalogue_id' => $catId,
                'image' => $item['image'],
                'publish' => 2,
                'order' => $index,
                'user_id' => 1,
                'recommend' => 2, // Hot topic
                'is_review' => 1,
                'created_at' => now()->subDays(15 - $index),
            ]);

            // Update canonical with real ID
            $realCanonical = Str::slug($item['name']) . '-' . $post->id;

            DB::table('post_language')->insert([
                'post_id' => $post->id,
                'language_id' => $languageId,
                'name' => $item['name'],
                'description' => $item['description'],
                'content' => $content,
                'canonical' => $realCanonical,
                'meta_title' => $item['name'],
                'meta_description' => $item['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('post_catalogue_post')->insert([
                'post_id' => $post->id,
                'post_catalogue_id' => $catId,
            ]);

            // Register dynamic route in routers table
            DB::table('routers')->insert([
                'canonical' => $realCanonical,
                'module_id' => $post->id,
                'controllers' => 'App\Http\Controllers\Frontend\PostController',
                'language_id' => $languageId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function generateDetailedContent($name, $desc)
    {
        return "
            <p>Chào mừng các bạn đã quay trở lại với chuyên mục đánh giá chi tiết của chúng tôi. Hôm nay, chúng ta sẽ cùng mổ xẻ một trong những sản phẩm hot nhất thị trường hiện nay: <strong>{$name}</strong>. Đây là sản phẩm đang nhận được rất nhiều sự quan tâm từ cộng đồng người dùng nhờ vào những hứa hẹn về hiệu năng và trải nghiệm đột phá.</p>
            
            <h2>1. Thiết kế và Hoàn thiện</h2>
            <p>Ngay từ cái nhìn đầu tiên, {$name} đã gây ấn tượng mạnh với phong cách thiết kế hiện đại và sang trọng. Nhà sản xuất đã chăm chút tỉ mỉ đến từng chi tiết nhỏ nhất, từ các đường bo cong mềm mại đến chất liệu hoàn thiện cao cấp. {$desc}</p>
            <p>Trọng lượng của sản phẩm được tối ưu hóa rất tốt, mang lại cảm giác cầm nắm chắc chắn nhưng không quá nặng nề. Các nút bấm vật lý và cổng kết nối được bố trí một cách khoa học, giúp người dùng dễ dàng thao tác ngay cả trong những lần sử dụng đầu tiên.</p>
            
            <h2>2. Hiệu năng thực tế</h2>
            <p>Để đánh giá chính xác sức mạnh của {$name}, chúng tôi đã tiến hành một loạt các bài thử nghiệm khắc nghiệt. Kết quả cho thấy sản phẩm hoạt động cực kỳ mượt mà, hầu như không có hiện tượng giật lag ngay cả khi xử lý các tác vụ nặng nhất. Đây thực sự là một điểm cộng lớn so với các đối thủ trong cùng phân khúc giá.</p>
            <ul>
                <li><strong>Tốc độ xử lý:</strong> Vượt mong đợi, xử lý đa nhiệm cực tốt.</li>
                <li><strong>Độ ổn định:</strong> Hoạt động liên tục trong 24h mà không gặp bất kỳ lỗi nhỏ nào.</li>
                <li><strong>Khả năng tương thích:</strong> Dễ dàng kết nối với nhiều thiết bị ngoại vi khác nhau.</li>
            </ul>
            
            <h2>3. Trải nghiệm người dùng</h2>
            <p>Giao diện điều khiển của {$name} rất thân thiện và dễ sử dụng. Hệ điều hành đi kèm được tối ưu hóa sâu, giúp mọi thao tác vuốt chạm trở nên nhạy bén hơn. Chúng tôi đặc biệt thích tính năng thông minh mới được tích hợp, nó thực sự giúp cuộc sống hàng ngày trở nên tiện lợi hơn rất nhiều.</p>
            <p>Chất lượng hiển thị (nếu là thiết bị màn hình) hoặc chất lượng âm thanh đều ở mức xuất sắc. Màu sắc rực rỡ, độ chi tiết cao và âm thanh sống động là những gì bạn có thể kỳ vọng từ chiếc máy này.</p>
            
            <h2>4. Ưu và Nhược điểm</h2>
            <p>Mặc dù là một sản phẩm xuất sắc, nhưng {$name} vẫn có những điểm cần lưu ý:</p>
            <div class='review-grid' style='display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;'>
                <div class='pros' style='background: #f0fff4; padding: 15px; border-radius: 8px;'>
                    <h4 style='color: #2f855a; margin-top: 0;'>Ưu điểm</h4>
                    <ul style='padding-left: 20px;'>
                        <li>Thiết kế cao cấp, chất liệu bền bỉ.</li>
                        <li>Hiệu năng mạnh mẽ hàng đầu phân khúc.</li>
                        <li>Nhiều tính năng thông minh đột phá.</li>
                        <li>Thời lượng sử dụng ấn tượng.</li>
                    </ul>
                </div>
                <div class='cons' style='background: #fff5f5; padding: 15px; border-radius: 8px;'>
                    <h4 style='color: #c53030; margin-top: 0;'>Nhược điểm</h4>
                    <ul style='padding-left: 20px;'>
                        <li>Giá thành còn hơi cao so với mặt bằng chung.</li>
                        <li>Một vài phụ kiện đi kèm cần mua rời.</li>
                    </ul>
                </div>
            </div>
            
            <h2>5. Tổng kết và Đánh giá cuối cùng</h2>
            <p>Tóm lại, <strong>{$name}</strong> là một sự đầu tư xứng đáng nếu bạn đang tìm kiếm một sản phẩm đỉnh cao về công nghệ và trải nghiệm. Dù vẫn còn một vài nhược điểm nhỏ, nhưng những giá trị mà nó mang lại hoàn toàn vượt xa sự mong đợi.</p>
            <p><strong>Điểm đánh giá chung: 9.5/10</strong></p>
            <p>Hy vọng bài viết này đã cung cấp cho bạn cái nhìn toàn diện nhất về sản phẩm. Đừng quên để lại ý kiến của bạn ở phần bình luận bên dưới nhé!</p>
        ";
    }
}
