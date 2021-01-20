<?php

namespace Widget;

use Widget\ClientWidgetComponent;

class ExecuteWidgetComponent
{
    /**
     * @var ClientWidgetComponent
     */
    private $widgetDeals;

    /**
     * ExecuteWidgetComponent constructor.
     * @param \Widget\ClientWidgetComponent $widgetDeals
     * @param string $userId
     * @param string $token
     * @param string $userIp
     */
    public function __construct(
        ClientWidgetComponent $widgetDeals,
        $userId,
        $token,
        $userIp)
    {
        $this->widgetDeals = $widgetDeals;

        $this->widgetDeals->setUserId($userId);
        $this->widgetDeals->setToken($token);
        $this->widgetDeals->setUserIp($userIp);

        if(!empty($userId)){
            $this->widgetDeals->setUserId($userId);
            $this->widgetDeals->setToken($token);
            $this->widgetDeals->setUserIp($userIp);
        }
    }

    /**
     * @param string $code
     */
    public function clickWidget($code){
        return $this->widgetDeals->setCode($code)
            ->clickPromo()
            //->getWidgetReference()
            ->getResult();
    }

    /**
     * @param string $code
     */
    public function clickProlongationWidget($code){
        return $this->widgetDeals->setCode($code)
            ->prolongationPromo()
            ->getResult();
    }

    /**
     * @param string $code
     */
    public function usePromoCode($code){
        return $this->widgetDeals->setCode($code)
            ->usePromo()
            ->getResult();
    }
}
