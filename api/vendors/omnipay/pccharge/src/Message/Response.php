<?php

namespace Omnipay\PCCharge\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * PCCharge Response
 */
class Response extends AbstractResponse
{
    public function isSuccessful()
    {
        return isset($this->data['Result']) && strtoupper($this->data['Result'] === 'CAPTURED') || strtoupper($this->data['Result'] === 'APPROVED');
    }

    public function getTransactionReference()
    {
        if (isset($this->data['TroutD'])) {
            return $this->data['TroutD'];
        }
    }

    // not used with PCCharge
    // public function getCardReference()
    // {
    //     if (isset($this->data['object']) && 'customer' === $this->data['object']) {
    //         return $this->data['id'];
    //     }
    // }

    public function getMessage()
    {
        if (!$this->isSuccessful()) {
            return ($this->data['ErrorDescription'] ?: $this->data['AuthorizationCode']) . ': ' . $this->data['Result'];
        }
    }
}
