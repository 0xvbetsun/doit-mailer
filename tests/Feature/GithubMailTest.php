<?php
declare(strict_types=1);

namespace Tests\Feature\Common;

use App\Models\User;
use Tests\TestCase;

/**
 * Class GithubMailTest
 * @package Tests\Feature\Common
 */
class GithubMailTest extends TestCase
{
    private $url;
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->url = '/api/v1/mail/github';
        $this->user = (new User)->find(1);
    }

    private const CORRECT_RESPONSE_STRUCTURE = [
        'message',
    ];

    public function testSendEmailsByUsernames()
    {
        $headers = ['Authorization' => "Bearer {$this->user->api_token}"];
        $body = [
            'usernames' => ['vbetsun'],
            'message' => 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit..'
        ];

        $response = $this->json('POST', $this->url, $body, $headers);

        $response->assertStatus(200)
            ->assertJsonStructure(static::CORRECT_RESPONSE_STRUCTURE)
        ;
    }

    public function testRestrictSendEmailsWithoutAuth()
    {
        $response = $this->json('POST', $this->url);

        $response->assertStatus(401)
            ->assertJson([
                'title' => 'You are not authenticated in the system.',
                'detail' => 'Check if token exists in "Authorization" header',
                'status' => 401,
            ]);
    }

    public function testRestrictSendEmailsWithEmptyData()
    {
        $headers = ['Authorization' => "Bearer {$this->user->api_token}"];
        $body = [
            'usernames' => $this->getEmptyField(),
            'message' => $this->getEmptyField()
        ];

        $response = $this->json('POST', $this->url, $body, $headers);

        $response->assertStatus(422)
            ->assertJson([
                'title' => 'Validation Failed',
                'detail' => [
                    'usernames' => ['The usernames field is required.'],
                    'message' => ['The message field is required.']
                ],
                'status' => 422,
            ])
        ;
    }
}
