<?php
/*
 * File name: UpdateAdvertRequest.php
 * Last modified: 2022.16.02 at 17:21
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Requests;

use App\Models\Advert;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdvertRequest extends FormRequest
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
        return Advert::$rules;
    }
}
