<?php

namespace ClubSpeed\Mail;

class MailBuilder {

    // potential weirdness with public properties the same names as public functions ?
    // (php seems to handle this ok...)
    public $subject; 
    public $from;
    public $to;
    public $cc;
    public $bcc;
    public $body;
    public $alternate;

    public function __construct() {

    }

    public function subject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function from($from) {
        $this->from = $from;
        return $this;
    }

    public function to($to) {
        $this->to = $to;
        return $this;
    }

    public function cc($cc) {
        $this->cc = $cc;
        return $this;
    }

    public function bcc($bcc) {
        $this->bcc = $bcc;
        return $this;
    }

    public function body($body) {
        $this->body = $body;
        return $this;
    }

    public function alternate($alternate) {
        $this->alternate = $alternate;
        return $this;
    }

    // public function validate() {
    //     if (!isset($from) || empty($from))
    //         throw new \RequiredArgumentMissingException("MailBuilder does not have the 'email from' field set!");
    //     if (!isset($to) || empty($to))
    //         throw new \RequiredArgumentMissingException("MailBuilder does not have any 'email to' set!");
    // }
}