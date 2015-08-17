<?php

namespace Omnipay\iDEAL;

use Omnipay\Common\AbstractGateway;

//use Omnipay\Ideal\Message\IdealPurchaseRequest;

/*
 * Omnipay class for iDEAL offsite payments via ABN AMRO.
 *
 */

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'iDEAL';
    }

    public function getDefaultParameters()
    {
        $settings = array();
        $settings['pspId'] = ''; //Customer's account name
        $settings['currency'] = 'EUR';
        $settings['language'] = 'nl_NL'; //Used to localize offsite payment flow
        $settings['shaIn'] = ''; //Used to hash the SHASIGN -- must match what is in their account
        $settings['shaOut'] = '';
        $settings['testMode'] = '1';
        return $settings;
    }

    /*
     * Getters and Setters
     *
     */

    public function getPSPID()
    {
        return $this->getParameter('PSPID');
    }

    public function setPSPID($value)
    {
        return $this->setParameter('PSPID', $value);
    }

    public function getCurrency()
    {
        return $this->getParameter('CURRENCY');
    }

    public function setCurrency($value)
    {
        return $this->setParameter('CURRENCY', $value);
    }

    public function getLanguage()
    {
        return $this->getParameter('LANGUAGE');
    }

    public function setLanguage($value)
    {
        return $this->setParameter('LANGUAGE', $value);
    }

    public function getShaIn()
    {
        return $this->getParameter('SHAIN');
    }

    public function setShaIn($value)
    {
        return $this->setParameter('SHAIN', $value);
    }

    /*
     * API end-points
     *
     */

    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\iDEAL\Message\iDEALPurchaseRequest', $parameters);
    }

    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\iDEAL\Message\iDEALCompletePurchaseRequest', $parameters);
    }
}
