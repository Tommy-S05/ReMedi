<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Respuesta exitosa estandarizada
     *
     * @param mixed|null $data Datos a devolver en la respuesta.
     * @param string $message Mensaje de éxito.
     * @param int $statusCode Código HTTP de la respuesta.
     * @return JsonResponse
     */
    protected function successResponse(mixed $data = null, string $message = 'OK', int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Respuesta de error estandarizada
     *
     * @param string $message Mensaje de error principal.
     * @param int $statusCode Código HTTP de la respuesta.
     * @param array|null $errors Array de errores específicos.
     * @return JsonResponse
     */
    protected function errorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST, ?array $errors = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    // --- Métodos Helper para Errores Específicos ---

    /**
     * Respuesta para errores de validación (422 Unprocessable Entity).
     *
     * @param array $errors Array de errores de validación.
     * @param string $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Validation Failed'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    /**
     * Respuesta para recurso no encontrado (404 Not Found).
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource Not Found'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Respuesta para errores de autorización (403 Forbidden).
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'This action is unauthorized.'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Respuesta para errores internos del servidor (500 Internal Server Error).
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function serverErrorResponse(string $message = 'Internal Server Error'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
