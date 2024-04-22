<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'qty' => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
            'discount' => 'nullable|integer|min:0',
            'brand_id' => 'sometimes|exists:brands,id',
            'category_id' => 'sometimes|exists:categories,id',
            'variants.*.size' => 'sometimes|string',
            'variants.*.quality' => 'sometimes|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'The product name must be a string',
            'name.max' => 'The product name may not be greater than 255 characters',
            'price.numeric' => 'The price must be a number',
            'price.min' => 'The price must be at least :min',
            'qty.integer' => 'The quantity must be an integer',
            'qty.min' => 'The quantity must be at least :min',
            'description.string' => 'The description must be a string',
            'discount.integer' => 'The discount must be an integer',
            'discount.min' => 'The discount must be at least :min',
            'brand_id.exists' => 'The selected brand is invalid',
            'category_id.exists' => 'The selected category is invalid',
            'variants.*.size.string' => 'The variant size must be a string',
            'variants.*.quality.string' => 'The variant quality must be a string',
            'photo.image' => 'The photo must be an image',
            'photo.mimes' => 'The photo must be a file of type: jpeg, png, jpg, gif',
            'photo.max' => 'The photo may not be greater than :max kilobytes',
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
