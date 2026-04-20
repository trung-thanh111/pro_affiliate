<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCatalogue;
use App\Models\Promotion;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TechProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->truncate();
        DB::table('product_language')->truncate();
        DB::table('product_catalogues')->truncate();
        DB::table('product_catalogue_language')->truncate();
        DB::table('product_catalogue_product')->truncate();
        DB::table('product_variants')->truncate();
        DB::table('product_variant_language')->truncate();
        DB::table('promotions')->truncate();
        DB::table('promotion_product_variant')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['name' => 'Laptop', 'canonical' => 'laptop', 'image' => 'https://picsum.photos/400/400?random=1'],
            ['name' => 'Smartphone', 'canonical' => 'smartphone', 'image' => 'https://picsum.photos/400/400?random=2'],
            ['name' => 'Tablet', 'canonical' => 'tablet', 'image' => 'https://picsum.photos/400/400?random=3'],
            ['name' => 'Smartwatch', 'canonical' => 'smartwatch', 'image' => 'https://picsum.photos/400/400?random=4'],
            ['name' => 'Camera', 'canonical' => 'camera', 'image' => 'https://picsum.photos/400/400?random=5'],
            ['name' => 'Âm thanh', 'canonical' => 'am-thanh', 'image' => 'https://picsum.photos/400/400?random=6'],
            ['name' => 'Gaming Gear', 'canonical' => 'gaming-gear', 'image' => 'https://picsum.photos/400/400?random=7'],
            ['name' => 'Phụ kiện', 'canonical' => 'phu-kien', 'image' => 'https://picsum.photos/400/400?random=8'],
            ['name' => 'Màn hình', 'canonical' => 'man-hinh', 'image' => 'https://picsum.photos/400/400?random=9'],
            ['name' => 'Networking', 'canonical' => 'networking', 'image' => 'https://picsum.photos/400/400?random=10'],
            ['name' => 'Đồ gia dụng', 'canonical' => 'do-gia-dung', 'image' => 'https://picsum.photos/400/400?random=11'],
            ['name' => 'Smart Home', 'canonical' => 'smart-home', 'image' => 'https://picsum.photos/400/400?random=12'],
            ['name' => 'PC Desktop', 'canonical' => 'pc-desktop', 'image' => 'https://picsum.photos/400/400?random=13'],
            ['name' => 'Lưu trữ SSD', 'canonical' => 'luu-tru', 'image' => 'https://picsum.photos/400/400?random=14'],
            ['name' => 'Máy in văn phòng', 'canonical' => 'may-in', 'image' => 'https://picsum.photos/400/400?random=15'],
        ];

        $techSpecs = [
            'mạnh mẽ hiệu năng cao vượt trội', 'thiết kế sang trọng tinh tế đẳng cấp', 'màn hình siêu nét chuẩn màu điện ảnh',
            'pin bền bỉ cả ngày dài sử dụng', 'hỗ trợ sạc nhanh siêu tốc an toàn', 'bảo hành chính hãng 24 tháng tận nơi',
            'tặng kèm bộ quà tặng giá trị cực khủng', 'phiên bản giới hạn đặc biệt duy nhất', 'nhập khẩu nguyên chiếc từ châu Âu',
            'công nghệ tiên tiến nhất năm 2024 mới', 'độ bền đạt chuẩn quân đội chống va đập', 'kết nối không dây ổn định mượt mà',
            'âm thanh sống động chân thực đỉnh cao', 'camera độ phân giải cực cao lấy nét nhanh', 'hệ điều hành mới nhất tối ưu nhất'
        ];

        foreach ($categories as $index => $catData) {
            $cat = ProductCatalogue::create([
                'publish' => 2,
                'image' => $catData['image'],
                'user_id' => 1,
                'level' => 0,
                'lft' => ($index * 2) + 1,
                'rgt' => ($index * 2) + 2,
            ]);

            DB::table('product_catalogue_language')->insert([
                'product_catalogue_id' => $cat->id,
                'language_id' => 1,
                'name' => $catData['name'],
                'canonical' => $catData['canonical'],
                'description' => 'Danh mục ' . $catData['name'] . ' chất lượng cao với nhiều ưu đãi hấp dẫn.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create 3-5 products per category
            $productCount = rand(3, 5);
            for ($i = 1; $i <= $productCount; $i++) {
                $price = rand(10, 100) * 500000;
                
                // Build a long name (16-20 words)
                $randomSpecs = array_rand(array_flip($techSpecs), rand(6, 8));
                $longName = 'Sản phẩm ' . $catData['name'] . ' mã hiệu ' . Str::random(5) . ' thế hệ mới ' . implode(' và ', $randomSpecs) . ' là sự lựa chọn hoàn hảo nhất cho người dùng công nghệ chuyên nghiệp hiện nay';
                $longName = Str::limit($longName, 250, ''); 

                $product = Product::create([
                    'product_catalogue_id' => $cat->id,
                    'image' => 'https://picsum.photos/400/400?random=' . ($cat->id * 100 + $i),
                    'publish' => 2,
                    'price' => $price,
                    'stock' => rand(10, 100),
                    'sold' => rand(50, 5000),
                    'code' => strtoupper(Str::random(8)),
                    'user_id' => 1,
                    'variant' => '{"size":["S","M","L"],"color":["Đen","Trắng","Bạc"]}'
                ]);

                DB::table('product_language')->insert([
                    'product_id' => $product->id,
                    'language_id' => 1,
                    'name' => $longName,
                    'canonical' => $catData['canonical'] . '-p-' . $product->id . '-' . time(),
                    'description' => 'Mô tả ngắn cho sản phẩm ' . $longName,
                    'content' => 'Nội dung chi tiết cho sản phẩm ' . $longName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('product_catalogue_product')->insert([
                    'product_id' => $product->id,
                    'product_catalogue_id' => $cat->id,
                ]);

                // Create 2 variants per product
                for ($v = 1; $v <= 2; $v++) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'uuid' => Str::uuid(),
                        'code' => $product->code . '-V' . $v,
                        'quantity' => rand(5, 50),
                        'price' => $product->price + (rand(-5, 5) * 100000),
                        'publish' => 2,
                        'user_id' => 1,
                    ]);

                    DB::table('product_variant_language')->insert([
                        'product_variant_id' => $variant->id,
                        'language_id' => 1,
                        'name' => 'Phiên bản ' . ($v == 1 ? 'Màu Đen - Ram 16GB - SSD 512GB (Chính hãng)' : 'Màu Trắng Bạc - Ram 32GB - SSD 1TB (Cao cấp)'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Create 2 Promotions
        for ($p = 1; $p <= 2; $p++) {
            $discountValue = rand(10, 30);
            $promo = Promotion::create([
                'name' => ($p == 1 ? 'CHƯƠNG TRÌNH KHUYẾN MÃI LỚN NHẤT NĂM' : 'Ưu đãi Tech ' . $p),
                'method' => 'product_and_variant',
                'code' => 'TECH' . $p,
                'discountValue' => $discountValue,
                'discountType' => 'percent',
                'publish' => 2,
                'is_primary' => ($p == 1 ? 1 : 0),
                'startDate' => now()->subDays(5),
                'endDate' => now()->addDays(30),
                'neverEndDate' => 'accept',
                'discountInformation' => [
                    'info' => [
                        'model' => 'Product',
                    ]
                ]
            ]);

            // Assign to 8 random products for the primary one, 3 for the other
            $assignCount = ($p == 1 ? 8 : 3);
            $randomProducts = Product::inRandomOrder()->take($assignCount)->get();
            foreach ($randomProducts as $rp) {
                DB::table('promotion_product_variant')->insert([
                    'promotion_id' => $promo->id,
                    'product_id' => $rp->id,
                    'variant_uuid' => '',
                    'model' => 'Product',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
