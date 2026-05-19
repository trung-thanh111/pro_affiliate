<?php

namespace Tests\Unit;

use Tests\TestCase;

class ArticleSeoTest extends TestCase
{
    /**
     * Case 1: Bài viết có đủ seo_title, seo_description, seo_image (meta_title, meta_description, etc.)
     * => Meta phải lấy đủ từ SEO custom.
     */
    public function test_case_1_full_seo_custom(): void
    {
        $post = (object)[
            'meta_title' => 'Custom SEO Title',
            'meta_description' => 'Custom SEO Description',
            'image' => 'images/custom-seo.jpg',
            'canonical' => 'bai-viet-seo-custom',
            'name' => 'Original Post Title',
            'description' => 'Original Post Short Description',
            'content' => '<p>Original Post Content</p>',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertEquals('Custom SEO Title', $seo->title);
        $this->assertEquals('Custom SEO Description', $seo->description);
        $this->assertStringContainsString('images/custom-seo.jpg', $seo->image);
        $this->assertStringContainsString('bai-viet-seo-custom', $seo->url);
    }

    /**
     * Case 2: Bài viết không có seo_title nhưng có title
     * => fallback về article.title (model.name).
     */
    public function test_case_2_fallback_title(): void
    {
        $post = (object)[
            'meta_title' => '',
            'meta_description' => 'Custom SEO Description',
            'image' => 'images/custom-seo.jpg',
            'canonical' => 'bai-viet-seo-custom',
            'name' => 'Original Post Title',
            'description' => 'Original Post Short Description',
            'content' => '<p>Original Post Content</p>',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertEquals('Original Post Title', $seo->title);
    }

    /**
     * Case 3: Bài viết không có seo_description nhưng có description
     * => description phải fallback về article.description.
     */
    public function test_case_3_fallback_description(): void
    {
        $post = (object)[
            'meta_title' => 'Custom SEO Title',
            'meta_description' => '',
            'image' => 'images/custom-seo.jpg',
            'canonical' => 'bai-viet-seo-custom',
            'name' => 'Original Post Title',
            'description' => 'Original Post Short Description',
            'content' => '<p>Original Post Content</p>',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertEquals('Original Post Short Description', $seo->description);
    }

    /**
     * Case 4: Bài viết không có seo_description và description nhưng có content
     * => description phải fallback excerpt từ content, strip HTML.
     */
    public function test_case_4_fallback_content(): void
    {
        $post = (object)[
            'meta_title' => 'Custom SEO Title',
            'meta_description' => '',
            'image' => 'images/custom-seo.jpg',
            'canonical' => 'bai-viet-seo-custom',
            'name' => 'Original Post Title',
            'description' => '',
            'content' => '<p>Đây là nội dung đầy đủ của bài viết dùng để test xem hệ thống có tự động cắt và chuẩn hóa sang dạng text thường không.</p>',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertStringNotContainsString('<p>', $seo->description);
        $this->assertStringContainsString('Đây là nội dung đầy đủ của bài viết', $seo->description);
    }

    /**
     * Case 5: Bài viết không có seo_image nhưng có thumbnail/image
     * => fallback về model.image/thumbnail.
     */
    public function test_case_5_fallback_image(): void
    {
        $post = (object)[
            'meta_title' => 'Custom SEO Title',
            'meta_description' => 'Custom SEO Description',
            'image' => 'images/thumbnail.png',
            'canonical' => 'bai-viet-seo-custom',
            'name' => 'Original Post Title',
            'description' => '',
            'content' => '',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertStringContainsString('images/thumbnail.png', $seo->image);
    }

    /**
     * Case 6: Bài viết không có bất kỳ image nào
     * => image phải fallback về default sharing image.
     */
    public function test_case_6_fallback_default_image(): void
    {
        $post = (object)[
            'meta_title' => 'Custom SEO Title',
            'meta_description' => 'Custom SEO Description',
            'image' => '',
            'canonical' => 'bai-viet-seo-custom',
            'name' => 'Original Post Title',
            'description' => '',
            'content' => '',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertStringContainsString('images/default-share.jpg', $seo->image);
    }

    /**
     * Case 7: Image là relative path
     * => phải convert thành absolute URL.
     */
    public function test_case_7_absolute_url_conversion(): void
    {
        $post = (object)[
            'meta_title' => 'Custom SEO Title',
            'meta_description' => 'Custom SEO Description',
            'image' => '/uploads/images/photo.jpg',
            'canonical' => 'bai-viet-seo-custom',
            'name' => 'Original Post Title',
            'description' => '',
            'content' => '',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertStringStartsWith('http', $seo->image);
        $this->assertStringContainsString('/uploads/images/photo.jpg', $seo->image);
    }

    /**
     * Case 8: SEO field là empty string
     * => phải xem như null và fallback đúng.
     */
    public function test_case_8_empty_string_handling(): void
    {
        $post = (object)[
            'meta_title' => '   ', // whitespace
            'meta_description' => '   ',
            'image' => '   ',
            'canonical' => 'bai-viet-test-empty',
            'name' => 'Real Title',
            'description' => 'Real Description',
            'content' => '',
            'created_at' => null,
            'updated_at' => null,
            'user_id' => null
        ];

        $system = [
            'seo_meta_title' => 'Default Site Title',
            'seo_meta_description' => 'Default Site Description',
            'seo_meta_image' => 'images/default-share.jpg',
            'homepage_company' => 'My Affiliate Site'
        ];

        $seo = resolveArticleSeo($post, $system);

        $this->assertEquals('Real Title', $seo->title);
        $this->assertEquals('Real Description', $seo->description);
        $this->assertStringContainsString('images/default-share.jpg', $seo->image);
    }
}
