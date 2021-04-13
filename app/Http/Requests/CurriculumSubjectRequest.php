<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurriculumSubjectRequest extends FormRequest
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
            "subject_id" => "required|integer",
            "type" => "required|max:5",
            "duration" => "required|max:5",
            "course" => "nullable|max:255",
            "part_time_course" => "nullable|integer",
            "comments" => "nullable|max:65535",
        ];
    }
}
