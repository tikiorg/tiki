<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class OpenPGP_Zend_Mail_Transport_Smtp extends Zend\Mail\Transport\Smtp
{
    /**
     * Prepare header string from message
     *
     * @param  Message $message
     * @return string
     */
    protected function prepareHeaders(Zend\Mail\Message $message)
    {
        $originalSubject = '';

        $headers = $message->getHeaders();
        if ($headers->has('Subject')) {
            $subjectHeader = $headers->get('Subject');
            $originalSubject = $subjectHeader->getFieldValue();
        }

        $body = $message->getBody();
        if ($body instanceof Zend\Mime\Message){
            $parts = $body->getParts();
            foreach($parts as $part) {
                /* @var $part Zend\Mime\Part */
                if ($part->getType() == Zend\Mime\Mime::TYPE_HTML) {
                    $part->setContent("******** PGP/MIME-ENCRYPTED MESSAGE ********<br>\n"
                        . "Subject: "
                        . $originalSubject
                        . "<br><br>\n"
                        . $part->getContent()
                    );
                }
                if ($part->getType() == Zend\Mime\Mime::TYPE_TEXT) {
                    $part->setContent("******** PGP/MIME-ENCRYPTED MESSAGE ********\n"
                        . "Subject: "
                        . $originalSubject
                        . "\n\n"
                        . $part->getContent()
                    );
                }
            }
        } else {
            $message->setBody("******** PGP/MIME-ENCRYPTED MESSAGE ********\n"
              . "Subject: "
              . $originalSubject
              . "\n\n"
              . $body);
        }

        $originalHeaders = parent::prepareHeaders($message);
        $originalBody = parent::prepareBody($message);

        $recipients = array();
        foreach($message->getTo() as $destination){
            $recipients[] = $destination->getEmail();
        }
        foreach($message->getCc() as $destination){
            $recipients[] = $destination->getEmail();
        }
        foreach($message->getBcc() as $destination){
            $recipients[] = $destination->getEmail();
        }

        global $openpgplib;
        $pgpmime_msg = $openpgplib->prepareEncryptWithZendMail($originalHeaders, $originalBody, $recipients);
        $headers = $pgpmime_msg[0]; // set pgp/mime headers from result array
        $this->OpenGPGStoreMailBody = $pgpmime_msg[1];    // set pgp/mime encrypted message body from result array

        return $headers;
    }

    /**
     * Prepare body string from message
     *
     * @param  Message $message
     * @return string
     */
    protected function prepareBody(Zend\Mail\Message $message)
    {
        return $this->OpenGPGStoreMailBody;
    }
}
