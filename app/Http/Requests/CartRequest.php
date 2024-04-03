<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ];
    }

    public function messages():array
    {
        return[
            'user_id.required' => 'The user is required',
            'user_id.exists' => 'The user is invalid',
            'product.required' => 'The product is required',
            'product_id.exists' => 'The selected product is invalid',
            'qty.required' => 'The product quantity is required',
            'qty.integer' => 'The quantity must be an integer',
        ];


    }

    public function FailedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ]));
    }
}
