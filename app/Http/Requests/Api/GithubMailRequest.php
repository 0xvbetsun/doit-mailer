<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GithubMailRequest extends FormRequest
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
        $this->sanitize();

        return [
            'usernames' => 'array|min:1',
            'usernames.*' => 'string|min:3',
            'massage' => 'required|string',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();

        $input['massage'] = filter_var($input['massage'], FILTER_SANITIZE_STRING);

        $this->replace($input);
    }
}
