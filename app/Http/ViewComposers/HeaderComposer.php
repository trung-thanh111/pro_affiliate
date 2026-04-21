<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\V1\Product\ProductCatalogueService;

class HeaderComposer
{
    protected $productCatalogueService;
    protected $language;

    public function __construct(
        ProductCatalogueService $productCatalogueService,
        $language
    ) {
        $this->productCatalogueService = $productCatalogueService;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $headerTags = $this->productCatalogueService->getFeaturedCatalogues($this->language, 8);
        $view->with('headerTags', $headerTags);
    }
}
