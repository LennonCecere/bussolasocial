<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentTypeRequest extends FormRequest
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
            'installments' => 'required|integer',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.max' => 'O campo nome não pode exceder 255 caracteres.',

            'description.string' => 'O campo descrição deve ser uma string.',
            'description.max' => 'O campo descrição não pode exceder 255 caracteres.',

            'installments.required' => 'O campo parcelas é obrigatório.',
            'installments.integer' => 'O campo parcelas deve ser um numero inteiro.',
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
            'erro' => true,
            'message' => 'Erro de validação.',
            'errors' => $validator->errors(), // Retorna os erros da validação
        ], 422)); // Código HTTP para Unprocessable Entity
    }

}
