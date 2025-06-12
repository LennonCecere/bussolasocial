<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentTypeRequest;
use App\Service\PaymentTypeService;
use Illuminate\Http\JsonResponse;

class PaymentTypeController extends Controller
{
    /**
     * @var PaymentTypeService
     */
    protected PaymentTypeService $paymentType;

    /**
     * @param PaymentTypeService $paymentType
     */
    public function __construct(PaymentTypeService $paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->paymentType->findAll();

        if ($response->isEmpty()) {
            $response = ['message' => 'Nenhum tipo de pagamento foi encontrado.'];
        }

        return response()->json($response);
    }

    /**
     * @param PaymentTypeRequest $request
     * @return mixed
     */
    public function store(PaymentTypeRequest $request): mixed
    {
        return $this->paymentType->save($request->toArray());
    }

    /**
     * @param PaymentTypeRequest $request
     * @param $id
     * @return mixed
     */
    public function update(PaymentTypeRequest $request, $id): mixed
    {
        return $this->paymentType->update($id, $request->toArray());
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->paymentType->destroy($id);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function active($id): JsonResponse
    {
        return $this->paymentType->active($id);
    }
}
