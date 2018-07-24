<?php
declare(strict_types=1);

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
    Route::post('login', 'SecurityController@login')->name('api_login');
    Route::post('register', 'SecurityController@register')->name('api_register');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('mail/github', 'MailController@byGithubUsernames');
    });
});
