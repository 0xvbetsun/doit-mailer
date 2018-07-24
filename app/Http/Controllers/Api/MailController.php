<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailController extends Controller
{
    public function byGithubUsernames()
    {
        return response()->json(['data' => 'all mails was sent']);
    }
}
