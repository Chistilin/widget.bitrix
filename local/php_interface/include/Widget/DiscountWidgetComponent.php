<?php

namespace Widget;

use Bitrix\Sale\Internals\DiscountCouponTable;
use CModule;
use CSaleDiscount;
use DateTime;
use Widget\ClientWidgetComponent;
use Bitrix\Main\Loader;

/*
 * $discountId = ID скидочной акции в системе Bitrix
 * $code = используемый промокод
 * $maxUse = максимальное количество раз для использования
 * $orderUse = 1 - промокод можно использовать в нескольких заках, 2 - в одном
 * $discountValue = процент скидки
 */

class DiscountWidgetComponent
{

    /**
     * ExecuteWidgetComponent constructor.
     * @param integer $discountId
     * @param string $code
     * @param integer $maxUse
     * @param $orderUse
     * @param $discountValue
     * @throws \Exception
     */
    public function __construct(
        $discountId,
        $code,
        $maxUse,
        $orderUse,
        $discountValue)
    {
        if (CModule::IncludeModule('sale')) {
            $this->setDiscountSale($discountId,$discountValue);
            DiscountCouponTable::add($this->generateFiledCoupon($discountId, $code, $maxUse, $orderUse));
        }
    }

    /**
     * @param $discountId
     * @param $code
     * @param $maxUse
     * @param $orderUse
     */
    private function generateFiledCoupon($discountId, $code, $maxUse, $orderUse = 1){
        $dateTime = new DateTime();

        return $fields[] = [
            'DISCOUNT_ID' => $discountId,
            'ACTIVE_FROM' => \Bitrix\Main\Type\DateTime::createFromTimestamp($dateTime->getTimestamp()),
            'ACTIVE_TO' => null,
            'TYPE' => $orderUse === 1 ? DiscountCouponTable::TYPE_MULTI_ORDER : DiscountCouponTable::TYPE_ONE_ORDER,
            'COUPON' => $code,
            'MAX_USE' => $maxUse,
        ];
    }

    private function setDiscountSale($discountId, $discount){
        $discountCollectionBitrix = CSaleDiscount::GetByID($discountId);

        $newDiscount = unserialize($discountCollectionBitrix["ACTIONS"]);
        $newDiscount['CHILDREN'][0]['DATA']["Value"] = floor($discount);

        $discountCollectionBitrix["ACTIONS"] = $newDiscount;

        return CSaleDiscount::Update($discountId,$discountCollectionBitrix);
    }


}
