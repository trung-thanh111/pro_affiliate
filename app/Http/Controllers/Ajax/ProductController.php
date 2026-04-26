<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Services\V1\Product\ProductService;
use App\Repositories\Product\ProductCatalogueRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductVariantRepository;
use App\Repositories\Product\PromotionRepository;
use App\Repositories\Attribute\AttributeRepository;
use App\Services\V1\Product\CompareService;

use App\Models\Language;
use Gloudemans\Shoppingcart\Facades\Cart;


class ProductController extends Controller
{
    protected $productService;
    protected $productCatalogueService;
    protected $productCatalogueRepository;
    protected $productVariantRepository;
    protected $promotionRepository;
    protected $attributeRepository;
    protected $productRepository;
    protected $compareService;
    protected $language;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductVariantRepository $productVariantRepository,
        ProductRepository $productRepository,
        PromotionRepository $promotionRepository,
        AttributeRepository $attributeRepository,
        ProductService $productService,
        CompareService $compareService,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productRepository = $productRepository;
        $this->promotionRepository = $promotionRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productService = $productService;
        $this->compareService = $compareService;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function loadProductPromotion(Request $request)
    {

        $get = $request->input();

        $loadClass = loadClass($get['model']);

        if ($get['model'] == 'Product') {
            $condition = [
                ['tb2.language_id', '=', $this->language]
            ];
            if (isset($get['keyword']) && $get['keyword'] != '') {
                $keywordCondition = ['tb2.name', 'LIKE', '%' . $get['keyword'] . '%'];
                array_push($condition, $keywordCondition);
            }
            $objects = $loadClass->findProductForPromotion($condition);
        } else if ($get['model'] == 'ProductCatalogue') {

            $conditionArray['keyword'] = ($get['keyword']) ?? null;
            $conditionArray['where'] = [
                ['tb2.language_id', '=', $this->language]
            ];

            $objects = $loadClass->pagination(
                [
                    'product_catalogues.id',
                    'tb2.name',
                ],
                $conditionArray,
                20,
                ['path' => 'product.catalogue.index'],
                ['product_catalogues.id', 'DESC'],
                [
                    ['product_catalogue_language as tb2', 'tb2.product_catalogue_id', '=', 'product_catalogues.id']
                ],
                []
            );
        }

        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects,
        ]);
    }

    public function loadProductVoucher(Request $request)
    {

        $get = $request->input();

        $loadClass = loadClass($get['model']);

        $condition = [
            ['tb2.language_id', '=', $this->language]
        ];

        if (isset($get['keyword']) && $get['keyword'] != '') {
            $keywordCondition = ['tb2.name', 'LIKE', '%' . $get['keyword'] . '%'];
            array_push($condition, $keywordCondition);
        }

        $objects = $loadClass->findProductForVoucher($condition);

        return response()->json([
            'model' => ($get['model']) ?? 'Product',
            'objects' => $objects,
        ]);
    }

    public function loadVariant(Request $request)
    {
        $get = $request->input();
        $attributeId = $get['attribute_id'];

        $attributeId = sortAttributeId($attributeId);

        $variant = $this->productVariantRepository->findVariant($attributeId, $get['product_id'], $get['language_id']);

        $variantPromotion = $this->promotionRepository->findPromotionByVariantUuid($variant->uuid);
        $variantPrice = getVariantPrice($variant, $variantPromotion);

        return response()->json([
            'variant' => $variant,
            'variantPrice' => $variantPrice,
        ]);
    }


    public function filter(Request $request)
    {

        $products = $this->productService->filter($request);

        $countProduct = $products->count();

        $html = $this->renderFilterProduct($products);

        return response()->json([
            'data' => $html,
            'countProduct' => $countProduct
        ]);
    }

    public function getProducts(Request $request)
    {
        $catalogueId = $request->input('catalogue_id');
        $productCatalogue = $this->productCatalogueRepository->findById($catalogueId);
        if (!$productCatalogue) {
            return response()->json(['code' => 0, 'message' => 'Danh mục không tồn tại'], 404);
        }

        $products = $this->productService->paginate(
            $request,
            $this->language,
            $productCatalogue,
            1,
            ['limit' => 12],
            ['products.order', 'desc']
        );

        $productId = $products->pluck('id')->toArray();
        if (count($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }

        $html = $this->renderFilterProduct($products);

        return response()->json([
            'html' => $html,
            'code' => 10
        ]);
    }

    public function renderFilterProduct($products)
    {
        $html = '';
        if (!is_null($products) && count($products)) {
            foreach ($products as $product) {
                $html .= '<div class="mb20">';
                $html .= view('frontend.component.product_card', compact('product'))->render();
                $html .= '</div>';
            }
        } else {
            $html .= '<div class="no-result col-12 text-center py-5">
                <img src="/backend/img/no-product.png" alt="No product" class="img-fluid mb-3" style="max-width: 150px; opacity: 0.5;">
                <p class="text-muted">Không có sản phẩm phù hợp</p>
            </div>';
        }
        return $html;
    }



    public function wishlist(Request $request)
    {
        $id = (int) $request->input('id');
        $wishlist = Cart::instance('wishlist')->content();

        if ($id <= 0) {
            return response()->json([
                'code' => 0,
                'message' => 'Sản phẩm không hợp lệ',
                'wishlistTotal' => Cart::instance('wishlist')->count(),
            ]);
        }

        $itemIndex = $wishlist->search(function ($item) use ($id) {
            return (int)$item->id === $id;
        });

        $response = [
            'code' => 0,
            'message' => '',
            'wishlistTotal' => Cart::instance('wishlist')->count(),
        ];

        if ($itemIndex !== false && isset($wishlist->keyBy('id')[$id])) {
            Cart::instance('wishlist')->remove($wishlist->keyBy('id')[$id]->rowId);
            $response['code'] = 1;
            $response['message'] = 'Sản phẩm đã được xóa khỏi danh sách yêu thích';
        } else {
            Cart::instance('wishlist')->add([
                'id' => $id,
                'name' => 'wishlist item',
                'qty' => 1,
                'price' => 0,
            ]);

            $response['code'] = 2;
            $response['message'] = 'Đã thêm sản phẩm vào danh sách yêu thích';
        }

        $response['wishlistTotal'] = Cart::instance('wishlist')->count();

        return response()->json($response);
    }

    public function unWishlist(Request $request)
    {
        $id = (int) $request->input('id');
        $wishlist = Cart::instance('wishlist')->content();

        if ($id <= 0) {
            return response()->json([
                'code' => 0,
                'message' => 'Sản phẩm không hợp lệ',
                'wishlistTotal' => Cart::instance('wishlist')->count(),
            ]);
        }

        $item = $wishlist->firstWhere('id', $id);

        if (!$item) {
            return response()->json([
                'code' => 0,
                'message' => 'Sản phẩm không tồn tại trong danh sách yêu thích',
                'wishlistTotal' => Cart::instance('wishlist')->count(),
            ]);
        }

        Cart::instance('wishlist')->remove($item->rowId);

        return response()->json([
            'code' => 1,
            'message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích',
            'wishlistTotal' => Cart::instance('wishlist')->count(),
        ]);
    }

    public function compareSearch(Request $request)
    {
        $keyword = $request->input('keyword');
        $products = $this->productRepository->searchForCompare($keyword, $this->language);

        $items = $products->map(function ($product) {
            $price = $product->price > 0 ? convert_price($product->price, true) . '₫' : 'Liên hệ';
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => image($product->image),
                'code' => $product->code,
                'price' => $price,
            ];
        });

        return response()->json([
            'data' => $items,
        ]);
    }

    public function compareAdd(Request $request)
    {
        $productId = (int) $request->input('id');
        $position = (int) $request->input('position', 0);

        if ($productId <= 0 || $position < 0 || $position >= CompareService::MAX_ITEMS) {
            return response()->json([
                'code' => 0,
                'message' => 'Thông tin không hợp lệ',
            ], 422);
        }

        $product = $this->productRepository->findByIds([$productId], $this->language)->first();

        if (!$product) {
            return response()->json([
                'code' => 0,
                'message' => 'Không tìm thấy sản phẩm',
            ], 404);
        }

        $cart = Cart::instance('compare');
        $items = $cart->content();

        $duplicate = $items->firstWhere('id', $productId);
        if ($duplicate) {
            $cart->remove($duplicate->rowId);
            $items = $cart->content();
        }

        $slotItem = $items->first(function ($item) use ($position) {
            return (int) data_get($item->options, 'position', -1) === $position;
        });

        if ($slotItem) {
            $cart->remove($slotItem->rowId);
        } elseif ($cart->count() >= CompareService::MAX_ITEMS) {
            return response()->json([
                'code' => 0,
                'message' => 'Bạn chỉ có thể so sánh tối đa ' . CompareService::MAX_ITEMS . ' sản phẩm.',
                'html' => $this->compareService->renderTable($this->language),
                'compareTotal' => $cart->count(),
            ]);
        }

        $cart->add([
            'id' => $productId,
            'name' => $product->name,
            'qty' => 1,
            'price' => 0,
            'weight' => 0,
            'options' => [
                'position' => $position,
            ],
        ]);

        return response()->json([
            'code' => 1,
            'message' => 'Đã thêm sản phẩm vào danh sách so sánh',
            'html' => $this->compareService->renderTable($this->language),
            'compareTotal' => $cart->count(),
        ]);
    }

    public function compareRemove(Request $request)
    {
        $rowId = $request->input('rowId');
        $position = (int) $request->input('position', -1);
        $productId = (int) $request->input('id');

        $cart = Cart::instance('compare');
        $items = $cart->content();

        if ($rowId) {
            $cart->remove($rowId);
        } elseif ($position >= 0) {
            $slotItem = $items->first(function ($item) use ($position) {
                return (int) data_get($item->options, 'position', -1) === $position;
            });
            if ($slotItem) {
                $cart->remove($slotItem->rowId);
            }
        } elseif ($productId > 0) {
            $item = $items->firstWhere('id', $productId);
            if ($item) {
                $cart->remove($item->rowId);
            }
        }

        return response()->json([
            'code' => 1,
            'message' => 'Đã xóa sản phẩm khỏi danh sách so sánh',
            'html' => $this->compareService->renderTable($this->language),
            'compareTotal' => $cart->count(),
        ]);
    }

    public function compareList()
    {
        return response()->json([
            'html' => $this->compareService->renderTable($this->language),
            'compareTotal' => Cart::instance('compare')->count(),
        ]);
    }

    public function updateOrder(Request $request)
    {
        $payload['order'] =  $request->input('order');
        unset($payload['id']);
        $id = $request->input('id');
        $class = loadClass($request->input('model'));
        $update_order = $class->update($id, $payload);
        return response()->json([
            'response' => $update_order,
            'messages' => 'Cập nhật thứ tự thành công',
            'code' => (!$update_order) ? 11 : 10,
        ]);
    }
}
