<?php

class ErrorExit
{
    public static function handleException(Throwable $exception): void
    {
        $code = 500;
        $exceptionCode = $exception->getCode();

        if (
            $exceptionCode === 400 ||
            $exceptionCode === 409 ||
            $exceptionCode === 422
        ) {
            $code = $exceptionCode;
        }

        http_response_code($code);

        echo json_encode(
            ["error" => [
                "message" => $exception->getMessage(),
                "file" => $exception->getFile(),
                "line" => $exception->getLine()
            ]]
        );
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): void
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
