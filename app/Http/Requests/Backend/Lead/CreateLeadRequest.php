<?php

namespace App\Http\Requests\Backend\Lead;

use App\Http\Requests\Request;

/**
 * Class CreateLeadRequest.
 */
class CreateLeadRequest extends Request
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
            'name'          => 'required|max:255',
            'phone'         => 'required|unique_phone',
            'country_code'  => 'required',
            'call_date'     => 'required_if:assign,1|date_format:"d/m/Y"',
            'assigned_to'   => 'required_if:assign,1',
        ];
    }
}
