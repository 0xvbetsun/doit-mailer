<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        \Artisan::call('db:seed');
    }

    protected function getLongField($length = 170)
    {
        return str_repeat("a", $length);
    }

    protected function getEmptyField()
    {
        return '';
    }

    protected function getInvalidField()
    {
        return 'asdasdasd';
    }
}
