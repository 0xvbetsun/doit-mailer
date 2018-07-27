<?php
declare(strict_types=1);

namespace App\Traits;

/**
 * Trait AuthSanitizerTrait
 * @package App\Traits
 */
trait AuthSanitizerTrait
{
    use InputCleaner;

    public function sanitize()
    {
        $input = $this->all();
	if ($this->filled('email')) {
            $input['email'] = $this->clean($input['email']);
        }
	if ($this->filled('password')) {
            $input['password'] = $this->clean($input['password']);
        }

        $this->replace($input);
    }
}
