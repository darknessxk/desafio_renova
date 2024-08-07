<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function __construct() { }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $rawMessage = $exception->getMessage();

        if (in_array($rawMessage[0], ['[', '{'])) {
            $parsedMessage = json_decode($rawMessage);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $parsedMessage = $rawMessage;
            }
        } else {
            $parsedMessage = $rawMessage;
        }

        $responseData = [
            'error' => $parsedMessage
        ];

        if ($_ENV['APP_ENV'] === 'dev') {
            $responseData['trace'] = $exception->getTrace();
            $responseData['instance'] = get_class($exception);
        }

        $response = new JsonResponse($responseData);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

            if ($_ENV['APP_ENV'] === 'dev') {
                $response->setData($responseData);
            } else {
                $response->setData(['error' => 'Internal Server Error']);
            }
        }

        $event->setResponse($response);
    }
}