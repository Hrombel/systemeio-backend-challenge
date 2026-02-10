<?php namespace App\Service\Payment;

use App\Service\Payment\Contract\PaymentSystemInterface;
use App\Service\Payment\Exception\NotFoundException;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

class Gateway {
    private ?array $fqnByType = null;

    public function __construct(
        #[TaggedLocator('app.paysystem')]
        private readonly ServiceLocator $locator,
    ) {
    }

    public function getPaymentSystem(string $type): PaymentSystemInterface {
        try {
            return $this->locator->get($this->byType()[$type] ?? '');
        } catch (NotFoundExceptionInterface) {
            throw new NotFoundException('Payment system not found');
        }
    }

    public function getPaySystemTypes(): array {
        return array_keys($this->byType());
    }

    /**
     * TODO: add a caching method.
     */
    private function byType(): array {
        if (null === $this->fqnByType) {
            $this->fqnByType = [];
            $items = $this->locator->getProvidedServices();
            foreach ($items as $id => $fqn) {
                /** @var PaymentSystemInterface $ps */
                $ps = $this->locator->get($id);
                $this->fqnByType[$ps->getType()] = $fqn;
            }
        }

        return $this->fqnByType;
    }
}
