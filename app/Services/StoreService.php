<?php

namespace App\Services;

use App\Models\Store;
use Exception;

class StoreService
{
    /**
     * get all stores.
     *
     * @return Store
     */
    public function getStores($maxPerPage)
    {
        return Store::select(
            'id',
            'store_name',
            'user_id',
            'status'
        )
        ->paginate($maxPerPage);
    }

    /**
     * get store by Id has user and product information.
     *
     * @param $id
     * @return Store
     */
    public function getStoreById($id)
    {
        return Store::select(
            'id',
            'store_name',
            'user_id'
        )
        ->with('user:id,username,email,status')
        ->with('products:id,store_id,product_name,price,status')
        ->where('id', $id)
        ->get();
    }

    /**
     * Search store by name.
     *
     * @param $param
     * @return Store
     */
    public function searchStoreByName($param, $maxPerPage)
    {
        return Store::select(
            'id',
            'store_name',
            'user_id',
            'status'
        )
        ->when(!empty($param['store_name']), function ($q) use ($param) {
            $keyword = '%' . $param['store_name'] . '%';
            $q->where('store_name', 'like', $keyword);
        })
        ->paginate($maxPerPage);
    }

    /**
     * Create store
     *
     * @param string $storeName
     * @param int $userId
     * @param boolean $status
     */
    public function createStore(
        $storeName,
        $userId,
        $status
    ) {
        try {
            $store = [
                'store_name' => $storeName,
                'user_id' => $userId,
                'status' => $status
            ];
            Store::create($store);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Edit store
     *
     * @param Store $store
     * @param string $storeName
     * @param boolean $status
     */
    public function editStore(
        $store,
        $storeName,
        $status
    ) {
        try {
            $store->store_name = $storeName;
            $store->status = $status;
            $store->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Delete store
     *
     * @param Store $store
     */
    public function deleteStore(
        $store
    ) {
        try {
            $store->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
