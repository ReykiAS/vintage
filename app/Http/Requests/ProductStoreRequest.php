<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'discount' => 'nullable|integer|min:0',
            'weight' => 'required|numeric|min:0',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'array',
            'variants.*.size' => 'required|string',
            'variants.*.quality' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required',
            'price.required' => 'The product price is required',
            'price.numeric' => 'The price must be a number',
            'qty.required' => 'The product quantity is required',
            'qty.integer' => 'The quantity must be an integer',
            'description.string' => 'The description must be a string',
            'discount.integer' => 'The discount must be an integer',
            'weight.required' => 'The product weight is required',
            'weight.numeric' => 'The weight must be a number',
            'brand_id.required' => 'The product brand is required',
            'brand_id.exists' => 'The selected brand is invalid',
            'category_id.required' => 'The product category is required',
            'category_id.exists' => 'The selected category is invalid',
            'photo.image' => 'The photo must be an image',
            'photo.mimes' => 'The photo must be a file of type: jpeg, png, jpg, gif',
            'photo.max' => 'The photo may not be greater than 1 megabytes',
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
