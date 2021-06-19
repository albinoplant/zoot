<?php
namespace App\EventListener;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionListener
{
    private KernelInterface $kernel;

    private SerializerInterface $serializer;

    private LoggerInterface $logger;

    public function __construct(KernelInterface $kernel, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->kernel = $kernel;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        switch (true) {
            case $exception instanceof InvalidDataException:
                $body = $this->serializer->serialize($exception->getErrors(), 'json');
                $event->setResponse(new JsonResponse($body, $statusCode, [], true));
                break;
            case $exception instanceof InvalidCliException:
                $body = $this->serializer->serialize([
                    'error' => true,
                    'description' => $exception->getMessage(),
                    'field' => $exception->getField()
                ], 'json');
                $event->setResponse(new JsonResponse($body, $statusCode, [], true));
                break;
            case $exception instanceof NotFoundHttpException && 404 === $exception->getStatusCode():
            case $exception instanceof BadRequestHttpException && 400 === $exception->getStatusCode():
            case $exception instanceof AccessDeniedHttpException && 403 === $exception->getStatusCode():
                $event->setResponse(new Response(null, $statusCode));
                break;
            default:
                $body = $this->serializer->serialize(in_array($this->kernel->getEnvironment(), ['dev', 'test'])
                    ? $this->exceptionResultForDebug($statusCode, $exception)
                    : [
                        'error_description' => "We can't perform the requested operation because $statusCode has appeared.",
                        'further_steps' => [
                            'Please contact IT support or Customer Support department for further instructions',
                        ],
                    ], 'json');
                $event->setResponse(new JsonResponse($body, $statusCode, [], true));
                break;
        }
    }

    private function exceptionResultForDebug($statusCode, $exception): array
    {
        return [
            'error' => true,
            'status' => $statusCode,
            'exception' => [
                'type' => is_object($exception) ? get_class($exception) : gettype($exception),
                'message' => $exception->getMessage(),
                'location' => $exception->getFile().' line '.$exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ],
        ];
    }
}
