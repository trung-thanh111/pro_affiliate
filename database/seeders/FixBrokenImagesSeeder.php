<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class FixBrokenImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $updates = [
            'Tủ lạnh Samsung Bespoke: Khi đồ gia dụng trở thành tác phẩm nghệ thuật' => 'userfiles/image/review/samsung-bespoke.png',
            'Xe đẩy em bé Combi Sugocal Switch: Sự kết hợp hoàn hảo giữa êm ái và linh hoạt' => 'userfiles/image/review/combi-sugocal.png',
            'Robot hút bụi Roborock S8 Pro Ultra: Tự động hóa hoàn toàn việc dọn dẹp?' => 'userfiles/image/review/roborock-s8.png',
        ];

        foreach ($updates as $name => $imagePath) {
            // Find post by name in post_language table
            $postId = DB::table('post_language')
                ->where('name', 'LIKE', '%' . $name . '%')
                ->value('post_id');

            if ($postId) {
                Post::where('id', $postId)->update(['image' => $imagePath]);
                $this->command->info("✅ Đã cập nhật ảnh cho: {$name}");
            } else {
                $this->command->warn("⚠️ Không tìm thấy bài viết: {$name}");
            }
        }
    }
}
