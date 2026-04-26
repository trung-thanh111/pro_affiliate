<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCatalogue;
use Illuminate\Support\Facades\DB;

class FixBrokenCategoryImagesSeeder extends Seeder
{
    public function run(): void
    {
        $imageMap = [
            'Bàn phím cơ' => 'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&w=600&q=80',
            'Tai nghe Bluetooth' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=600&q=80',
            'Gia dụng' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=600&q=80',
            'Máy giặt' => 'https://images.unsplash.com/photo-1545173168-9f1947eebb7f?auto=format&fit=crop&w=600&q=80',
            'Mẹ và Bé' => 'https://images.unsplash.com/photo-1515488442803-997c51e39bc4?auto=format&fit=crop&w=600&q=80',
            'Đồng hồ' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?auto=format&fit=crop&w=600&q=80',
            'Giày dép' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=600&q=80',
            'Chăm sóc thú cưng' => 'https://images.unsplash.com/photo-1516734212186-a967f81ad0d7?auto=format&fit=crop&w=600&q=80',
            'Nhạc cụ' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=600&q=80',
            'Thực phẩm' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?auto=format&fit=crop&w=600&q=80',
            'Nội thất' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=600&q=80',
            'Tivi' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?auto=format&fit=crop&w=600&q=80',
            'Gaming Gears' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=600&q=80',
            'Voucher' => 'https://images.unsplash.com/photo-1589156280159-27698a70f29e?auto=format&fit=crop&w=600&q=80',
            'Sim thẻ' => 'https://images.unsplash.com/photo-1556742044-3c52d6e88c62?auto=format&fit=crop&w=600&q=80',
            'Du lịch' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=600&q=80',
            'Ô tô & Xe máy' => 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?auto=format&fit=crop&w=600&q=80',
            'Laptop' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=600&q=80',
        ];

        foreach ($imageMap as $name => $image) {
            $catId = DB::table('product_catalogue_language')
                ->where('name', 'like', '%' . $name . '%')
                ->where('language_id', 1)
                ->value('product_catalogue_id');

            if ($catId) {
                ProductCatalogue::where('id', $catId)->update(['image' => $image]);
                echo "Updated image for: {$name} (ID: {$catId})\n";
            } else {
                echo "Could not find category: {$name}\n";
            }
        }
        
        // Also fix any other categories that might be using the placeholder image or have no image
        $catsToFix = ProductCatalogue::where('image', 'like', '%placeholder%')
            ->orWhereNull('image')
            ->orWhere('image', '')
            ->get();
            
        foreach($catsToFix as $cat) {
            $cat->image = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=600&q=80'; // Watch/Product generic
            $cat->save();
            echo "Fixed generic placeholder for ID: {$cat->id}\n";
        }
    }
}
