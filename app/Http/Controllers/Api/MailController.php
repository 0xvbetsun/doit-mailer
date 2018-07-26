<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Mail;
use App\Traits\CurlTrait;
use App\Mail\WeatherForecast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GithubMailRequest;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @throws BadRequestHttpException
     * @throws InternalErrorException
     */
    public function byGithubUsernames(GithubMailRequest $request)
    {
        $users = $usersWithoutEmail = $usersWithoutLocation = [];
        $message = $request->input('message');

        foreach ($request->input('usernames') as $username) {
            list($basicDecodedData, $publicEventsDecodedData) = $this->prepareUserData($username);

            if ($this->isGithubUserNotExists($basicDecodedData)) {
                throw new BadRequestHttpException(sprintf(
                    'User with username "%s" doesn\'t exist on GitHub',
                    $username
                ));
            }

            $email = $this->getEmailFromDecodedData($basicDecodedData, $publicEventsDecodedData);
            if (!$email) {
                $usersWithoutEmail[] = $username;
            }

            if ($this->isGithubUserWithoutLocation($basicDecodedData)) {
                $usersWithoutLocation[] = $username;
            }

            $users[] = [
                'email' => $email,
                'location' => $basicDecodedData->location
            ];
        }

        $this->checkDispatchAbility($usersWithoutEmail, $usersWithoutLocation);

        $this->sendWeatherEmails($users, $message);

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
     * @param $basicData
     * @param $publicEventsData
     * @return string|null
     */
    private function getEmailFromDecodedData($basicData, $publicEventsData)
    {
        if ($basicData->email !== null) {
            return $basicData->email;
        }

        foreach ($publicEventsData as $data) {
            if (property_exists($data->payload, 'commits')) {

                foreach ($data->payload->commits as $commit) {
                    if ($commit->author->email !== null) {
                        return $commit->author->email;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param $basicDecodedData
     * @return bool
     */
    private function isGithubUserWithoutLocation($basicDecodedData): bool
    {
        return $basicDecodedData->location === null;
    }

    /**
     * @param array $usersWithoutEmail
     * @param array $usersWithoutLocation
     * @throws InternalErrorException
     */
    private function checkDispatchAbility($usersWithoutEmail, $usersWithoutLocation): void
    {
        if ($usersWithoutEmail !== []) {
            throw new InternalErrorException(sprintf(
                'Emails can\'t be sent because the user(s): "%s" didn\'t specify email field',
                implode(', ', $usersWithoutEmail)
            ));
        }

        if ($usersWithoutLocation !== []) {
            throw new InternalErrorException(sprintf(
                'Emails can\'t be sent because the user(s): "%s" didn\'t specify email field', implode(', ', $usersWithoutEmail)
            ));
        }
    }

    /**
     * @param array $users
     * @param string $message
     */
    private function sendWeatherEmails(array $users, string $message): void
    {
        foreach ($users as $user) {
            Mail::to($user['email'])->send(new WeatherForecast($user['location'], $message));
        }
    }
}
