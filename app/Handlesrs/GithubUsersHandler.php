<?php
declare(strict_types=1);

namespace App\Handlers;

use App\Traits\CurlTrait;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class GithubUsersHandler
 * @package App\Handlers
 */
class GithubUsersHandler
{
    use CurlTrait;

    private $usernames;
    private $validUsers = [];
    private $usersWithoutEmail = [];
    private $usersWithoutLocation = [];

    /**
     * GithubUsersHandler constructor.
     * @param $usernames
     */
    public function __construct(array $usernames)
    {
        $this->usernames = $usernames;
    }

    /**
     * @return array
     */
    public function handle()
    {
        foreach ($this->usernames as $username) {
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

            $this->validUsers[] = [
                'email' => $email,
                'location' => $basicDecodedData->location
            ];
        }

        $this->checkDispatchAbility($this->usersWithoutEmail, $this->usersWithoutLocation);

        return $this->validUsers;
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
     * @throws \Exception
     */
    private function checkDispatchAbility($usersWithoutEmail, $usersWithoutLocation): void
    {
        if ($usersWithoutEmail !== []) {
            throw new \Exception(sprintf(
                'Emails can\'t be sent because the user(s): "%s" didn\'t specify email field',
                implode(', ', $usersWithoutEmail)
            ));
        }

        if ($usersWithoutLocation !== []) {
            throw new \Exception(sprintf(
                'Emails can\'t be sent because the user(s): "%s" didn\'t specify email field', implode(', ', $usersWithoutEmail)
            ));
        }
    }
}