<?php

namespace Infinety\Calendar\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Calendar events form request.
 *
 * @author Eric Lagarda <eric@infinety.es>
 */
class EventRequest extends FormRequest
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
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'model_id.required' => 'Select the project',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|min:3|max:255',
            'description' => 'min:10|max:1000|required',
            // 'start' => 'required|date_format:'.config('calendar.formatDate', 'd-m-Y H:i'),
            'start' => 'required',
            'class' => 'sometimes|string',
            'model_id' => 'required',
        ];

        if (empty($this->input('all_day'))) {
            $rules['end'] = 'required';
        }

        return $rules;
    }
}
