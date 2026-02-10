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
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            if ($exception->getPrevious() instanceof ValidationFailedException) {
                $validatorException = $exception->getPrevious();
            }
        }
        /** @var ValidationFailedException|null $validatorException */
        $isProd = 'prod' === $this->kernel->getEnvironment();

        $errors = null;

        if ($statusCode >= 500 && $isProd) {
            $message = 'Internal server error';
        } else {
            if ($validatorException) {
                $message = 'There is an error in your request. Check the "errors" field for extra info.';
                $errors = [];
                foreach ($validatorException->getViolations() as $e) {
                    $errors[$e->getPropertyPath()] = $e->getMessage();
                }
            } else {
                $message = $exception->getMessage();
            }
        }

        $resData = [
            'meta' => [
                'success' => false,
                'message' => $message,
            ],
        ];
        if ($errors) {
            $resData['meta']['errors'] = $errors;
        }

        $event->setResponse(new JsonResponse($resData, $statusCode));
    }

    public static function getSubscribedEvents(): array {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
