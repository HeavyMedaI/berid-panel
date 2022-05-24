<?php
/*
 * File name: CreateAdvertStatusRequest.php
 * Last modified: 2021.01.25 at 22:00:21
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 * Copyright (c) 2022
 */

namespace App\Http\Requests;

use App\Models\AdvertStatus;
use Illuminate\Foundation\Http\FormRequest;

class CreateAdvertStatusRequest extends FormRequest
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
        return AdvertStatus::$rules;
    }
}
