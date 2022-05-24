<?php
/*
 * File name: CreateAdvertRequest.php
 * Last modified: 2022.02.17 at 19:10
 * Author: Musa ATALAY - musaatalay.work@gmail.com
 */

namespace App\Http\Requests;

use App\Models\DriverDocuments;
use Illuminate\Foundation\Http\FormRequest;

class CreateDriverDocumentsRequest extends FormRequest
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
        return DriverDocuments::$rules;
    }
}
