<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RatingStoreRequest extends FormRequest
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
            'rating' => 'required|integer|min:0',
            'comment' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'order_detail_id' => 'required|exists:order_details,id',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.numeric' => 'The rating must be a number',
            'rating.required' => 'The rating is required',
            'order_detail_id.required' => 'The order detail id is required',
            'order_detail_id.exists' => 'The selected order is invalid',
            'product_id.required' => 'The product is required',
            'product_id.exists' => 'The selected product is invalid',
            'user_id.required' => 'The product user is required',
            'user_id.exists' => 'The selected user is invalid',
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
