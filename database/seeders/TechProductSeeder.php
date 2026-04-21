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
        DB::table('routers')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['name' => 'Laptop & Gaming', 'canonical' => 'laptop-gaming', 'image' => 'https://res.cloudinary.com/drt6v9atp/image/upload/v1713697000/categories/laptop.png'],
            ['name' => 'Điện Thoại & Phụ Kiện', 'canonical' => 'smartphone-accessory', 'image' => 'https://res.cloudinary.com/drt6v9atp/image/upload/v1713697000/categories/smartphone.png'],
            ['name' => 'Âm Thanh & Giải Trí', 'canonical' => 'audio-entertainment', 'image' => 'https://res.cloudinary.com/drt6v9atp/image/upload/v1713697000/categories/audio.png'],
            ['name' => 'Nhà Thông Minh', 'canonical' => 'smart-home', 'image' => 'https://res.cloudinary.com/drt6v9atp/image/upload/v1713697000/categories/home.png'],
            ['name' => 'Thời Trang Công Nghệ', 'canonical' => 'tech-fashion', 'image' => 'https://res.cloudinary.com/drt6v9atp/image/upload/v1713697000/categories/fashion.png'],
        ];

        $techSpecs = [
            'mạnh mẽ hiệu năng cao vượt trội',
            'thiết kế sang trọng tinh tế đẳng cấp',
            'màn hình siêu nét chuẩn màu điện ảnh 4K OLED',
            'pin bền bỉ cả ngày dài sử dụng liên tục',
            'hỗ trợ sạc nhanh siêu tốc 120W an toàn',
            'bảo hành chính hãng 24 tháng tận nơi trên toàn quốc',
            'tặng kèm bộ quà tặng trị giá 2 triệu đồng cực khủng',
            'phiên bản giới hạn đặc biệt duy nhất tại Việt Nam',
            'nhập khẩu nguyên chiếc từ thị trường quốc tế',
            'công nghệ tiên tiến nhất năm 2026 xu hướng mới',
            'độ bền đạt chuẩn quân đội MIL-STD 810H chống va đập',
            'kết nối không dây Wi-Fi 7 ổn định mượt mà',
            'âm thanh sống động chân thực chuẩn Hi-Res Audio',
            'camera độ phân giải 200MP cực cao lấy nét theo pha',
            'hệ điều hành tối ưu hóa mượt mà nhất hiện nay'
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
                'description' => 'Danh mục ' . $catData['name'] . ' chất lượng cao với nhiều ưu đãi hấp dẫn từ đối tác.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('routers')->insert([
                'canonical' => $catData['canonical'],
                'module_id' => $cat->id,
                'controllers' => 'App\Http\Controllers\Frontend\ProductCatalogueController',
                'language_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create 5 premium products per category
            for ($i = 1; $i <= 5; $i++) {
                $price = rand(2, 40) * 1000000;
                
                // Build a very long, SEO-optimized title (Shopee style)
                $randomSpecs = array_rand(array_flip($techSpecs), rand(6, 8));
                $longName = '[Mới 2026] ' . $catData['name'] . ' ' . Str::random(5) . ' ' . implode(' - ', $randomSpecs);
                $longName = Str::limit($longName, 250, '');
                
                $canonical = $catData['canonical'] . '-p-' . ($index * 10 + $i) . '-' . time();
                
                // Professional Album with 5 images
                $album = [
                    'https://picsum.photos/600/600?random=' . rand(100, 999),
                    'https://picsum.photos/600/600?random=' . rand(100, 999),
                    'https://picsum.photos/600/600?random=' . rand(100, 999),
                    'https://picsum.photos/600/600?random=' . rand(100, 999),
                ];

                $product = Product::create([
                    'product_catalogue_id' => $cat->id,
                    'image' => 'https://picsum.photos/600/600?random=' . ($cat->id * 100 + $i),
                    'album' => json_encode($album),
                    'publish' => 2,
                    'price' => $price,
                    'stock' => rand(20, 200),
                    'sold' => rand(100, 9999),
                    'code' => 'SKU-' . strtoupper(Str::random(6)),
                    'user_id' => 1,
                    'link' => 'https://shopee.vn/search?keyword=' . urlencode($longName),
                    'source' => collect(['Shopee', 'Lazada', 'Tiki', 'Amazon'])->random(),
                ]);

                DB::table('product_language')->insert([
                    'product_id' => $product->id,
                    'language_id' => 1,
                    'name' => $longName,
                    'canonical' => $canonical,
                    'description' => 'Sản phẩm ' . $longName . ' chính hãng, giá tốt nhất thị trường.',
                    'content' => '<p><strong>Thông tin chi tiết:</strong></p><ul><li>Thương hiệu uy tín hàng đầu toàn cầu</li><li>Công nghệ ' . $randomSpecs[0] . ' tiên tiến</li><li>Hiệu năng ' . $randomSpecs[1] . ' cực đỉnh</li></ul><p>Đặc biệt, khi mua ngay hôm nay bạn sẽ nhận được ưu đãi vận chuyển hỏa tốc trong 2h và bảo hành 1 đổi 1 trong 30 ngày nếu có lỗi từ nhà sản xuất. Đây là phiên bản nâng cấp hoàn hảo nhất của năm 2026 với nhiều cải tiến đáng kể về cả thiết kế lẫn tính năng bên trong.</p><img src="https://picsum.photos/800/400?random=' . $product->id . '" alt="Banner" style="width:100%; border-radius: 8px; margin: 20px 0;">',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('routers')->insert([
                    'canonical' => $canonical,
                    'module_id' => $product->id,
                    'controllers' => 'App\Http\Controllers\Frontend\ProductController',
                    'language_id' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('product_catalogue_product')->insert([
                    'product_id' => $product->id,
                    'product_catalogue_id' => $cat->id,
                ]);
            }
        }

        // Create 3 specific Promotions
        $promoData = [
            ['name' => 'FLASH SALE 25/04 - GIẢM SỐC 30%', 'val' => 30, 'type' => 'percent'],
            ['name' => 'ƯU ĐÃI ĐẶC QUYỀN VIP - GIẢM 15%', 'val' => 15, 'type' => 'percent'],
            ['name' => 'MÃ GIẢM GIÁ TRỰC TIẾP 500K', 'val' => 500000, 'type' => 'cash'],
        ];

        foreach ($promoData as $idx => $pd) {
            $promo = Promotion::create([
                'name' => $pd['name'],
                'method' => 'product_and_variant',
                'code' => 'PROMO' . ($idx + 1),
                'discountValue' => $pd['val'],
                'discountType' => $pd['type'],
                'publish' => 2,
                'is_primary' => ($idx == 0 ? 1 : 0),
                'startDate' => now()->subDays(2),
                'endDate' => now()->addDays(28),
                'neverEndDate' => 'accept',
                'discountInformation' => [
                    'info' => [
                        'model' => 'Product',
                    ]
                ]
            ]);

            // Assign to 10 random products
            $randomProducts = Product::inRandomOrder()->take(10)->get();
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
