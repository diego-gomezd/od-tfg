<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
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
            "code" => "required|unique:subjects|max:15",
            "name" => "required|unique:subjects|max:200",
            "department_id" => "required|integer",
            "english_name" => "nullable|max:200",
            "ects" => "required|integer",
            "comments" => "nullable|max:65535",
        ];
    }
}
