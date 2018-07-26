<?php
declare(strict_types=1);

namespace Tests\Feature\Common;

use Tests\TestCase;

/**
 * Class LoginTest
 * @package Tests\Feature\Common
 */
class LoginTest extends TestCase
{
    private $url = '/api/v1/login';
    private const CORRECT_RESPONSE_STRUCTURE = [
        'token',
        'avatar',
    ];

    public function testCorrectLogin()
    {
        $body = [
            'email' => 'admin@ukr.net',
            'password' => '12301230'
        ];

        $response = $this->json('POST', $this->url, $body);

        $response->assertStatus(200)
            ->assertJsonStructure(static::CORRECT_RESPONSE_STRUCTURE)
        ;
    }

    public function testInvalidEmailLogin()
    {
        $email = 'admin@ukr.net1';
        $body = [
            'email' => $email,
            'password' => '12301230'
        ];

        $response = $this->json('POST', $this->url, $body);

        $response->assertStatus(404)
            ->assertJson([
                'title' => 'Record not found',
                'detail' => sprintf('The user with email: "%s" doesn\'t exist!', $email),
                'status' => 404,
            ])
        ;
    }

    public function testInvalidPasswordLogin()
    {
        $body = [
            'email' => 'admin@ukr.net',
            'password' => '1230123'
        ];

        $response = $this->json('POST', $this->url, $body);

        $response->assertStatus(422)
            ->assertJson([
                'title' => 'Validation Failed',
                'detail' => [
                    'password' => ['Password is incorrect!']
                ],
                'status' => 422,
            ])
        ;
    }

    public function testLoginWithNotValidEmail()
    {
        $body = [
            'email' => $this->getInvalidField(),
            'password' => '1230123'
        ];

        $response = $this->json('POST', $this->url, $body);

        $response->assertStatus(422)
            ->assertJson([
                'title' => 'Validation Failed',
                'detail' => [
                    'email' => ['The email must be a valid email address.']
                ],
                'status' => 422,
            ])
        ;
    }

    public function testLoginWithEmptyData()
    {
        $body = [
            'email' => $this->getEmptyField(),
            'password' => $this->getEmptyField()
        ];

        $response = $this->json('POST', $this->url, $body);

        $response->assertStatus(422)
            ->assertJson([
                'title' => 'Validation Failed',
                'detail' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.']
                ],
                'status' => 422,
            ])
        ;
    }
}
