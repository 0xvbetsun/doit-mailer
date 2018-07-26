<?php
declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Traits\InputCleaner;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GithubMailRequest
 * @package App\Http\Requests\Api
 */
class GithubMailRequest extends FormRequest
{
    use InputCleaner;

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
            'usernames' => 'required',
            'usernames.*' => 'required|string|min:3',
            'message' => 'required|string',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'usernames.*.required' => 'No one username in the list can\'t be empty',
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        if ($this->filled('usernames')) {
            if (!is_array($input['usernames'])) {
                $input['usernames'] = [$this->clean($input['usernames'])];
            } else {
                $usernames = [];
                foreach ($input['usernames'] as $username) {
                    $usernames[] = $this->clean($username);
                }
                $input['usernames'] = $usernames;
            }
        }

        if ($this->filled('message')) {
            $input['message'] = $this->clean($input['message']);
        }

        $this->replace($input);
    }
}
