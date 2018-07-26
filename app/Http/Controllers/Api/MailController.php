<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\GithubMailRequest;
use App\Mail\WeatherForecast;
use App\Traits\CurlTrait;
use App\Http\Controllers\Controller;
use Mail;

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
        $users = [];
        $usersWithoutEmail = [];
        $usersWithoutLocation = [];

        foreach ($request->input('usernames') as $username) {
            list($basicDecodedData, $publicEventsDecodedData) = $this->prepareUserData($username);

            if ($this->isGithubUserNotExists($basicDecodedData)) {
                return response()->json([
                    'message' => sprintf('User with username "%s" doesn\'t exist on GitHub', $username)
                ], 400);
            }

            if ($this->isGithubUserWithoutEmail($publicEventsDecodedData)) {
                $usersWithoutEmail[] = $username;
            }

            if ($this->isGithubUserWithoutLocation($basicDecodedData)) {
                $usersWithoutLocation[] = $username;
            }

            $users[] = [
                'email' => $publicEventsDecodedData[0]->payload->commits[0]->author->email,
                'location' => $basicDecodedData->location
            ];
        }

        if ($usersWithoutEmail !== []) {
            return response()->json([
                'message' => sprintf('Emails can\'t be sent because the user(s): "%s" didn\'t specify email field', implode(', ', $usersWithoutEmail))
            ], 500);
        }

        if ($usersWithoutLocation !== []) {
            return response()->json([
                'message' => sprintf('Emails can\'t be sent because the user(s): "%s" didn\'t specify location field', implode(', ', $usersWithoutLocation))
            ], 500);
        }

        foreach ($users as $user) {
            Mail::to($user['email'])->send(new WeatherForecast($user['location'], $request->input('message')));
        }

        return response()->json(['message' => 'All emails were sent successfully']);
    }

    /**
     * @param $username
     * @return array
     */
    private function prepareUserData($username): array
    {
        $basicUrl = sprintf('https://api.github.com/users/%s', $username);
        $basicData = $this->sendGetRequest($basicUrl);
        $publicEventsData = $this->sendGetRequest($basicUrl . '/events/public');

        $basicDecodedData = json_decode($basicData);
        $publicEventsDecodedData = json_decode($publicEventsData);

        return [$basicDecodedData, $publicEventsDecodedData];
    }

    /**
     * @param $basicDecodedData
     * @return bool
     */
    private function isGithubUserNotExists($basicDecodedData): bool
    {
        return property_exists($basicDecodedData, 'message') &&
            $basicDecodedData->message === 'Not Found';
    }

    /**
     * @param $publicEventsDecodedData
     * @return bool
     */
    private function isGithubUserWithoutEmail($publicEventsDecodedData): bool
    {
        return !is_array($publicEventsDecodedData) ||
            !is_array($publicEventsDecodedData[0]->payload->commits);
    }

    /**
     * @param $basicDecodedData
     * @return bool
     */
    private function isGithubUserWithoutLocation($basicDecodedData): bool
    {
        return $basicDecodedData->location === null;
    }
}
