<?php

namespace Omnipay\iDEAL\Message;

use Omnipay\Common\Message\AbstractRequest;

class iDEALCompletePurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        return $this->httpRequest->query->all();
    }

    public function sendData($data)
    {
        return $this->createResponse($data);
    }

    protected function createResponse($data)
    {
        return $this->response = new iDEALCompletePurchaseResponse($this, $data);
    }

}
