<?php

namespace App\Services\V1\Post;

use Illuminate\Support\Facades\DB;
use App\Models\PostProduct;
use App\Models\ComparisonSection;

class CompareService
{
    /**
     * Save or update comparison data for a post.
     * Strategy: Delete existing data and re-insert for simplicity and data integrity.
     */
    public function save($post, $request)
    {
        // Only process if the post is a comparison type
        // Note: You can add a check for post_type or a specific field if needed
        if ($request->input('post_type') !== 'compare') {
            return;
        }

        return DB::transaction(function () use ($post, $request) {
            // 1. Clean up old data
            // Cascade delete will handle rows and cells
            $post->post_products()->delete();
            $post->comparison_sections()->delete();

            // 2. Save Comparison Products (Columns)
            $productMap = []; // Maps temp_id or product_id to the new PostProduct ID
            $compareProducts = $request->input('compare_products', []);
            
            if (!empty($compareProducts)) {
                foreach ($compareProducts as $index => $item) {
                    $newPP = $post->post_products()->create([
                        'product_id' => $item['product_id'],
                        'sort_order' => $index,
                        'column_title' => $item['column_title'] ?? null,
                        'column_subtitle' => $item['column_subtitle'] ?? null,
                        'column_image' => $item['column_image'] ?? null,
                        'cta_text' => $item['cta_text'] ?? null,
                        'cta_url' => $item['cta_url'] ?? null,
                        'badge_text' => $item['badge_text'] ?? null,
                        'is_highlight' => isset($item['is_highlight']) && ($item['is_highlight'] == 1 || $item['is_highlight'] == 'on'),
                    ]);
                    
                    // We use the product_id as the key for mapping cells
                    $productMap[$item['product_id']] = $newPP->id;
                }
            }

            // 3. Save Sections -> Rows -> Cells
            $compareSections = $request->input('compare_sections', []);
            if (!empty($compareSections)) {
                foreach ($compareSections as $sIndex => $sData) {
                    $section = $post->comparison_sections()->create([
                        'title' => $sData['title'],
                        'description' => $sData['description'] ?? null,
                        'sort_order' => $sIndex,
                    ]);

                    $rows = $sData['rows'] ?? [];
                    foreach ($rows as $rIndex => $rData) {
                        $row = $section->rows()->create([
                            'label' => $rData['label'],
                            'row_type' => $rData['row_type'] ?? 'text',
                            'unit' => $rData['unit'] ?? null,
                            'tooltip' => $rData['tooltip'] ?? null,
                            'sort_order' => $rIndex,
                            'is_featured' => isset($rData['is_featured']) && ($rData['is_featured'] == 1 || $rData['is_featured'] == 'on'),
                        ]);

                        // Cells for each product in this row
                        $cells = $rData['cells'] ?? [];
                        foreach ($cells as $productId => $value) {
                            if (isset($productMap[$productId])) {
                                $row->cells()->create([
                                    'post_product_id' => $productMap[$productId],
                                    'value_text' => is_array($value) ? null : $value,
                                    'value_html' => null, // Reserved for rich content
                                    'value_type' => 'text',
                                ]);
                            }
                        }
                    }
                }
            }
        });
    }
}
