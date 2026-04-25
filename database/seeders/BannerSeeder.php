<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run()
    {
        // Main Slides
        $mainSlides = [
            [
                'image' => 'userfiles/image/banner/tech-banner.png',
                'name' => 'Review Công Nghệ Đỉnh Cao',
                'description' => 'Cập nhật những đánh giá mới nhất về các siêu phẩm công nghệ hàng đầu thế giới.',
                'canonical' => '/danh-muc/cong-nghe',
            ],
            [
                'image' => 'userfiles/image/banner/baby-banner.png',
                'name' => 'Cẩm Nang Mẹ và Bé',
                'description' => 'Chia sẻ kinh nghiệm và đánh giá các sản phẩm tốt nhất cho bé yêu của bạn.',
                'canonical' => '/danh-muc/me-va-be',
            ],
        ];

        $mainItems = [];
        foreach ($mainSlides as $banner) {
            $mainItems[] = [
                'image' => $banner['image'],
                'name' => $banner['name'],
                'description' => $banner['description'],
                'canonical' => $banner['canonical'],
                'alt' => $banner['name'],
                'window' => '',
            ];
        }

        DB::table('slides')->updateOrInsert(
            ['keyword' => 'main-slide'],
            [
                'name' => 'Main Slider',
                'item' => json_encode(['1' => $mainItems]),
                'publish' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Side Banners
        $sideBanners = [
            [
                'image' => 'userfiles/image/banner/promo-banner.png',
                'name' => 'Giao Nhanh - Xử Lý Tức Thì',
                'description' => 'Hàng sẵn tại kho, giao nhanh siêu tốc.',
                'canonical' => '/khuyen-mai',
            ],
            [
                'image' => 'userfiles/image/banner/tech-banner.png',
                'name' => 'Nóng Tăng Nhiệt - Deal Hủy Diệt',
                'description' => 'Săn deal hời mỗi ngày.',
                'canonical' => '/khuyen-mai',
            ],
        ];

        $sideItems = [];
        foreach ($sideBanners as $banner) {
            $sideItems[] = [
                'image' => $banner['image'],
                'name' => $banner['name'],
                'description' => $banner['description'],
                'canonical' => $banner['canonical'],
                'alt' => $banner['name'],
                'window' => '',
            ];
        }

        DB::table('slides')->updateOrInsert(
            ['keyword' => 'side-banner'],
            [
                'name' => 'Side Banners',
                'item' => json_encode(['1' => $sideItems]),
                'publish' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
