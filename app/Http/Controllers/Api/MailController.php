<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Mail;
use App\Mail\WeatherForecast;
use App\Handlers\GithubUsersHandler;
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
    /**
     * @param GithubMailRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws BadRequestHttpException
     * @throws InternalErrorException
     */
    public function byGithubUsernames(GithubMailRequest $request)
    {
        $message = $request->input('message');
        $usernames = $request->input('usernames');

        $githubUsersHandler = app()->make(GithubUsersHandler::class, compact('usernames'));
        $users = $githubUsersHandler->handle();

        $this->sendWeatherEmails($users, $message);

        return response()->json(['message' => 'All emails were sent successfully']);
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
