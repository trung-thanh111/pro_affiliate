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
            ['name' => 'Điện thoại', 'canonical' => 'dien-thoai', 'image' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=200&h=200&fit=crop'],
            ['name' => 'Laptop', 'canonical' => 'laptop', 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=200&h=200&fit=crop'],
            ['name' => 'Bàn phím cơ', 'canonical' => 'ban-phim-co', 'image' => 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?w=200&h=200&fit=crop'],
            ['name' => 'Chuột máy tính', 'canonical' => 'chuot-may-tinh', 'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=200&h=200&fit=crop'],
            ['name' => 'Tai nghe Bluetooth', 'canonical' => 'tai-nghe-bluetooth', 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop'],
            ['name' => 'Tivi', 'canonical' => 'tivi', 'image' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=200&h=200&fit=crop'],
            ['name' => 'Tủ lạnh', 'canonical' => 'tu-lanh', 'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=200&h=200&fit=crop'],
            ['name' => 'Máy giặt', 'canonical' => 'may-giat', 'image' => 'https://images.unsplash.com/photo-1610557892470-55d9e80c0bce?w=200&h=200&fit=crop'],
            ['name' => 'Gia dụng', 'canonical' => 'gia-dung', 'image' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=200&h=200&fit=crop'],
            ['name' => 'Mẹ và Bé', 'canonical' => 'me-va-be', 'image' => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=200&h=200&fit=crop'],
            ['name' => 'Sức khỏe & Sắc đẹp', 'canonical' => 'suc-khoe-sac-dep', 'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=200&h=200&fit=crop'],
            ['name' => 'Thời trang Nam', 'canonical' => 'thoi-trang-nam', 'image' => 'https://images.unsplash.com/photo-1490114538077-0a7f8cb49891?w=200&h=200&fit=crop'],
            ['name' => 'Thời trang Nữ', 'canonical' => 'thoi-trang-nu', 'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=200&h=200&fit=crop'],
            ['name' => 'Sách', 'canonical' => 'sach', 'image' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=200&h=200&fit=crop'],
            ['name' => 'Máy ảnh', 'canonical' => 'may-anh', 'image' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=200&h=200&fit=crop'],
            ['name' => 'Đồng hồ', 'canonical' => 'dong-ho', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=200&h=200&fit=crop'],
            ['name' => 'Giày dép', 'canonical' => 'giay-dep', 'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=200&h=200&fit=crop'],
            ['name' => 'Túi xách', 'canonical' => 'tui-xach', 'image' => 'https://images.unsplash.com/photo-1548036627-cb955d43d15d?w=200&h=200&fit=crop'],
            ['name' => 'Đồ thể thao', 'canonical' => 'do-the-thao', 'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=200&h=200&fit=crop'],
            ['name' => 'Phụ kiện công nghệ', 'canonical' => 'phu-kien-cong-nghe', 'image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=200&h=200&fit=crop'],
            ['name' => 'Gaming Gears', 'canonical' => 'gaming-gears', 'image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=200&h=200&fit=crop'],
            ['name' => 'Thiết bị văn phòng', 'canonical' => 'thiet-bi-van-phong', 'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=200&h=200&fit=crop'],
            ['name' => 'Đồ dùng học sinh', 'canonical' => 'do-dung-hoc-sinh', 'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=200&h=200&fit=crop'],
            ['name' => 'Thực phẩm', 'canonical' => 'thuc-pham', 'image' => 'https://images.unsplash.com/photo-1506484381205-f7945653044d?w=200&h=200&fit=crop'],
            ['name' => 'Nội thất', 'canonical' => 'noi-that', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=200&h=200&fit=crop'],
            ['name' => 'Quà tặng', 'canonical' => 'qua-tang', 'image' => 'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=200&h=200&fit=crop'],
            ['name' => 'Dịch vụ', 'canonical' => 'dich-vu', 'image' => 'https://images.unsplash.com/photo-1521791136364-798a7bc0d26d?w=200&h=200&fit=crop'],
            ['name' => 'Sim thẻ', 'canonical' => 'sim-the', 'image' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=200&h=200&fit=crop'],
            ['name' => 'Voucher', 'canonical' => 'voucher', 'image' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=200&h=200&fit=crop'],
            ['name' => 'Du lịch', 'canonical' => 'du-lich', 'image' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=200&h=200&fit=crop'],
            ['name' => 'Chăm sóc thú cưng', 'canonical' => 'thu-cung', 'image' => 'https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?w=200&h=200&fit=crop'],
            ['name' => 'Ô tô & Xe máy', 'canonical' => 'o-to-xe-may', 'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=200&h=200&fit=crop'],
            ['name' => 'Nhạc cụ', 'canonical' => 'nhac-cu', 'image' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=200&h=200&fit=crop'],
        ];

        $liveLinks = [
            'https://shopee.vn/B%C3%A0n-ph%C3%ADm-gi%E1%BA%A3-c%C6%A1-XUNFOX-K82-K820-%E2%80%93-94-ph%C3%ADm-d%C3%B9ng-ch%C6%A1i-game-k%C3%A8m-n%C3%BAt-v%E1%BA%B7n-%C3%A2m-l%C6%B0%E1%BB%A3ng-ti%E1%BB%87n-d%E1%BB%A5ng-i.136096670.22274294107',
            'https://shopee.vn/%C3%81o-Kho%C3%A1c-S%C6%A1-Mi-H%E1%BB%8Da-Ti%E1%BA%BFt-Retro-Square-Unisex-M%E1%BA%ABu-M%E1%BB%9Bi-2026-i.1804520656.56509312255',
            'https://shopee.vn/-M%E1%BB%9Bi-%C4%90i%E1%BB%87n-Tho%E1%BA%A1i-Meizu-Mblu-22-l-UNISOC-SC9863A-l-M%C3%A0n-H%C3%ACnh-6.79-90Hz-l-Pin-5000mAh-Ch%C3%ADnh-h%C3%A3ng-i.191065034.44805830442'
        ];

        $productData = [
            'dien-thoai' => [
                ['name' => 'iPhone 15 Pro Max 256GB - Chính hãng Apple VN/A', 'price' => 34990000, 'discount' => 29490000, 'source' => 'Shopee'],
                ['name' => 'Samsung Galaxy S24 Ultra 5G 12GB/256GB - AI Phone', 'price' => 33990000, 'discount' => 27990000, 'source' => 'Shopee'],
                ['name' => 'Xiaomi 14 Ultra 5G (16GB/512GB) - Camera Leica', 'price' => 29990000, 'discount' => 25990000, 'source' => 'Shopee'],
                ['name' => 'OPPO Find N3 Flip 12GB/256GB - Điện thoại gập', 'price' => 22990000, 'discount' => 19990000, 'source' => 'Shopee'],
                ['name' => 'Honor X9d 5G (8GB/256GB) - Pin 8300mAh Siêu Khủng', 'price' => 10490000, 'discount' => 9490000, 'source' => 'Shopee'],
            ],
            'laptop' => [
                ['name' => 'Laptop MSI Modern 14 F13MG-466VN Core i5-1334U', 'price' => 16790000, 'discount' => 14990000, 'source' => 'Shopee'],
                ['name' => 'Laptop Acer Aspire 7 A715-59G-73LB Core i7-12650H', 'price' => 18490000, 'discount' => 16490000, 'source' => 'Shopee'],
                ['name' => 'Laptop HP 15S-FQ5161TU i5-1235U 8GB/512GB Win11', 'price' => 15690000, 'discount' => 13690000, 'source' => 'Shopee'],
                ['name' => 'Laptop Asus Vivobook Go 14 E1404FA-NK177W R5-7520U', 'price' => 11490000, 'discount' => 9990000, 'source' => 'Shopee'],
                ['name' => 'Laptop HP 15S-FQ5146TU Core i7-1255U 8GB/512GB', 'price' => 18990000, 'discount' => 16990000, 'source' => 'Shopee'],
            ],
            'ban-phim-co' => [
                ['name' => 'Bàn phím cơ DAREU EK87 V2 Gray Black Multi-Led PBT', 'price' => 499000, 'discount' => 429000, 'source' => 'Shopee'],
                ['name' => 'Bàn phím cơ Gaming DAREU EK87L V2 Dream/Firefly', 'price' => 399000, 'discount' => 349000, 'source' => 'Shopee'],
                ['name' => 'Bàn phím cơ gaming Logitech G413 TKL SE Keycap PBT', 'price' => 1250000, 'discount' => 1050000, 'source' => 'Shopee'],
                ['name' => 'Bàn phím cơ Logitech G413 SE TKL (Tactile Switch)', 'price' => 1250000, 'discount' => 1050000, 'source' => 'Shopee'],
                ['name' => 'Bàn phím cơ Akko 3087 v2 DS Horizon (Akko sw v2)', 'price' => 1250000, 'discount' => 1090000, 'source' => 'Shopee'],
            ]
        ];

        $globalProductIndex = 0;

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

            DB::table('routers')->insert([
                'canonical' => $catData['canonical'],
                'module_id' => $cat->id,
                'controllers' => 'App\Http\Controllers\Frontend\ProductCatalogueController',
                'language_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $productsToSeed = $productData[$catData['canonical']] ?? [];

            // Fill up to 5 products if not enough real data
            while (count($productsToSeed) < 5) {
                $i = count($productsToSeed) + 1;
                $pName = '[Mới 2026] ' . $catData['name'] . ' Cao Cấp ' . $i . ' - Chính Hãng';
                $price = rand(2, 20) * 100000;
                $discount = $price * 0.8;
                $productsToSeed[] = [
                    'name' => $pName,
                    'price' => $price,
                    'discount' => $discount,
                    'source' => 'Shopee'
                ];
            }

            foreach ($productsToSeed as $i => $p) {
                $pCanonical = Str::slug($p['name']) . '-' . time() . rand(10, 99);
                $link = $liveLinks[$globalProductIndex % 3];
                $globalProductIndex++;

                $album = [
                    'https://picsum.photos/600/600?random=' . rand(100, 999),
                    'https://picsum.photos/600/600?random=' . rand(100, 999),
                    'https://picsum.photos/600/600?random=' . rand(100, 999),
                ];

                $hasDiscount = (rand(1, 100) <= 50);
                $product = Product::create([
                    'product_catalogue_id' => $cat->id,
                    'image' => 'https://picsum.photos/600/600?random=' . ($cat->id * 100 + $i),
                    'album' => json_encode($album),
                    'publish' => 2,
                    'price' => $p['price'],
                    'price_discount' => $hasDiscount ? ($p['discount'] ?? $p['price'] * 0.8) : 0,
                    'stock' => rand(20, 200),
                    'sold' => rand(100, 9999),
                    'code' => 'SKU-' . strtoupper(Str::random(6)),
                    'user_id' => 1,
                    'link' => $link,
                    'source' => $p['source'],
                ]);

                DB::table('product_language')->insert([
                    'product_id' => $product->id,
                    'language_id' => 1,
                    'name' => $p['name'],
                    'canonical' => $pCanonical,
                    'description' => 'Sản phẩm ' . $p['name'] . ' chất lượng cao, giá rẻ nhất thị trường.',
                    'content' => '<p>Mô tả chi tiết sản phẩm ' . $p['name'] . ' với các tính năng mới nhất 2026.</p>',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('routers')->insert([
                    'canonical' => $pCanonical,
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
    }
}
