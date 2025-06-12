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
        return ProductModel::create($request);
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
            return response()->json([
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
                return response()->json([
                    'success' => true,
                    'message' => 'Produto já está excluído.',
                ], 200);
            }

            $paymentType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Produto excluído com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
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
                return response()->json([
                    'success' => true,
                    'message' => 'Produto já está ativo.',
                ], 200);
            }

            $paymentType->restore();

            return response()->json([
                'success' => true,
                'message' => 'Produto ativo com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado.',
            ], 404);
        }
    }
}
