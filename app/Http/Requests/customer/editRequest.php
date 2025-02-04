<?php

namespace App\Http\Requests\customer;

use Illuminate\Validation\Rule;
use App\Rules\customer\BirthYearRule;
use App\Rules\customer\numberPhoneRule;
use Illuminate\Foundation\Http\FormRequest;

/*
|--------------------------------------------------------------------------
| Rumah Dev
| Backend Developer : ibudirsan
| Email             : ibnudirsan@gmail.com
| Copyright © Raungdev 2022
|--------------------------------------------------------------------------
*/

class editRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'firstName'     => 'required|string|max:30',
            'lastName'      => 'required|string|max:30',
            'email'         => 'required|email:rfc,dns|max:25|unique:customers,email,'.$this->id.',uuid',
            'address'       => 'required|max:30',
            'Numberphone'   => ['required',
                                'numeric',
                                'digits_between:10,13',
                                new numberPhoneRule()],
            'birthDay'      => [ 'required',
                                 'date_format:Y-m-d',
                                 new BirthYearRule()
                                ],
        ];
    }
}
