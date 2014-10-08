<?php

namespace ClubSpeed\Mail;

class SwiftMailer implements MailerInterface {

    private $transport;
    private $mailer;

    public function __construct() {
        $this->init();
    }

    private function init() {
        $settings = MailService::$settings;
        if (!isset($settings['SMTPServerPort']))
            $settings['SMTPServerPort'] = "25";
        if (isset($settings['SMTPServerUseAuthentiation']) && strtolower($settings['SMTPServerUseAuthentiation']) == "true") {
            if (isset($settings['SMTPServerUseSSL']) && strtolower($settings['SMTPServerUseSSL']) == "true") {
                $this->transport = \Swift_SmtpTransport::newInstance($settings['SMTPServer'], $settings['SMTPServerPort'], 'ssl')
                    ->setUsername($settings['SMTPServerAuthenticationUserName'])
                    ->setPassword($settings['SMTPServerAuthenticationPassword']);
            }
            else {
                $this->transport = \Swift_SmtpTransport::newInstance($settings['SMTPServer'], $settings['SMTPServerPort'])
                    ->setUsername($settings['SMTPServerAuthenticationUserName'])
                    ->setPassword($settings['SMTPServerAuthenticationPassword']);
            }
        }
        else
            $this->transport = \Swift_SmtpTransport::newInstance($settings['SMTPServer'], $settings['SMTPServerPort']);
        $this->mailer = \Swift_Mailer::newInstance($this->transport);
    }

    public function send(MailBuilder $mail) {
        //Send the e-mail
        $message = \Swift_Message::newInstance()
            ->setSubject($mail->subject)
            ->setFrom($mail->from)
            ->setTo($mail->to)
            ->setBody($mail->body, 'text/html'); // default to html? is this sufficient?
        $this->mailer->send($message);
    }
}