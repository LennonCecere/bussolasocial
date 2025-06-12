<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'price_in_cents' => 'required|integer',
            'quantity_in_stock' => 'required|integer',
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser um texto.',
            'name.max' => 'O campo nome não pode exceder 255 caracteres.',

            'description.string' => 'O campo descrição deve ser um texto.',
            'description.max' => 'O campo descrição não pode exceder 255 caracteres.',

            'price_in_cents.required' => 'O campo preço em centavos é obrigatório.',
            'price_in_cents.integer' => 'O campo preço em centavos deve ser um número inteiro.',

            'quantity_in_stock.required' => 'O campo quantidade em estoque é obrigatório.',
            'quantity_in_stock.integer' => 'O campo quantidade em estoque deve ser um número inteiro.',
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
