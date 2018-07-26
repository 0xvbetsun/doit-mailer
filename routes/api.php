<?php
declare(strict_types=1);

use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     host="doit-task.loc",
 *     basePath="/api/v1/",
 *     @SWG\SecurityScheme(
 *         securityDefinition="default",
 *         type="apiKey",
 *         name="Authorization",
 *         in="header",
 *         description="
           For accessing the API a valid token must be passed
           in the desired queries. The following syntax must
           be used in the 'Authorization' header :

           Bearer: {{ token }} "
 *     ),
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="DOIT Test Task Mailer API",
 *         description="REST API implementation based on the requirements described in technical specification",
 *         termsOfService="",
 *         @SWG\Contact(
 *             email="vlad.betcun@gmail.com"
 *         ),
 *     ),
 *     @SWG\Definition(
 *            definition="LoginRequest",
 *            required={"email", "password"},
 * 			@SWG\Property(property="email", type="string", format="email"),
 * 			@SWG\Property(property="password", type="string", format="password"),
 *        ),
 *     @SWG\Definition(
 *            definition="GithubMailRequest",
 *            required={"usernames", "message"},
 * 			@SWG\Property(
 *              property="usernames",
 *              type="array",
 *              @SWG\Items(type="string")
 *          ),
 * 			@SWG\Property(property="message", type="string"),
 *        ),
 *     @SWG\Definition(
 *            definition="LoginResponse",
 * 			@SWG\Property(property="token", type="string"),
 * 			@SWG\Property(
 *              property="avatar",
 *              @SWG\Property(property="main", type="string"),
 *              @SWG\Property(property="thumbnail", type="string"),
 *          ),
 *        ),
 *     @SWG\Definition(
 *            definition="RegisterResponse",
 * 			@SWG\Property(property="id", type="number"),
 * 			@SWG\Property(property="email", type="string"),
 * 			@SWG\Property(property="token", type="string"),
 * 			@SWG\Property(
 *              property="avatar",
 *              @SWG\Property(property="main", type="string"),
 *              @SWG\Property(property="thumbnail", type="string"),
 *          ),
 *        ),
 *     @SWG\Definition(
 *            definition="Error",
 * 			@SWG\Property(property="message", type="string"),
 *        ),
 * )
 */
Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
    /**
     * @SWG\Post(
     *      path="/login",
     *      operationId="apiLogin",
     *      tags={"Security"},
     *      summary="Sign in  user in the system",
     *      description="Returns token and link to the avatar",
     *      @SWG\Parameter(
     *            name="Body",
     *            in="body",
     *            required=true,
     * 			@SWG\Schema(ref="#/definitions/LoginRequest"),
     *        ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(ref="#/definitions/LoginResponse"),
     *       ),
     *       @SWG\Response(
     *          response=400,
     *          description="Bad Request",
     *          @SWG\Schema(ref="#/definitions/Error"),
     *       ),
     *       @SWG\Response(
     *          response=422,
     *          description="Validation Error",
     *          @SWG\Schema(ref="#/definitions/Error"),
     *        ),
     *     )
     */
    Route::post('login', 'SecurityController@login')->name('api_login');
    /**
     * @SWG\Post(
     *      path="/register",
     *      operationId="apiRegister",
     *      tags={"Security"},
     *      summary="Sign up user in the system",
     *      description="Returns all user data",
     *      @SWG\Parameter(
     *          name="email",
     *          in="formData",
     *          description="Email address",
     *          required=true,
     *          type="string",
     *          format="email"
     *       ),
     *      @SWG\Parameter(
     *          name="password",
     *          in="formData",
     *          description="Password",
     *          required=true,
     *          type="string",
     *          format="password"
     *       ),
     *      @SWG\Parameter(
     *          name="avatar",
     *          in="formData",
     *          description="Avatar image",
     *          required=false,
     *          type="file",
     *       ),
     *      @SWG\Response(
     *          response=200,
     *          description="Successful Operation",
     *          @SWG\Schema(ref="#/definitions/RegisterResponse"),
     *       ),
     *       @SWG\Response(
     *          response=400,
     *          description="Bad Request",
     *          @SWG\Schema(ref="#/definitions/Error"),
     *       ),
     *       @SWG\Response(
     *          response=422,
     *          description="Validation Error",
     *          @SWG\Schema(ref="#/definitions/Error"),
     *        ),
     *       @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(ref="#/definitions/Error"),
     *        ),
     *     )
     */
    Route::post('register', 'SecurityController@register')->name('api_register');
    /**
     * @SWG\Post(
     *      path="/mail/github",
     *      operationId="mailGithub",
     *      tags={"Mail"},
     *      summary="Emails to GitHub Users",
     *      description="Sending emails to GitHub Users by their usernames",
     *     security={{"default": {}}},
     *      @SWG\Parameter(
     *            name="Body",
     *            in="body",
     *            required=true,
     * 			@SWG\Schema(ref="#/definitions/GithubMailRequest"),
     *        ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(ref="#/definitions/LoginResponse"),
     *       ),
     *       @SWG\Response(
     *          response=400,
     *          description="Bad Request",
     *          @SWG\Schema(ref="#/definitions/Error"),
     *       ),
     *       @SWG\Response(
     *          response=422,
     *          description="Validation Error",
     *          @SWG\Schema(ref="#/definitions/Error"),
     *        ),
     *     ),
     */
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('mail/github', 'MailController@byGithubUsernames');
    });
});
