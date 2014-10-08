<?php

namespace ClubSpeed\Mail;

interface MailerInterface {
    public function __construct();
    public function send(MailBuilder $mail);
}