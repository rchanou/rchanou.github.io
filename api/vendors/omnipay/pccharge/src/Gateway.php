<?php

namespace Omnipay\PCCharge;

use Omnipay\Common\AbstractGateway;
use Omnipay\PCCharge\Message\PurchaseRequest;
use Omnipay\PCCharge\Message\RefundRequest;

/**
 * PCCharge Gateway
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'PCCharge';
    }

    public function getDefaultParameters()
    {
        return array(
            'username' => '',
            'password' => ''
        );
    }

    public function getUsername() {
        return $this->getParameter('username');
    }

    public function setUsername($value) {
        return $this->setParameter('username', $value);
    }

    public function getPassword() {
        return $this->getParameter('password');
    }

    public function setPassword($value) {
        return $this->setParameter('password', $value);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\PCCharge\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\PCCharge\Message\PurchaseRequest', $parameters);
    }
}
