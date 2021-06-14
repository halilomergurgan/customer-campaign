<?php

namespace App\Responses;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiResponse implements Responsable
{
    public const EXCEPTION_MESSAGES = [
        'VALIDATION' => 'validation_error',
        'MODEL_NOT_FOUND' => 'model_not_found',
        'QUERY_EXCEPTION' => 'query_exception',
        'exception' => 'error',
    ];
    public const SUCCESS_MESSAGE = 'success';
    public const ERROR_MESSAGE = 'error';
    public const SUCCESS_CODE = 200;
    public const VALIDATION_ERROR_CODE = 422;
    public const ERROR_CODE = 400;

    public array $headers = [];
    public array $response = [];

    private bool $status = false;
    private ?int $statusCode = null;
    private ?string $message = null;
    private array $errors = [];
    private array $jsonErrors = [];
    private array $data = [];

    /**
     * ApiResponse constructor.
     * @param  int  $code
     */
    public function __construct($code = self::ERROR_CODE)
    {
        $this->code($code);

        return $this;
    }

    /**
     * @param  int|null  $code
     * @return ApiResponse
     */
    public function code(?int $code): ApiResponse
    {
        $this->statusCode = $code;

        return $this;
    }

    /**
     * @param  array  $headers
     * @return ApiResponse
     */
    public function headers(array $headers = []): ApiResponse
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * @param  bool  $status
     * @return ApiResponse
     */
    public function status(bool $status = false)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param  string|null  $message
     * @return ApiResponse
     */
    public function message(?string $message = null)
    {
        $this->message = Str::lower(Str::snake($message));

        return $this;
    }

    /**
     * @param  array|null  $data
     * @return ApiResponse
     */
    public function data(?array $data = null): ApiResponse
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * @param  array|null  $error
     * @return ApiResponse
     */
    public function errors(?array $error = []): ApiResponse
    {
        $this->errors = array_merge($this->errors, $error);

        return $this;
    }

    /**
     * @param  array  $jsonCompatibleArray
     * @return ApiResponse
     */
    public function jsonErrors(array $jsonCompatibleArray = []): ApiResponse
    {
        $this->jsonErrors = array_merge($this->jsonErrors, $jsonCompatibleArray);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        $this->prepare();

        return $this->statusCode;
    }

    /**
     * @return void
     */
    public function prepare(): void
    {
        $response = [
            'status' => false,
            'code' => null,
            'message' => null,
        ];

        if (!empty($this->data)) {
            $this->message = self::SUCCESS_MESSAGE;
            $response['data'] = $this->data;
            $this->statusCode = self::SUCCESS_CODE;
        } else {
            $this->statusCode = $this->statusCode ? $this->statusCode : self::ERROR_CODE;
        }

        if (!empty($this->errors)) {
            $response['errors'] = $this->errors;
            $response['json_errors'] = $this->jsonErrors;
            $this->message = $this->message ? $this->message : self::ERROR_MESSAGE;
        }

        $this->status = $this->statusCode == self::SUCCESS_CODE;

        if (!$this->message && $this->status) {
            $this->message = self::SUCCESS_MESSAGE;
        }

        $response['status'] = $this->status;
        $response['message'] = $this->message ?? self::ERROR_MESSAGE;
        $response['code'] = $this->statusCode ?? self::ERROR_CODE;
        $this->response = $response;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function toResponse($request): Response
    {
        $this->prepare();

        return response()->json($this->response, $this->statusCode, $this->headers);
    }

    /**
     * @param  array  $errors
     * @return ApiResponse
     */
    public function validationError(array $errors = []): ApiResponse
    {
        $this->message = self::EXCEPTION_MESSAGES['VALIDATION'];
        $this->statusCode = self::VALIDATION_ERROR_CODE;
        $this->errors = $errors;
        $this->jsonErrors = $errors;
        $this->status = false;

        return $this;
    }

    /**
     * @return ApiResponse
     */
    public function success(): ApiResponse
    {
        $this->message = self::SUCCESS_MESSAGE;
        $this->statusCode = self::SUCCESS_CODE;
        $this->status = true;

        return $this;
    }

    /**
     * @return ApiResponse
     */
    public function error(): ApiResponse
    {
        $this->message = self::ERROR_MESSAGE;
        $this->statusCode = self::ERROR_CODE;
        $this->status = false;

        return $this;
    }
}
