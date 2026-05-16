<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;

use App\Services\V1\Core\OrderService;
use App\Repositories\Core\OrderRepository;
use App\Services\V1\Customer\CustomerService;
use App\Services\V1\Product\ProductService;

class DashboardController extends Controller
{


    protected $orderService;
    protected $customerService;
    protected $orderRepository;
    protected $productService;
    protected $language;

    public function __construct(
        OrderService $orderService,
        CustomerService $customerService,
        OrderRepository $orderRepository,
        ProductService $productService,
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = \App\Models\Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });

        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->customerService = $customerService;
        $this->productService = $productService;
    }

    public function index(){

        $orderStatistic = $this->orderService->statistic(); 
        $customerStatistic = $this->customerService->statistic();
        $startDate = convertDateTime( now(), 'Y-m-d 00:00:00');
        $endDate = convertDateTime( now(), 'Y-m-d 23:59:59');
        $newOrders = $this->orderRepository->newOrder($startDate, $endDate);
        $productStatistic = $this->productService->statistic($this->language);
        $config = $this->config();
        $template = 'backend.dashboard.home.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orderStatistic',
            'customerStatistic',
            'productStatistic',
            'newOrders'
        ));
    }

    private function config(){
        return [
            'js' => [
                'backend/js/plugins/chartJs/Chart.min.js',
                'backend/library/dashboard.js',
            ]
        ];
    }

}
