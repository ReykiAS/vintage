<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
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
            'fullname' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'street' => 'nullable|string',
            'district' => 'nullable|string',
            'city' => 'nullable|string',
            'regency' => 'nullable|string',
            'province' => 'nullable|string',
            'zip_code' => 'nullable|numeric',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',
        ];
    }
    public function messages():array
    {
        return[
            'fullname.required' => 'Full name is required.',
            'username.required' => 'Username is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Email address must be valid.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
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
