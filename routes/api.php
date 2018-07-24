<?php
declare(strict_types=1);

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
    Route::post('/login', 'SecurityController@login')->name('login');
    Route::post('/register', 'SecurityController@register');

    Route::middleware('auth:api')->group(function () {
        Route::post('/mail/github', 'MailController@byGithubUsernames');
    });
});

Route::fallback(function(){
    return response()->json(['message' => 'Not Found!'], 404);
});
