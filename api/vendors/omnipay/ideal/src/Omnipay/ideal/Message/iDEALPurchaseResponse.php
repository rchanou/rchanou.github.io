<?php

namespace Omnipay\iDEAL\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class iDEALPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $liveCheckoutEndpoint = 'https://internetkassa.abnamro.nl/ncol/prod/orderstandard_utf8.asp';
    protected $testCheckoutEndpoint = 'https://internetkassa.abnamro.nl/ncol/test/orderstandard_utf8.asp';

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl()
    {
        return $this->getCheckoutEndpoint();
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return $this->data;
    }

    protected function getCheckoutEndpoint()
    {
        return $this->getRequest()->getTestMode() ? $this->testCheckoutEndpoint : $this->liveCheckoutEndpoint;
    }
}
