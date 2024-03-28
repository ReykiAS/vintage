<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BrandStoreRequest extends FormRequest
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
            'name' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
        ];
    }


    /**
     * Get the validation error messages.
   
     * In this case, we've defined custom messages for the following validation rules:
     *  - 'name.required': The error message displayed when the 'name' field is empty.
     *  - 'photo.image': The error message displayed when the 'photo' field is not a valid image file.
     *  - 'photo.mimes': The error message displayed when the 'photo' file format is not supported (JPEG, PNG, JPG, or GIF).
     *  - 'photo.max': The error message displayed when the 'photo' file size exceeds 5MB.
     *
     * @return array
     */
    public function messages():array
    {
        return[
            'name.required' => 'Nama harus diisi',
            'photo.image' => 'Foto harus berupa gambar',
            'photo.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'photo.max' => 'Ukuran gambar tidak boleh lebih dari 5MB',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     *
     * @throws HttpResponseException
     */
    public function FailedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ]));
    }
}
