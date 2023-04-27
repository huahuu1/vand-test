<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateStoreRequest;
use App\Http\Requests\EditStoreRequest;
use App\Models\Store;
use App\Models\User;
use App\Services\ProductService;
use App\Services\StoreService;
use Exception;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
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
     * Get all stores.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStores($maxPerPage = null)
    {
        $maxPerPage = $maxPerPage ?? config('defines.pagination.general');
        $result = $this->storeService->getStores($maxPerPage);
        return response()->json([
            'data' => $result->values()->all(),
            'total_pages' => $result->lastPage()
        ]);
    }

    /**
     * Get store by Id.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoreById(Request $request)
    {
        return response()->json([
            'data' => $this->storeService->getStoreById($request->id),
        ]);
    }

    /**
     * Search Store by name.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchStoreByName(Request $request, $maxPerPage = null)
    {
        $maxPerPage = $maxPerPage ?? config('defines.pagination.general');
        $result = $this->storeService->searchStoreByName($request->all(), $maxPerPage);
        return response()->json([
            'data' => $result->values()->all(),
            'total_pages' => $result->lastPage()
        ]);
    }

    /**
     * Create store
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createStore(CreateStoreRequest $request)
    {
        try {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json([
                    'message' => __('user.getUser.not_found')
                ], 422);
            }
            $this->storeService->createStore(
                $request->store_name,
                $request->user_id,
                $request->status
            );
            return response()->json([
                'message' => __('store.createStore.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('store.createStore.fail'),
                'error' => $e,
            ], 400);
        }
    }

    /**
     * Edit store.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function editStore(EditStoreRequest $request)
    {
        try {
            $store = Store::find($request->id);
            if (!$store) {
                return response()->json([
                    'message' => __('store.getStore.not_found')
                ], 422);
            }
            $this->storeService->editStore(
                $store,
                $request->store_name,
                $request->status
            );
            return response()->json([
                'message' => __('store.editStore.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('store.editStore.fail'),
                'error' => $e,
            ], 400);
        }
    }

    /**
     * Delete store.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteStore(Request $request)
    {
        try {
            $store = Store::find($request->id);
            if (!$store) {
                return response()->json([
                    'message' => __('store.getStore.not_found')
                ], 422);
            }
            //check if store still have any products
            $countProduct = $this->productService->countProductByStoreId($store->id);
            if ($countProduct != 0) {
                return response()->json([
                    'message' => __('store.deleteStore.have_product')
                ], 422);
            }
            $this->storeService->deleteStore(
                $store
            );
            return response()->json([
                'message' => __('store.deleteStore.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('store.deleteStore.fail'),
                'error' => $e,
            ], 400);
        }
    }
}
