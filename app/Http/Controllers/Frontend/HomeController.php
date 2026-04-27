<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\Core\SystemRepository;
use App\Services\V1\Core\SlideService;
use App\Enums\SlideEnum;
use App\Services\V1\Core\WidgetService;
use App\Services\V1\Post\PostService;
use App\Repositories\Post\PostCatalogueRepository;
use Illuminate\Http\Request;

class HomeController extends FrontendController
{
    protected $systemRepository;
    protected $slideService;
    protected $widgetService;
    protected $postService;
    protected $postCatalogueRepository;
    protected $scholarService;

    public function __construct(
        SlideService $slideService,
        SystemRepository $systemRepository,
        WidgetService $widgetService,
        PostService $postService,
        PostCatalogueRepository $postCatalogueRepository,
    ) {
        $this->slideService = $slideService;
        $this->systemRepository = $systemRepository;
        $this->widgetService = $widgetService;
        $this->postService = $postService;
        $this->postCatalogueRepository = $postCatalogueRepository;

        parent::__construct(
            $systemRepository,
        );
    }


    public function index()
    {
        $config = $this->config();

        $slides = $this->slideService->getSlide(
            [SlideEnum::MAIN, SlideEnum::TECHSTAFF, SlideEnum::PARTNER, SlideEnum::MIDDLEHOME],
            $this->language
        );

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'intro'],
            ['keyword' => 'bring'],
            ['keyword' => 'p-hl'],
            ['keyword' => 'category', 'children' => true],
            ['keyword' => 'feedback', 'object' => true],
            ['keyword' => 'news', 'object' => true, 'limit' => 12],
            ['keyword' => 'value', 'object' => true],
            ['keyword' => 'ship'],
        ], $this->language);


        $bestSellers = $this->widgetService->getBestSellers($this->language, 6);
        $promotionProducts = $this->widgetService->getPromotionProducts($this->language, 12);
        $categoryWithProducts = $this->widgetService->getCategoryWithProducts($this->language, 12, 1); // Lấy 1 danh mục tiêu biểu
        $categories = $widgets['category'] ?? null;

        $system = $this->system;
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];
        $schema = $this->schema($seo);

        $latestPosts = $this->postService->findPosts([
            ['publish', '=', 2],
            ['recommend', '=', 2]
        ], $this->language, ['id', 'DESC'], 12);

        $reviewPosts = $this->postService->findPosts([
            ['publish', '=', 2],
            ['is_review', '=', 1]
        ], $this->language, ['id', 'DESC'], 4);

        $featuredReviews = $this->postService->findPosts([
            ['publish', '=', 2],
            ['recommend', '=', 2]
        ], $this->language, ['id', 'DESC'], 5);

        $postCatalogues = $this->postCatalogueRepository->findByCondition([
            ['publish', '=', 2]
        ], true, [
            'languages' => function($query) {
                $query->where('language_id', $this->language);
            }
        ], ['lft', 'ASC']);

        $template = 'frontend.homepage.home.index';
        return view($template, compact(
            'config',
            'slides',
            'seo',
            'system',
            'schema',
            'widgets',
            'bestSellers',
            'promotionProducts',
            'categories',
            'categoryWithProducts',
            'latestPosts',
            'reviewPosts',
            'featuredReviews',
            'postCatalogues'
        ));
    }

    /**
     * @param array $seo
     * @return string
     */
    public function schema(array $seo = []): string
    {
        $schema = "<script type='application/ld+json'>
                {
                    \"@context\": \"https://schema.org\",
                    \"@type\": \"WebSite\",
                    \"name\": \"" . ($seo['meta_title'] ?? '') . "\",
                    \"url\": \"" . ($seo['canonical'] ?? '') . "\",
                    \"description\": \"" . ($seo['meta_description'] ?? '') . "\",
                    \"publisher\": {
                        \"@type\": \"Organization\",
                        \"name\": \"" . ($seo['meta_title'] ?? '') . "\"
                    },
                    \"potentialAction\": {
                        \"@type\": \"SearchAction\",
                        \"target\": {
                            \"@type\": \"EntryPoint\",
                            \"urlTemplate\": \"" . ($seo['canonical'] ?? '') . "search?q={search_term_string}\"
                        },
                        \"query-input\": \"required name=search_term_string\"
                    }
                }
            </script>";

        return $schema;
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [
                '__frontend/resources/style.css'
            ],
            'js' => []
        ];
    }
}
