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
     * @return JsonResponse
     */
    public function save(array $request): JsonResponse
    {
        try {
            $paymentType = PaymentTypeModel::create($request);
            return new JsonResponse([
                'erro' => false,
                'data' => $paymentType
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse([
                'erro' => true,
                'message' => 'Erro ao criar tipo de pagamento.'
            ], 500);
        }
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
            return new JsonResponse([
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
                return new JsonResponse([
                    'erro' => false,
                    'message' => 'Tipo de pagamento já está excluído.',
                ], 200);
            }

            $paymentType->delete();

            return new JsonResponse([
                'erro' => false,
                'message' => 'Tipo de pagamento excluído com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return new JsonResponse([
                'erro' => true,
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
                return new JsonResponse([
                    'erro' => false,
                    'message' => 'O tipo de pagamento já está ativo.',
                ], 200);
            }

            $paymentType->restore();

            return new JsonResponse([
                'erro' => false,
                'message' => 'Tipo de pagamento ativado com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return new JsonResponse([
                'erro' => true,
                'message' => 'Tipo de pagamento não encontrado.',
            ], 404);
        }
    }
}
