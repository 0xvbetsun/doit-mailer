<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\GithubMailRequest;
use App\Traits\CurlTrait;
use App\Http\Controllers\Controller;

/**
 * Class MailController
 * @package App\Http\Controllers\Api
 */
class MailController extends Controller
{
    use CurlTrait;

    /**
     * @param GithubMailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function byGithubUsernames(GithubMailRequest $request)
    {
        $emails = [];
        $usersWithoutEmail = [];
        $usersWithoutLocation = [];
        foreach ($request->input('usernames') as $username) {
            $url = sprintf('https://api.github.com/users/%s', $username);
            $response = $this->sendGetRequest($url);

            $decodedResponse = json_decode($response);

            if (property_exists($decodedResponse, 'message') && $decodedResponse->message === 'Not Found') {
                return response()->json([
                    'message' => sprintf('User with username "%s" doesn\'t exist on GitHub', $username)
                ], 400);
            }
            if ($decodedResponse->email === null) {
                $usersWithoutEmail[] = $username;
            }
            if ($decodedResponse->location === null) {
                $usersWithoutLocation[] = $username;
            }
            $emails[] = 'admin@ukr.net';
        }
        if ($usersWithoutEmail) {
            return response()->json([
                'message' => sprintf('Emails can\'t be sent because the user(s): "%s" didn\'t specify email field', implode(', ', $usersWithoutEmail))
            ], 500);
        }

        if ($usersWithoutLocation) {
            return response()->json([
                'message' => sprintf('Emails can\'t be sent because the user(s): "%s" didn\'t specify location field', implode(', ', $usersWithoutLocation))
            ], 500);
        }
        foreach ($emails as $email) {
            //@todo sent email
        }

        return response()->json(['message' => 'All emails was sent successfully']);
    }
}
