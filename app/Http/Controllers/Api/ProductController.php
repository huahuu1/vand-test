<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\EditProductRequest;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\ProductService;
use App\Services\StoreService;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected $storeService;
    protected $productService;

    /**
     * StoreController constructor.
     *
     * @param StoreService $storeService
     * @param ProductService $productService
     */
    public function __construct(
        StoreService $storeService,
        ProductService $productService
    ) {
        $this->storeService = $storeService;
        $this->productService = $productService;
    }

    /**
     * Get all products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProducts($maxPerPage = null)
    {
        $maxPerPage = $maxPerPage ?? config('defines.pagination.general');
        $result = $this->productService->getProducts($maxPerPage);
        return response()->json([
            'data' => $result->values()->all(),
            'total_pages' => $result->lastPage()
        ]);
    }

    /**
     * Get product by Id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductById(Request $request)
    {
        return response()->json([
            'data' => $this->productService->getProductById($request->id),
        ]);
    }

    /**
     * Search product by name.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProductByName(Request $request, $maxPerPage = null)
    {
        $maxPerPage = $maxPerPage ?? config('defines.pagination.general');
        $result = $this->productService->searchProductByName($request->all(), $maxPerPage);
        return response()->json([
            'data' => $result->values()->all(),
            'total_pages' => $result->lastPage()
        ]);
    }

    /**
     * Create product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProduct(CreateProductRequest $request)
    {
        try {
            $store = Store::find($request->store_id);
            if (!$store) {
                return response()->json([
                    'message' => __('store.getStore.not_found')
                ], 422);
            }
            $this->productService->createProduct(
                $request->product_name,
                $request->store_id,
                $request->price,
                $request->status
            );
            return response()->json([
                'message' => __('product.createProduct.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('product.createProduct.fail'),
                'error' => $e,
            ], 400);
        }
    }

    /**
     * Edit product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editProduct(EditProductRequest $request)
    {
        try {
            $product = Product::find($request->id);
            if (!$product) {
                return response()->json([
                    'message' => __('product.getProduct.not_found')
                ], 422);
            }
            $this->productService->editProduct(
                $product,
                $request->product_name,
                $request->price,
                $request->status
            );
            return response()->json([
                'message' => __('product.editProduct.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('product.editProduct.fail'),
                'error' => $e,
            ], 400);
        }
    }

    /**
     * Delete product.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProduct(Request $request)
    {
        try {
            $product = Product::find($request->id);
            if (!$product) {
                return response()->json([
                    'message' => __('Product.getProduct.not_found')
                ], 422);
            }
            $this->productService->deleteProduct(
                $product
            );
            return response()->json([
                'message' => __('product.deleteProduct.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('product.deleteProduct.fail'),
                'error' => $e,
            ], 400);
        }
    }
}
