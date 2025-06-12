<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Service\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    protected ProductService $product;

    /**
     * @param ProductService $product
     */
    public function __construct(ProductService $product)
    {
        $this->product = $product;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->product->findAll();

        if ($response->isEmpty()) {
            $response = ['message' => 'Nenhum produto foi encontrado.'];
        }

        return response()->json($response);
    }

    /**
     * @param ProductRequest $request
     * @return mixed
     */
    public function store(ProductRequest $request): mixed
    {
        return $this->product->save($request->toArray());
    }

    /**
     * @param ProductRequest $request
     * @param $id
     * @return mixed
     */
    public function update(ProductRequest $request, $id): mixed
    {
        return $this->product->update($id, $request->toArray());
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->product->destroy($id);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function active($id): JsonResponse
    {
        return $this->product->active($id);
    }
}
