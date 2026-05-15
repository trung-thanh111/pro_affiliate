<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Language;
use App\Models\System;

class FrontendController extends Controller
{
    protected $language;
    protected $systemRepository;
    protected $system;

    public function __construct(
        // SystemRepository $systemRepository
    ){

        $this->setLanguage();
        $this->setSystem();

    }

    public function setLanguage(){
        $locale = app()->getLocale(); // vn en cn
        // $language = Language::where('canonical', $locale)->first();
        $this->language = 1;
    }

    public function setSystem(){
        $this->system = convert_array(System::where('language_id', $this->language)->get(), 'keyword', 'content');
        
        // Fetch a random affiliate product for fallback redirection
        $randomProduct = \App\Models\Product::whereNotNull('link')
            ->where('link', '!=', '')
            ->where('publish', '=', 2)
            ->with(['languages' => function($query) {
                $query->where('language_id', $this->language);
            }])
            ->inRandomOrder()
            ->first();

        $fallbackAffiliateUrl = '';
        if ($randomProduct) {
            $fallbackAffiliateUrl = $randomProduct->link;
        }
        
        view()->share('fallbackAffiliateUrl', $fallbackAffiliateUrl);
    }
   

}
