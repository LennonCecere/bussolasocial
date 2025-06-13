<?php

namespace App\Service;

use App\Helper\ConvertNumber;
use App\Models\CartModel;
use App\Models\PaymentTypeModel;
use Exception;
use Illuminate\Http\JsonResponse;

class CartService
{
    /**
     * @return array
     */
    public function find(): array
    {
        $products =  CartModel::all();

        if ($products->isEmpty()) {
            return [
                "erro" => true,
                "message" => "O carrinho está vazio.",
                "data" => false,
            ];
        }

        $response = [];
        foreach ($products as $product) {
            $response[] = [
                'Nome' => $product->product->name,
                'Preço unitário' => 'R$ '.ConvertNumber::centToReal($product->product->price_in_cents),
                'Quantidade' => $product->quantity,
            ];
        }

        return $response;
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function addProduct($request): JsonResponse
    {
        $results = CartModel::where('product_id', $request->product_id)->first();

        try {
            if (!empty($results)) {
                return new JsonResponse([
                    "erro" => true,
                    "message" => "O produto já está no carrinho.",
                    "data" => false,
                ], 400);
            } else {
                $cart = CartModel::create([
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }
        } catch (Exception $exception) {
            return new JsonResponse([
                "erro" => true,
                "message" => "Erro ao inserir o produto no carrinho. Tente novamente.",
                "data" => false,
            ], 400);
        }

        return new JsonResponse([
            "erro" => false,
            "message" => "Produto adicionado no carrinho com sucesso.",
            "data" => $cart,
        ], 200);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function updateProduct($request): JsonResponse
    {
        $results = CartModel::where('product_id', $request->product_id)->first();

        try {
            if (!empty($results)) {
                $cart = $results->update(['quantity' => $request->quantity]);
            } else {
                return new JsonResponse([
                    "erro" => true,
                    "message" => "O produto não está no carrinho.",
                    "data" => false,
                ], 200);
            }
        } catch (Exception $exception) {
            return new JsonResponse([
                "erro" => true,
                "message" => "Erro ao atualizar o produto no carrinho. Tente novamente.",
                "data" => false,
            ], 400);
        }

        return new JsonResponse([
            "erro" => false,
            "message" => "Quantidade do produto {$results->product->name} foi atualizada no carrinho com sucesso.",
            "data" => $cart,
        ], 200);
    }

    /**
     * @return JsonResponse
     */
    public function destroy(): JsonResponse
    {
        $cart = CartModel::all();
        if ($cart->isEmpty()) {
              return new JsonResponse([
                'erro' => false,
                'message' => 'O carrinho já está vazio.',
            ], 200);
        }

        $cart->each->delete();

        return new JsonResponse([
            'erro' => false,
            'message' => 'Carrinho deletado com sucesso.',
        ], 200);
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function checkout($request): JsonResponse
    {
        $paymentType = PaymentTypeModel::find($request->payment_type_id);
        $cart = CartModel::all();

        $totalPurchase = 0;
        $cart->each(function ($item) use (&$totalPurchase) {
            $totalPurchase += $item->quantity * $item->product->price_in_cents; // Soma o preço total por produto
        });
        $totalPurchase = (float)ConvertNumber::centToReal($totalPurchase);

        if ($paymentType->installments > 1) {
            // Pagamento Parcelado (Cartão de Crédito)
            $installments = $request->installments; // Quantidade de parcela escolhida na compra
            $interestRate = 0.01; // Taxa de juros de 1% ao mês
            $totalFees = pow((1 + $interestRate), $installments); // Cálculo de juros compostos
            $finalAmount = $totalFees + $totalPurchase; // Coma o juros com o valor total da compra

            return new JsonResponse([
                "success" => true,
                "message" => "Juros de 1% ao mês aplicado para pagamento parcelado em $installments vezes.",
                "data" => [
                    "total_original" => 'R$ '.number_format($totalPurchase, 2, ',', '.'),
                    "total_com_juros" => 'R$ '.number_format($finalAmount, 2, ',', '.'),
                    "quantidade_parcelas" => $installments,
                    "valor_por_parcela" => 'R$ '.number_format($finalAmount / $installments, 2, ',', '.'),
                ],
            ], 200);
        } else {
            // Pagamento à vista (Pix ou Cartão de Crédito à Vista)
            $discountedTotal = $totalPurchase * 0.9; // Aplica 10% de desconto
              return new JsonResponse([
                "success" => true,
                "message" => "Desconto de 10% aplicado para pagamento à vista.",
                "data" => [
                    "total_original" => 'R$ '.number_format($totalPurchase, 2, ',', '.'),
                    "total_com_desconto" => 'R$ '.number_format($discountedTotal, 2, ',', '.'),
                    "valor_do_desconto" => 'R$ '.number_format(($discountedTotal - $totalPurchase), 2, ',', '.')
                ],
            ], 200);
        }
    }
}
