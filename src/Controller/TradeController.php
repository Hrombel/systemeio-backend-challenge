<?php namespace App\Controller;

use App\Entity\DiscountCoupon;
use App\Entity\FixedDiscountCoupon;
use App\Entity\PercentDiscountCoupon;
use App\Entity\Product;
use App\Entity\Tax;
use App\Exception\NotImplementedException;
use App\Exception\ProductNotFoundTradeException;
use App\Exception\UnrecognizedTaxTradeException;
use App\Service\Payment\Gateway;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TradeController extends AbstractController {
    #[Route('/calculate-price', name: 'app_trade_calculate_price', methods: 'POST')]
    public function calculatePrice(Request $req, EntityManagerInterface $em): JsonResponse {
        $data = $req->toArray();

        $totalPrice = $this->_calculatePrice($data['product'], $data['taxNumber'], $data['couponCode'], $em);

        return $this->json([
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/purchase', name: 'app_trade_purchase', methods: 'POST')]
    public function purchase(Request $req, EntityManagerInterface $em, Gateway $paymentGateway): JsonResponse {
        $data = $req->toArray();

        $totalPrice = $this->_calculatePrice($data['product'], $data['taxNumber'], $data['couponCode'], $em);

        $processor = $paymentGateway->getPaymentSystem($data['paymentProcessor']);
        $processor->process(number_format($totalPrice, 2, '.'));

        return $this->json([
            'message' => 'OK',
        ]);
    }

    private function _calculatePrice(int $productId, string $taxNumber, string $couponCode, EntityManagerInterface $em): float {
        $productFQN = Product::class;
        $taxFQN = Tax::class;
        $discountCouponFQN = DiscountCoupon::class;

        try {
            $foundItem = $em->createQuery(
                "SELECT
                    p.price productPrice, 
                    t.percentValue taxValue,
                    c
                FROM
                    $productFQN p
                LEFT JOIN
                    $taxFQN t WITH SIMILAR_TO(:taxNumber, t.rule) = TRUE
                LEFT JOIN
                    $discountCouponFQN c WITH c.sellerId = p.sellerId AND c.code = :couponCode
                WHERE
                    p.id = :productId AND p.price IS NOT NULL
            ")
                ->setParameters([
                    'productId' => $productId,
                    'taxNumber' => $taxNumber,
                    'couponCode' => $couponCode,
                ])
                ->getSingleResult()
            ;
        } catch (NoResultException) {
            throw new ProductNotFoundTradeException('Product not found by productId given');
        }

        $price = (float) $foundItem['productPrice'];
        $taxValue = $foundItem['taxValue'] ?? null;
        $coupon = $foundItem[0];

        if (!$taxValue) {
            throw new UnrecognizedTaxTradeException('Invalid or unrecognized tax number given');
        }

        if (null !== $coupon && !in_array(get_class($coupon), [FixedDiscountCoupon::class, PercentDiscountCoupon::class])) {
            throw new NotImplementedException('Unrecognized coupon type found');
        }

        if ($coupon instanceof FixedDiscountCoupon) {
            $price -= $coupon->getExactValue();
        } elseif ($coupon instanceof PercentDiscountCoupon) {
            $price *= (100 - $coupon->getPercentValue()) / 100;
        }

        $price += $taxValue;

        return $price;
    }
}
