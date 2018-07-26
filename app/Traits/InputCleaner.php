<?php
declare(strict_types=1);

namespace App\Traits;

/**
 * Trait InputCleaner
 * @package App\Traits
 */
trait InputCleaner
{
    /**
     * @param string|null $string
     * @return string
     */
    private function clean($string): string
    {
        return trim(filter_var($string, FILTER_SANITIZE_STRING));
    }
}