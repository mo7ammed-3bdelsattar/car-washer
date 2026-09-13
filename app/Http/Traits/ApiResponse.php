<?php

namespace App\Http\Traits;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Send a successful response.
     */
    protected function successResponse(
        mixed $data = null,
        string $message = null,
        int $code = Response::HTTP_OK
    ): JsonResponse {
        $message = $message ?? __('Success');
        return response()->json([
            'success' => true,
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Send an error response.
     */
    protected function errorResponse(
        string $message = null,
        int $code = Response::HTTP_BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        $message = $message ?? __('Error');
        $response = [
            'success' => false,
            'status' => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Send a validation error response.
     */
    protected function validationErrorResponse(
        mixed $errors,
        string $message = null
    ): JsonResponse {
        $message = $message ?? __('Validation failed');
        return $this->errorResponse(
            $message,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $errors
        );
    }

    /**
     * Send a not found response.
     */
    protected function notFoundResponse(
        string $message = null
    ): JsonResponse {
        $message = $message ?? __('Resource not found');
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Send an unauthorized response.
     */
    protected function unauthorizedResponse(
        string $message = null
    ): JsonResponse {
        $message = $message ?? __('Unauthorized');
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Send a forbidden response.
     */
    protected function forbiddenResponse(
        string $message = null
    ): JsonResponse {
        $message = $message ?? __('Forbidden');
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Send a created response.
     */
    protected function createdResponse(
        mixed $data = null,
        string $message = null
    ): JsonResponse {
        $message = $message ?? __('Resource created successfully');
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Send a deleted response.
     */
    protected function deletedResponse(
        string $message = null
    ): JsonResponse {
        $message = $message ?? __('Resource deleted successfully');
        return $this->successResponse(null, $message);
    }

    /**
     * Send a no content response.
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Send a paginated response.
     */
    protected function paginatedResponse(
        mixed $paginator,
        string $message = null,
        $code = 200
    ): JsonResponse {
        $message = $message ?? __('Success');
        if ($paginator->total() > $paginator->perPage()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $code,
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'from' => $paginator->firstItem(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'to' => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ],
                'links' => [
                    'first' => $paginator->url(1),
                    'last' => $paginator->url($paginator->lastPage()),
                    'prev' => $paginator->previousPageUrl(),
                    'next' => $paginator->nextPageUrl(),
                ],
            ], $code);
        } else {
            return $this->successResponse($paginator, $message, $code);
        }
    }
}
