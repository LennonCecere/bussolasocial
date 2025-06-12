<?php

namespace App\Http\Requests;

use App\Models\CartModel;
use App\Models\PaymentTypeModel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartCheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $cart = CartModel::all();

        if ($cart->isEmpty()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Não é possível fazer checkout com o carrinho vazio.',
                    'errors' => [],
                ], 422)
            );
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'payment_type_id' => 'required|integer|exists:payment_type,id',
        ];

        $paymentType = PaymentTypeModel::find($this->input('payment_type_id'));
        if ($paymentType && $paymentType->installments > 1) {
            $rules = array_merge($rules, [
                'card_holder_name' => 'required|string|max:255',
                'card_number' => 'required|numeric|digits_between:13,19',
                'card_expiry_date' => 'required|date_format:m/y|after:today',
                'card_cvv' => 'required|numeric|digits_between:3,4',
                'installments' => 'required|integer|min:2|max:' . $paymentType->installments,
            ]);
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_type_id.required' => 'O campo tipo de pagamento é obrigatório.',
            'payment_type_id.integer' => 'O tipo de pagamento deve ser um número inteiro.',
            'payment_type_id.exists' => 'O tipo de pagamento selecionado é inválido.',

            'card_holder_name.required' => 'O Nome do titular do cartão é obrigatório.',
            'card_holder_name.string' => 'O Nome do titular do cartão deve ser um texto válido.',
            'card_holder_name.max' => 'O Nome do titular do cartão não pode exceder 255 caracteres.',
            'card_number.required' => 'O Número do cartão de crédito é obrigatório.',
            'card_number.numeric' => 'O Número do cartão de crédito deve conter apenas números.',
            'card_number.digits_between' => 'O Número do cartão de crédito deve ter entre 13 e 19 dígitos.',
            'card_expiry_date.required' => 'A Data de validade do cartão é obrigatória.',
            'card_expiry_date.date_format' => 'A Data de validade do cartão deve estar no formato MM/AA.',
            'card_expiry_date.after' => 'A Data de validade do cartão deve ser uma data futura.',
            'card_cvv.required' => 'O Código de segurança (CVV) é obrigatório.',
            'card_cvv.numeric' => 'O Código de segurança (CVV) deve conter apenas números.',
            'card_cvv.digits_between' => 'O Código de segurança (CVV) deve ter entre 3 e 4 dígitos.',
        ];
    }

    /**
     * @param Validator $validator
     * @return mixed
     */
    public function failedValidation(Validator $validator): mixed
    {
        // Lançar uma exceção com a resposta JSON personalizada
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação.',
            'errors' => $validator->errors(), // Retorna os erros da validação
        ], 422)); // Código HTTP para Unprocessable Entity
    }

}
