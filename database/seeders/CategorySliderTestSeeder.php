<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCatalogue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySliderTestSeeder extends Seeder
{
    public function run(): void
    {
        $startRgt = ProductCatalogue::max('rgt') ?? 0;
        
        $categories = [
            'Máy Tính Bàn', 'Linh Kiện PC', 'Màn Hình Máy Tính', 'Bàn Phím Cơ', 'Chuột Gaming',
            'Tai Nghe Bluetooth', 'Loa Thông Minh', 'Camera Giám Sát', 'Ổ Cắm Thông Minh', 'Đèn Thông Minh',
            'Đồng Hồ Thông Minh', 'Máy Tính Bảng', 'Máy Ảnh & Quay Phim', 'Phụ Kiện Máy Ảnh', 'Thiết Bị Mạng',
            'Máy In & Scan', 'Máy Chiếu & Phụ Kiện', 'Phần Mềm Bản Quyền', 'Ghế Gaming', 'Bàn Chơi Game'
        ];

        foreach ($categories as $index => $name) {
            $canonical = Str::slug($name) . '-' . $index . '-' . rand(100, 999);
            $image = 'https://picsum.photos/200/200?random=' . ($index + 200);
            
            $lft = $startRgt + ($index * 2) + 1;
            $rgt = $startRgt + ($index * 2) + 2;

            $cat = ProductCatalogue::create([
                'publish' => 2,
                'image' => $image,
                'user_id' => 1,
                'level' => 0,
                'lft' => $lft,
                'rgt' => $rgt,
            ]);

            DB::table('product_catalogue_language')->insert([
                'product_catalogue_id' => $cat->id,
                'language_id' => 1,
                'name' => $name,
                'canonical' => $canonical,
                'description' => 'Mô tả cho ' . $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('routers')->insert([
                'canonical' => $canonical,
                'module_id' => $cat->id,
                'controllers' => 'App\Http\Controllers\Frontend\ProductCatalogueController',
                'language_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
