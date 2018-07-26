<?php
declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Traits\AuthSanitizerTrait;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RegisterRequest
 * @package App\Http\Requests\Api
 */
class RegisterRequest extends FormRequest
{
    use AuthSanitizerTrait;

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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:255',
            'avatar' => 'image',
        ];
    }
}
