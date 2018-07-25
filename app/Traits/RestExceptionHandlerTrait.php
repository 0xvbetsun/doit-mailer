<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

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
     * Returns json response for internal server error.
     *
     * @param \Exception $exception
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function internalServerError($exception, $statusCode = 500)
    {
        return $this->jsonResponse(['error' => $exception->getMessage()], $statusCode);
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
        return $this->jsonResponse(['error' => $message], $statusCode);
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
     * Returns json response.
     *
     * @param array|null $payload
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function jsonResponse(array $payload = null, $statusCode = 404)
    {
        $payload = $payload ?: [];

        return response()->json($payload, $statusCode);
    }

    /**
     * Determines if the given exception is an Eloquent model not found.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isModelNotFoundException(Exception $e)
    {
        return $e instanceof ModelNotFoundException;
    }

    /**
     * Determines if the given exception is a Validation exception.
     *
     * @param Exception $e
     * @return bool
     */
    protected function isRequestValidationException(Exception $e)
    {
        return $e instanceof ValidationException;
    }
}