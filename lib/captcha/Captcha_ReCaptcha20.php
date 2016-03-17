<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
class Captcha_ReCaptcha20 extends Zend\Captcha\ReCaptcha
{
    protected $_RESPONSE  = 'g-recaptcha-response';

    /**
     * Validate captcha
     *
     * @see    Zend_Validate_Interface::isValid()
     * @param  mixed      $value
     * @param  array|null $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        if (!is_array($value) && !is_array($context)) {
            $this->error(self::MISSING_VALUE);
            return false;
        }

        if (empty($value[$this->_RESPONSE])) {
            $this->error(self::MISSING_VALUE);
            return false;
        }

        if (! extension_loaded('curl')) {
            $this->error('ReCaptcha 2 requires the PHP CURL extension');
            return false;
        }

        // Google request was cached
        if (in_array($value[$this->_RESPONSE], $_SESSION['recaptcha_cache'])) {
            return true;
        }

        //set POST variables
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $fields = array(
            'secret' => urlencode($this->getPrivkey()),
            'response' => urlencode($value[$this->_RESPONSE]),
            'remoteip' => urlencode($_SERVER['REMOTE_ADDR']),
        );

        $fields_string = '';
        foreach ($fields as $k => $v) {
            $fields_string .= $k . '=' . $v . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = @json_decode(curl_exec($ch), true);

        if (!is_array($result)) {
            $this->error(self::ERR_CAPTCHA);
            return false;
        }

        if ($result['success'] == false) {
            $this->error(self::BAD_CAPTCHA);
            return false;
        }

        // Cache google respnonse to avoid second resubmission on ajax form
        $_SESSION['recaptcha_cache'][] = $value[$this->_RESPONSE];

        return true;
    }

    /**
     * Render captcha
     *
     * @return string
     */
    public function render()
    {
        return '<div class="g-recaptcha" data-sitekey="' . $this->getPubkey() . '" id="antibotcode"></div>';
    }

    /**
     * Render captcha though Ajax
     *
     * @return string
     */
    public function renderAjax()
    {
        static $id = 1;
        TikiLib::lib('header')->add_js("
				grecaptcha.render('g-recaptcha{$id}', {
				'sitekey': '{$this->getPubkey()}'
				});
				", 100);
        return '<div id="g-recaptcha'.$id.'"></div>';
    }

}
