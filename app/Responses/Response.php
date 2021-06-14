<?php

namespace App\Responses;

class Response
{
    public const SUCCESS_MESSAGE = 'success';
    public const ERROR_MESSAGE = 'error';
    public const SUCCESS_CODE = 200;
    public const ERROR_CODE = 400;

    /**
     * @param array|null $data
     * @return array
     */
    public static function data(?array $data): array
    {
        $response = [
            'status' => false,
            'code' => null,
            'message' => null,
        ];

        if (!empty($data)) {
            $response['status'] = true;
            $response['code'] = self::SUCCESS_CODE;
            $response['message'] = self::SUCCESS_MESSAGE;
            $response['data'] = $data;
        } else {
            $response['status'] = false;
            $response['code'] = self::ERROR_CODE;
            $response['message'] = self::ERROR_MESSAGE;
        }

        return $response;
    }

    /**
     * @param string|null $message
     * @return array
     */
    public static function success(string $message = null): array
    {
        return [
            'status' => true,
            'code' => self::SUCCESS_CODE,
            'message' => $message ?? self::SUCCESS_MESSAGE,
        ];
    }

    /**
     * @param string|null $message
     * @return array
     */
    public static function error(string $message = null): array
    {
        return [
            'status' => false,
            'code' => self::ERROR_CODE,
            'message' => $message ?? self::ERROR_MESSAGE,
        ];
    }
}
