<?php

namespace ClubSpeed\Mail;

class SwiftMailer implements MailerInterface {

    private $transport;
    private $mailer;

    public function __construct(Array $settings) {
        $this->init($settings);
    }

    private function init(Array $settings) {
        /*
            SMTPServer
            SMTPServerAuthenticationPassword
            SMTPServerAuthenticationUserName
            SMTPServerPort
            SMTPServerUseAuthentiation
            SMTPServerUseSSL
            SMTPServerEncryptionType
        */
        if ($settings['SMTPServerUseAuthentiation']) { // "intentional" misspell of authentication
            if ($settings['SMTPServerUseSSL']) {
                $this->transport = \Swift_SmtpTransport::newInstance($settings['SMTPServer'], $settings['SMTPServerPort'], $settings['SMTPServerEncryptionType'])
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
            ->setCc($mail->cc)
            ->setBcc($mail->bcc)
            ->setBody($mail->body, 'text/html')
            ->addPart($mail->alternate, 'text/plain');
        $this->mailer->send($message);
    }

    //TODO: A little hacky, but didn't want to risk breaking existing functionality - could be rolled into existing MailBuilder and SwiftMailer
    public function sendWithInlineImages(MailBuilder $mail,$inlineImages = array())
    {
        $message = \Swift_Message::newInstance();

        $mailBody = $mail->body;

        foreach($inlineImages as $inlineImageKey => $inlineImageValue) //Embed each image into the email and then link the contentId in the body
        {
            $contentId = $message->embed(\Swift_Image::newInstance(base64_decode($inlineImageValue),$inlineImageKey . '.png','image/png'));
            $imageString = '<img alt="' . $inlineImageKey . '" style="margin: 0 auto; display: block;" src="' . $contentId . '">';
            $mailBody = str_replace('##' . $inlineImageKey . '##', $imageString, $mailBody); //Ex. ##giftCardImage## gets replaced
        }

        $message->setSubject($mail->subject)
            ->setFrom($mail->from)
            ->setTo($mail->to)
            ->setCc($mail->cc)
            ->setBcc($mail->bcc)
            ->setBody($mailBody, 'text/html')
            ->addPart($mail->alternate, 'text/plain');
        $this->mailer->send($message);
    }
}