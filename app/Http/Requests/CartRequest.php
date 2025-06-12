<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:product,id', 
            'quantity' => 'required|integer|min:1'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'O campo produto é obrigatório.',
            'product_id.integer' => 'O campo produto deve ser um número inteiro.',
            'product_id.exists' => 'O produto selecionado não existe.',

            'quantity.required' => 'O campo quantidade é obrigatório.',
            'quantity.integer' => 'O campo quantidade deve ser um número inteiro.',
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
