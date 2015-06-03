<?php

namespace ClubSpeed\Mail;

interface MailerInterface {
    public function __construct(Array $settings);
    public function send(MailBuilder $mail);
}