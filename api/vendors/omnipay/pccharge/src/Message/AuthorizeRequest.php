<?php

namespace Omnipay\PCCharge\Message;

/**
 * PCCharge Authorize Request
 */
class AuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'transactionId');

        $data = $this->getCardData(); // card data at the root level
        $data['AmountToCharge'] = $this->getAmount();
        $data['TaxAmount'] = $this->getTaxAmount();
        $data['CheckID'] = $this->getTransactionId();
        
        return $data;
    }

    public function getEndpoint()
    {
        return $this->endpoint.'/charges';
    }
}
