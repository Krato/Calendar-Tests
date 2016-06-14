<?php

namespace Infinety\Calendar\Http\Requests;

use App\Http\Requests\Request;

/**
 * Calendar events form request
 *
 * @package Todstoychev\CalendarEvents\Http\Requests
 * @author Eric Lagarda <eric@infinety.es>
 */
class EventRequest extends Request
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
            'title' => 'required|min:3|max:255',
            'description' => 'min:10|max:1000',
            // 'start' => 'required|date_format:'.config('calendar.formatDate', 'd-m-Y H:i'),
            'start' => 'required',
            'class' => 'sometimes|string',
        ];

        if (empty($this->input('all_day'))) {
            $rules['end'] = 'required';
        }

        return $rules;
    }
}