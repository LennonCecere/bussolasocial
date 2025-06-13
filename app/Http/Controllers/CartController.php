<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartCheckoutRequest;
use App\Http\Requests\CartRequest;
use App\Service\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    /**
     * @var CartService
     */
    protected CartService $cart;

    /**
     * @param CartService $cart
     */
    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->cart->find();

        return new JsonResponse($response, 200);
    }

    /**
     * @param CartRequest $request
     * @return JsonResponse
     */
    public function store(CartRequest $request): JsonResponse
    {
        return $this->cart->addProduct($request);
    }

    /**
     * @param CartRequest $request
     * @return JsonResponse
     */
    public function update(CartRequest $request): JsonResponse
    {
        return $this->cart->updateProduct($request);
    }

    /**
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        return $this->cart->destroy();
    }

    /**
     * @param CartCheckoutRequest $request
     * @return JsonResponse
     */
    public function checkout(CartCheckoutRequest $request): JsonResponse
    {
        return $this->cart->checkout($request);
    }
}
