<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'image' => 'nullable|image|max:2048',
            'username' => [
                'required',
                Rule::unique('users', 'username')->ignore($this->user()->id),
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(Response::error("The given data was invalid.", 400, $validator->errors()->messages()));
    }
}
