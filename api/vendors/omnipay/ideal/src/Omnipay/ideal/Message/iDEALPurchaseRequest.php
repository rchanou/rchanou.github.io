<?php

namespace Omnipay\iDEAL\Message;

use Omnipay\Common\Message\AbstractRequest;

class iDEALPurchaseRequest extends AbstractRequest
{

    public function getCurrency()
    {
        return $this->getParameter('currency');
    }

    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    public function setLanguage($value)
    {
        return $this->setParameter('language', $value);
    }

    public function getOrderId()
    {
        return $this->getParameter('transactionId');
    }

    public function setOrderId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    public function getPSPId()
    {
        return $this->getParameter('pspId');
    }

    public function setPSPId($value)
    {
        return $this->setParameter('pspId', $value);
    }

    public function getShaIn()
    {
        return $this->getParameter('shaIn');
    }

    public function setShaIn($value)
    {
        return $this->setParameter('shaIn', $value);
    }

    public function getData()
    {
        $this->validate('amount', 'cancelUrl', 'currency', 'language', 'pspId', 'returnUrl', 'shaIn', 'transactionId');

        $data = array();
        $data['ACCEPTURL'] = $this->getReturnUrl();
        $data['AMOUNT'] = $this->getAmountInteger();
        $data['CANCELURL'] = $this->getCancelUrl();
        $data['CURRENCY'] = $this->getCurrency();
        $data['DECLINEURL'] = $this->getReturnUrl();
        $data['EXCEPTIONURL'] = $this->getReturnUrl();
        $data['LANGUAGE'] = $this->getLanguage();
        $data['ORDERID'] = $this->getOrderId();
        $data['PSPID'] = $this->getPSPId();
        $data['SHA-IN'] = $this->getShaIn();
        $data['SHASIGN'] = $this->createShaSign();

        return $data;
    }

    /*
     * "SHASIGN" is computed by concatenating most (see URL) passed in parameters and their values, using "SHA-IN" as the glue between each item.
        The items must be in alphabetical order, with parameter labels in all caps, followed by an equal sign, followed by the value.
        Empty items are to be skipped.
        Reference: https://support-internetkassa-abnamro.v-psp.com/~/media/kdb/integration%20guides/sha-in_params.ashx?la=en
        We require that the user's ABN AMRO account is set to have the hashing mechanism set to SHA1.
     */
    public function createShaSign()
    {
        $arrayToHash = array (
            'ACCEPTURL=' . $this->getReturnUrl(),
            'AMOUNT=' . $this->getAmountInteger(),
            'CANCELURL=' . $this->getCancelUrl(),
            'CURRENCY=' . $this->getCurrency(),
            'DECLINEURL=' . $this->getReturnUrl(),
            'EXCEPTIONURL=' . $this->getReturnUrl(),
            'LANGUAGE=' . $this->getLanguage(),
            'ORDERID=' . $this->getOrderId(),
            'PSPID=' . $this->getPSPId()
        );

        $stringGlue = $this->getShaIn();

        $stringToHash = implode($stringGlue,$arrayToHash) . $stringGlue;
        $hash = strtoupper(sha1($stringToHash));

        return $hash;
    }

    public function sendData($data)
    {
        return $this->createResponse($data);
    }

    protected function createResponse($data)
    {
        return $this->response = new iDEALPurchaseResponse($this, $data);
    }


}
