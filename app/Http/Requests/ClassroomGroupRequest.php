<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClassroomGroupRequest extends FormRequest
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
        $rules = [
            "name" => "required|max:200",
            "activity_id" => "required|max:45",
            "activity_group" => "required|max:45",
            "duration" => "required",
            "language" => "nullable",
            "capacity" => "nullable|integer",
            "capacity_left" => "nullable|integer",
            "location" => "nullable|max:100",
        ];

        if ($this->getMethod() == 'POST') {
            $rules += ["subject_id" => "required"];
        }

        return $rules;
    }
}
