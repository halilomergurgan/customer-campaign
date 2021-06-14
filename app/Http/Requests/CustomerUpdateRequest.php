<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|min:2|max:255,' . $this->customerId . ',id',
            'revenue' => 'required|numeric|min:0|max:999999999999',
            'since' => 'nullable|date_format:Y-m-d|before:' . date('Y-m-d'),
        ];
    }
}
