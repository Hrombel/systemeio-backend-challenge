<?php namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiExceptionSubscriber implements EventSubscriberInterface {

    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
    }

    public function onExceptionEvent(ExceptionEvent $event): void {
        
        $exception = $event->getThrowable();
        $validatorException = null;

        $statusCode = 500;
        if($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            if($exception->getPrevious() instanceof ValidationFailedException) {
                $validatorException = $exception->getPrevious();
            }
        }
        /** @var null|ValidationFailedException $validatorException */

        $isProd = $this->kernel->getEnvironment() === 'prod';

        if($statusCode >= 500 && $isProd) {
            $message = 'Internal server error';
        }
        else if($validatorException) {
            # TODO: Make errors presented in real response array structure
            $message = implode(
                "\n",
                array_map(
                    static fn ($v) => sprintf(
                        '%s: %s',
                        $v->getPropertyPath(),
                        $v->getMessage()
                    ),
                    iterator_to_array($validatorException->getViolations())
                )
            );
        }
        else {
            $message = $exception->getMessage();
        }

        $response = new JsonResponse(
            [
                'meta' => [
                    'success' => false,
                    'message' => $message,
                ],
            ],
            $statusCode
        );

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
