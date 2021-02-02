<?php

namespace App\EventListener;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

class ExceptionListener
{

    private $serializer;

    private $logger;


    public function __construct(SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->serializer = $serializer;
        $this->logger     = $logger;
    }


    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->hasResponse()) {
            return;
        }

        $exception = $event->getThrowable();

        switch (true) {
            case $exception instanceof ValidationFailedException:
                $event->setResponse(
                    new JsonResponse(
                        $this->serializer->serialize($exception->getViolations(), 'json'),
                        JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                        [],
                        true
                    )
                );
                break;
            case $exception instanceof BadRequestHttpException:
            case $exception instanceof ConflictHttpException:
                $event->setResponse(
                    new JsonResponse(
                        ['message' => ($exception->getMessage() ?? 'Unknown error.')],
                        ($exception->getStatusCode() ?? JsonResponse::HTTP_INTERNAL_SERVER_ERROR)
                    )
                );
                break;
            case $exception instanceof InvalidArgumentException:
                $event->setResponse(
                    new JsonResponse(
                        ['message' => ($exception->getMessage() ?? 'Bad request.')],
                        JsonResponse::HTTP_BAD_REQUEST
                    )
                );
                break;
            case $exception instanceof NotFoundHttpException:
            case $exception instanceof MethodNotAllowedHttpException:
                $event->setResponse(
                    new JsonResponse(
                        ['message' => 'Not found.'],
                        JsonResponse::HTTP_NOT_FOUND
                    )
                );
                break;
            case $exception instanceof UnprocessableEntityHttpException:
                $event->setResponse(
                    new JsonResponse(
                        ['message' => $exception->getMessage()],
                        JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                    )
                );
                break;
            case $exception instanceof AccessDeniedException:
                $event->setResponse(
                    new JsonResponse(
                        ['message' => $exception->getMessage()],
                        JsonResponse::HTTP_UNAUTHORIZED
                    )
                );
                break;
            case $exception instanceof HttpException:
            case $exception instanceof AccessDeniedHttpException:
                if (
                    $exception->getStatusCode() === Response::HTTP_UNAUTHORIZED ||
                    $exception->getStatusCode() === Response::HTTP_FORBIDDEN
                ) {
                    $event->setResponse(
                        new JsonResponse(
                            ['message' => $exception->getMessage()],
                            JsonResponse::HTTP_UNAUTHORIZED
                        )
                    );
                }

                break;
            default:
                return;
        }//end switch

        $this->logException($exception);
    }


    private function logException(Throwable $exception): void
    {
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);
    }
}
