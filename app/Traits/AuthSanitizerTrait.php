<?php
declare(strict_types=1);

namespace App\Traits;

trait AuthSanitizerTrait
{
    public function sanitize()
    {
        $input = $this->all();

        $input['email'] = filter_var($input['email'], FILTER_SANITIZE_STRING);
        $input['password'] = filter_var($input['password'],
            FILTER_SANITIZE_STRING);

        $this->replace($input);
    }
}