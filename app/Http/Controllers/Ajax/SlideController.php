<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;

use App\Services\V1\Core\SlideService;


class SlideController extends Controller
{
    protected $slideService;
    protected $language;

    public function __construct(
        SlideService $slideService
    ){
        $this->slideService = $slideService;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $this->language = current_language_id($locale);
            return $next($request);
        });
    }

    public function order(Request $request){
        $payload = $request->input('payload');
        $flag = $this->slideService->updateSlideOrder($payload, $this->language);
    }
    
    
}
