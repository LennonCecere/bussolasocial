<?php

namespace App\Service;

use App\Models\PaymentTypeModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PaymentTypeService
{
    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return PaymentTypeModel::all();
    }

    /**
     * @param array $request
     * @return mixed
     */
    public function save(array $request): mixed
    {
        return PaymentTypeModel::create($request);
    }

    /**
     * @param $id
     * @param array $data
     * @return JsonResponse
     */
    public function update($id, array $data): JsonResponse
    {
        try {
            $payment = PaymentTypeModel::findOrFail($id);
            $payment->update($data);

            return $payment;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Tipo de pagamento não encontrado.',
            ], 404);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $paymentType = PaymentTypeModel::withTrashed()->findOrFail($id);

            if ($paymentType->deleted_at !== null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de pagamento já está excluído.',
                ], 200);
            }

            $paymentType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de pagamento excluído com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de pagamento não encontrado.',
            ], 404);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function active($id): JsonResponse
    {
        try {
            $paymentType = PaymentTypeModel::withTrashed()->findOrFail($id);

            if ($paymentType->deleted_at === null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipo de pagamento já está ativo.',
                ], 200);
            }

            $paymentType->restore();

            return response()->json([
                'success' => true,
                'message' => 'Tipo de pagamento ativo com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de pagamento não encontrado.',
            ], 404);
        }
    }
}
