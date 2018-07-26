<?php
declare(strict_types=1);

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

/**
 * Trait RestExceptionHandlerTrait
 * @package App\Traits
 */
trait RestExceptionHandlerTrait
{

    /**
     * Creates a new JSON response based on exception type.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e)
    {
        switch (true) {
            case $this->isBadRequest($e):
                $returnedValue = $this->badRequest($e);
                break;
            case $this->isNotAuthentificated($e):
                $returnedValue = $this->notAuthentificated();
                break;
            case $this->isModelNotFoundException($e):
                $returnedValue = $this->modelNotFound($e);
                break;
            case $this->isMethodNotAllowed($e):
                $returnedValue = $this->methodNotAllowed($e);
                break;
            case $this->isRequestValidationException($e):
                $returnedValue = $this->validationFailed($e);
                break;
            default:
                $returnedValue = $this->internalServerError($e);
        }

        return $returnedValue;
    }

    /**
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload = null, $statusCode = 200)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }

    /**
     * Returns json response for bad request exception.
     *
     * @param BadRequestHttpException $exception
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function badRequest($exception, $message = 'Bad Request', $statusCode = 400)
    {
        return $this->jsonResponse([
            'title' => $message,
            'detail' => $exception->getMessage(),
            'status' => $statusCode
        ], $statusCode);
    }

    /**
     * Returns json response for not authentificated error.
     *
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notAuthentificated($statusCode = 401)
    {
        return $this->jsonResponse([
            'title' => 'You are not authenticated in the system.',
            'detail' => 'Check if token exists in "Authorization" header',
            'status' => $statusCode
        ], $statusCode);
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param ModelNotFoundException $exception
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound($exception, $message = 'Record not found', $statusCode = 404)
    {
        return $this->jsonResponse([
            'title' => $message,
            'detail' => $exception->getMessage(),
            'status' => $statusCode
        ], $statusCode);
    }

    /**
     * Returns json response for EMethod Not Allowed exception.
     *
     * @param MethodNotAllowedHttpException $exception
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function methodNotAllowed($exception, $message = 'Method Not Allowed', $statusCode = 405)
    {
        return $this->jsonResponse([
            'title' => $message,
            'detail' => $exception->getHeaders(),
            'status' => $statusCode
        ], $statusCode);
    }

    /**
     * Returns json response for Validation exception.
     *
     * @param ValidationException $exception
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validationFailed($exception, $statusCode = 422)
    {
        return $this->jsonResponse([
            'title' => 'Validation Failed',
            'detail' => $exception->errors(),
            'status' => $statusCode
        ], $statusCode);
    }

    /**
     * Returns json response for internal server error.
     *
     * @param \Exception $exception
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function internalServerError($exception, $statusCode = 500)
    {
        return $this->jsonResponse([
            'title' => 'Internal Server Error',
            'detail' => $exception->getMessage(),
            'status' => $statusCode
        ], $statusCode);
    }

    /**
     * Determines if the given exception is bad request.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isBadRequest(Exception $e): bool
    {
        return $e instanceof BadRequestHttpException;
    }

    /**
     * Determines if the given exception if User not authentificated.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isNotAuthentificated(Exception $e): bool
    {
        return $e instanceof AuthenticationException;
    }

    /**
     * Determines if the given exception is an Eloquent model not found.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isModelNotFoundException(Exception $e): bool
    {
        return $e instanceof ModelNotFoundException;
    }

    /**
     * Determines if the given exception is an Eloquent model not found.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isMethodNotAllowed(Exception $e): bool
    {
        return $e instanceof MethodNotAllowedHttpException;
    }

    /**
     * Determines if the given exception is a Validation exception.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isRequestValidationException(Exception $e): bool
    {
        return $e instanceof ValidationException;
    }
}