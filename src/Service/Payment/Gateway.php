<?php namespace App\Service\Payment;

use App\Service\Payment\Contract\PaymentSystemInterface;
use App\Service\Payment\Exception\NotFoundException;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class Gateway {
    public function __construct(
        #[TaggedLocator('app.paysystem')]
        private readonly ServiceLocator $locator,
    ) {
    }

    public function getPaymentSystem(string $type): PaymentSystemInterface {
        try {
            return $this->locator->get(__NAMESPACE__.'\\System\\'.ucfirst($type));
        } catch (NotFoundExceptionInterface) {
            throw new NotFoundException('Payment system not found');
        }
    }
}
