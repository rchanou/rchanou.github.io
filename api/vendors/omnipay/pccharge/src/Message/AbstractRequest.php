<?php

namespace Omnipay\PCCharge\Message;

/**
 * PCCharge Abstract Request
 *
 * @method \Omnipay\PCCharge\Message\Response send()
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    // protected $endpoint = 'https://' . $_SERVER['SERVER_NAME'] . '/WebAPI/V1.5/PCCharge'; // CANT DO CONCATENATION HERE -- fix before going to live
    protected $endpoint = 'https://vm-140.clubspeedtiming.com/WebAPI/V1.5/PCCharge'; // for test purposes

    abstract public function getEndpoint();

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getTaxAmount()
    {
        return $this->getParameter('taxAmount');
    }

    public function setTaxAmount($value)
    {
        return $this->setParameter('taxAmount', $value);
    }

    public function getCheckId()
    {
        return $this->getParameter('checkId');
    }

    public function setCheckId($value)
    {
        return $this->setParameter('checkId', $value);
    }

    public function getHttpMethod()
    {
        return 'POST';
    }

    public function sendData($data)
    {
        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getEndpoint(),
            null,
            $data // body ??
        );
        $httpResponse = $httpRequest
            ->setHeader('Authorization', 'Basic '.base64_encode($this->getUsername().':'.$this->getPassword()))
            ->send();

        pr($httpResponse->json());
        die();

        return $this->response = new Response($this, $httpResponse->json());
    }

    protected function getCardData()
    {
        $this->getCard()->validate();

        $data['CreditCardNo']   = $this->getCard()->getNumber();
        $data['AccountName']    = $this->getCard()->getName();
        $data['ExpirationDate'] = $this->getCard()->getExpiryDate('my');
        $data['Zip']            = $this->getCard()->getPostcode();
        $data['Address']        = $this->getCard()->getAddress1();
        $data['CVV']            = $this->getCard()->getCvv();
        $data['CardIssuer']     = ''; // unknown?
        $data['TaxExempt']      = false; // override to false
        $data['IsCommercial']   = false;

        return $data;
    }
}
