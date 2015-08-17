<?php

namespace Omnipay\iDEAL\Message;

use Omnipay\Common\Message\AbstractResponse;

class iDEALCompletePurchaseResponse extends AbstractResponse
{
    public function isSuccessful()
    {
        return $this->data['STATUS'] == 9; //This corresponds to a sale, not just an authorization
    }
}
