<?php
declare(strict_types=1);

namespace Tests\Feature\Common;

use Tests\TestCase;

/**
 * Class RegisterTest
 * @package Tests\Feature\Common
 */
class RegisterTest extends TestCase
{
    private $url = '/api/v1/register';
    private const CORRECT_RESPONSE_STRUCTURE = [
        'id',
        'email',
        'token',
        'avatar',
    ];

    public function testCorrectRegister()
    {
        $email = 'user@ukr.net';
        $body = [
            'email' => $email,
            'password' => '12301230'
        ];

        $response = $this->json('POST', $this->url, $body);

        $response->assertStatus(201)
            ->assertJsonStructure(static::CORRECT_RESPONSE_STRUCTURE)
        ;

        $this->assertDatabaseHas('users', compact('email'));
    }

    public function testRestrictRegisterWithNotValidEmail()
    {
        $body = [
            'email' => $this->getInvalidField(),
            'password' => '12301230'
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

    public function testRestrictRegisterWithExistedEmail()
    {
        $body = [
            'email' => 'admin@ukr.net',
            'password' => '12301230'
        ];

        $response = $this->json('POST', $this->url, $body);

        $response->assertStatus(422)
            ->assertJson([
                'title' => 'Validation Failed',
                'detail' => [
                    'email' => ['The email has already been taken.']
                ],
                'status' => 422,
            ])
        ;
    }

    public function testRestrictRegisterWithEmptyData()
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
