<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoryUpdateRequest extends FormRequest
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
            'name' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',
        ];
    }
    public function messages():array
    {
        return[
            'name.required' => 'Full name is required.',
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
