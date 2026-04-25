<?php
namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\V1\Product\ProductCatalogueService;

class CategorySliderComposer
{
    protected $productCatalogueService;
    protected $language;
    protected static $categories = null;

    public function __construct(
        ProductCatalogueService $productCatalogueService,
        $language
    ) {
        $this->productCatalogueService = $productCatalogueService;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        if (static::$categories === null) {
            static::$categories = $this->productCatalogueService->getCategorySlider($this->language);
        }
        $view->with('categories', static::$categories);
    }
}
