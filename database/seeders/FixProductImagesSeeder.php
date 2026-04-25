<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCatalogue;
use Illuminate\Support\Facades\DB;

class FixProductImagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info("🚀 Đang tiến hành seeding lại toàn bộ 165+ ảnh sản phẩm...");

        $imagePool = [
            'phone' => [
                '1511707171634-5f897ff02aa9', '1512941937669-90a1b58e7e9c', '1601784551446-20c9e07cdbdb', 
                '1592890288564-76628a30a657', '1510557880182-3d4d3cba35a5'
            ],
            'laptop' => [
                '1496181133206-80ce9b88a853', '1517336714731-489689fd1ca8', '1593642632823-8f785ba67e45', 
                '1499951360447-b19be8fe80f5', '1488590526976-3816174301e6'
            ],
            'keyboard' => [
                '1511467687858-23d96c32e4ae', '1595043533440-4b3343413630', '1618384881928-df3cc82aaef1',
                '1541140134513-85a161dcbb21', '1560869713-7d0a29430803'
            ],
            'mouse' => [
                '1527443224154-c4a3942d3acf', '1586776977607-310e9c725c37', '1615663245857-ac93bb7c39e7',
                '1617050318658-c9c22b130773'
            ],
            'audio' => [
                '1505740420928-5e560c06d30e', '1546435770-a3e4265da3ec', '1583394838336-acd3777a8880',
                '1484704849700-10358f4d670c', '1506244856747-f3bf5f89a9cd'
            ],
            'camera' => [
                '1516035069371-29a1b244cc32', '1510127844038-537a89c391f3', '1502920917128-1aa500764cbd',
                '1452784447466-7fe3ad13d9e2'
            ],
            'watch' => [
                '1523275335684-37898b6baf30', '1508685096489-7aacd44bd3b3', '1544117518-2b462f558b6d',
                '1517502884402-40bbde1f27b7'
            ],
            'fashion' => [
                '1490114538077-0a7f8cb49891', '1483985988355-763728e1935b', '1525507119028-ed4c629a60a3', 
                '1556906781-9a412961c28c', '1515886657611-09f04065a1d4', '1539106395181-df8b2ed4c222'
            ],
            'shoes' => [
                '1542291026-7eec264c27ff', '1560769629-975ec94e6a86', '1525966222319-74d1520679e0',
                '1491510899347-be47045b4103'
            ],
            'baby' => [
                '1522771739844-6a9f6d5f14af', '1555252333-d17e7210a400', '1515488764276-beab7607c1e6',
                '1594913785162-e6786967fcc9', '1510154264274-563d1f287660'
            ],
            'home' => [
                '1584622650111-993a426fbf0a', '1527515637462-cff94eecc1ac', '1584269600464-37b1b58a9fe7', 
                '1556911228-3e2320d098a5', '1571175432230-01c24822509e', '1583940940739-197ff1388654'
            ],
            'book' => [
                '1497633762265-9d179a990aa6', '1512820790803-83ca734da794', '1495446815901-a7297e633e8d',
                '1544947950-fa07a98d237f'
            ],
            'furniture' => [
                '1555041469-a586c61ea9bc', '1524758631624-d24240954930', '1586023492823-10243160e963'
            ],
            'food' => [
                '1506484381205-f7945653044d', '1504674900247-41589d435991', '1512621776951-a57141f2eefd'
            ],
            'pet' => [
                '1516734212186-a967f81ad0d7', '1537150535042-881270425a44', '1541364983-4a18760126a4'
            ],
            'vehicle' => [
                '1533473359331-0135ef1b58bf', '1511919884228-859e21b13283', '1519206045609-0d19658f8147'
            ],
            'instrument' => [
                '1511379938547-c1f69419868d', '1514649923238-57734814de73', '1507833423225-3b7430154901'
            ],
            'general' => [
                '1523275335684-37898b6baf30', '1505740420928-5e560c06d30e', '1496181133206-80ce9b88a853'
            ]
        ];

        $getUnsplashUrl = function($category, $index) use ($imagePool) {
            $pool = $imagePool[$category] ?? $imagePool['general'];
            $id = $pool[$index % count($pool)];
            return "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w=800&q=80";
        };

        $categoryMap = [
            'dien-thoai' => 'phone', 'laptop' => 'laptop', 'ban-phim-co' => 'keyboard', 
            'chuot-may-tinh' => 'mouse', 'tai-nghe-bluetooth' => 'audio', 'tivi' => 'home', 
            'tu-lanh' => 'home', 'may-giat' => 'home', 'gia-dung' => 'home', 'me-va-be' => 'baby', 
            'suc-khoe-sac-dep' => 'fashion', 'thoi-trang-nam' => 'fashion', 'thoi-trang-nu' => 'fashion', 
            'sach' => 'book', 'may-anh' => 'camera', 'dong-ho' => 'watch', 'giay-dep' => 'shoes', 
            'tui-xach' => 'fashion', 'do-the-thao' => 'fashion', 'phu-kien-cong-nghe' => 'keyboard', 
            'gaming-gears' => 'keyboard', 'thiet-bi-van-phong' => 'laptop', 'do-dung-hoc-sinh' => 'book', 
            'thuc-pham' => 'food', 'noi-that' => 'furniture', 'qua-tang' => 'general', 
            'dich-vu' => 'general', 'sim-the' => 'phone', 'voucher' => 'general', 
            'du-lich' => 'general', 'thu-cung' => 'pet', 'o-to-xe-may' => 'vehicle', 'nhac-cu' => 'instrument',
        ];

        $products = Product::all();
        foreach ($products as $index => $product) {
            $lang = DB::table('product_language')->where('product_id', $product->id)->first();
            $name = $lang ? $lang->name : '';

            // Priority: Local high-quality images
            if (str_contains($name, 'iPhone 15 Pro Max')) {
                $imageUrl = 'userfiles/image/product/iphone-15-pro-max.png';
            } elseif (str_contains($name, 'MSI Modern')) {
                $imageUrl = 'userfiles/image/product/msi-modern-laptop.png';
            } elseif (str_contains($name, 'Logitech G413')) {
                $imageUrl = 'userfiles/image/product/logitech-g413.png';
            } else {
                $catId = $product->product_catalogue_id;
                $catCanonical = DB::table('product_catalogue_language')
                    ->where('product_catalogue_id', $catId)
                    ->value('canonical');
                
                $categoryKey = $categoryMap[$catCanonical] ?? 'general';
                $imageUrl = $getUnsplashUrl($categoryKey, $index);
            }

            $product->update(['image' => $imageUrl]);
        }

        // Cập nhật Catalogues
        $catalogues = DB::table('product_catalogue_language')->get();
        foreach ($catalogues as $index => $cat) {
            $categoryKey = $categoryMap[$cat->canonical] ?? 'general';
            $imageUrl = $getUnsplashUrl($categoryKey, $index + 50); // Offset to avoid duplicate with products
            DB::table('product_catalogues')->where('id', $cat->product_catalogue_id)->update(['image' => $imageUrl]);
        }

        $this->command->info("🎉 Xong! Đã cập nhật lại toàn bộ link sống.");
    }
}
