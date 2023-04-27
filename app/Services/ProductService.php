<?php

namespace App\Services;

use App\Models\Product;
use Exception;

class ProductService
{
    /**
     * get all products.
     *
     * @return Product
     */
    public function getProducts($maxPerPage)
    {
        return Product::select(
            'id',
            'product_name',
            'store_id',
            'price',
            'status'
        )
        ->paginate($maxPerPage);
    }

    /**
     * get product by Id has user and product information.
     *
     * @param $id
     * @return Product
     */
    public function getProductById($id)
    {
        return Product::select(
            'id',
            'product_name',
            'store_id',
            'price',
            'status'
        )
        ->with('store:id,store_name,status')
        ->where('id', $id)
        ->get();
    }

    /**
     * get product by Store Id.
     *
     * @param $storeId
     * @return Product
     */
    public function countProductByStoreId($storeId)
    {
        return Product::where('store_id', $storeId)
        ->count();
    }

    /**
     * Search product by name.
     *
     * @param $param
     * @return Product
     */
    public function searchProductByName($param, $maxPerPage)
    {
        return Product::select(
            'id',
            'product_name',
            'store_id',
            'price',
            'status'
        )
        ->when(!empty($param['product_name']), function ($q) use ($param) {
            $keyword = '%' . $param['product_name'] . '%';
            $q->where('product_name', 'like', $keyword);
        })
        ->paginate($maxPerPage);
    }

    /**
     * Create product
     *
     * @param string $productName
     * @param int $storeId
     * @param double $price
     * @param boolean $status
     */
    public function createProduct(
        $productName,
        $storeId,
        $price,
        $status
    ) {
        try {
            $Product = [
                'product_name' => $productName,
                'store_id' => $storeId,
                'price' => $price,
                'status' => $status
            ];
            Product::create($Product);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Edit product
     *
     * @param Product $product
     * @param string $productName
     * @param boolean $status
     */
    public function editProduct(
        $product,
        $productName,
        $price,
        $status
    ) {
        try {
            $product->product_name = $productName;
            $product->price = $price;
            $product->status = $status;
            $product->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Delete product
     *
     * @param Product $product
     */
    public function deleteProduct(
        $product
    ) {
        try {
            $product->delete();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
