<?php
declare(strict_types=1);

namespace App\Traits;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

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
            case $this->isNotAuthentificated($e):
                $returnedValue = $this->notAuthentificated();
                break;
            case $this->isModelNotFoundException($e):
                $returnedValue = $this->modelNotFound();
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
     * Returns json response for not authentificated error.
     *
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function notAuthentificated($statusCode = 401)
    {
        return $this->jsonResponse(['message' => 'You are not authenticated in the system.'], $statusCode);
    }

    /**
     * Returns json response for Eloquent model not found exception.
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function modelNotFound($message = 'Record not found', $statusCode = 404)
    {
        return $this->jsonResponse(['message' => $message], $statusCode);
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
            'message' => 'Validation Failed',
            'errors' => $exception->errors()
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
        return $this->jsonResponse(['message' => $exception->getMessage()], $statusCode);
    }

    /**
     * Determines if the given exception if User not authentificated .
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