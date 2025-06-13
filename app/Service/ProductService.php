<?php

namespace App\Service;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ProductService
{
    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return ProductModel::all();
    }

    /**
     * @param array $request
     * @return mixed
     */
    public function save(array $request): mixed
    {
        try {
            $product = ProductModel::create($request);
            return new JsonResponse([
                'erro' => false,
                'data' => $product
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
     * @return mixed
     */
    public function update($id, array $data): mixed
    {
        try {
            $payment = ProductModel::findOrFail($id);
            $payment->update($data);

            return $payment;
        } catch (ModelNotFoundException $e) {
            return new JsonResponse([
                'error' => true,
                'message' => 'Produto não encontrado.',
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
            $paymentType = ProductModel::withTrashed()->findOrFail($id);

            if ($paymentType->deleted_at !== null) {
                return new JsonResponse([
                    'erro' => false,
                    'message' => 'Produto já está excluído.',
                ], 200);
            }

            $paymentType->delete();

            return new JsonResponse([
                'erro' => false,
                'message' => 'Produto excluído com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return new JsonResponse([
                'erro' => true,
                'message' => 'Produto não encontrado.',
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
            $paymentType = ProductModel::withTrashed()->findOrFail($id);

            if ($paymentType->deleted_at === null) {
                return new JsonResponse([
                    'erro' => false,
                    'message' => 'O produto já está ativo.',
                ], 200);
            }

            $paymentType->restore();

            return new JsonResponse([
                'erro' => false,
                'message' => 'Produto ativado com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return new JsonResponse([
                'erro' => true,
                'message' => 'Produto não encontrado.',
            ], 404);
        }
    }
}
