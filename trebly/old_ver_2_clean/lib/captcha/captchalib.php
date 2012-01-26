<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

require_once('lib/core/Zend/Captcha/Image.php');

/**
 * A simple class to switch between Zend_Captcha_Image and
 * Zend_Captcha_ReCaptcha based on admin preference
 */
class Captcha
{

	/**
	 * The type of the captch ('default' when using Zend_Captcha_Image
	 * or 'recaptcha' when using Zend_Captcha_ReCaptcha)
	 *
	 * @var string 
	 */
	public $type = '';

	/**
	 * An instance of Zend_Captcha_Image or Zend_Captcha_ReCaptcha
	 * depending on the value of $this->type
	 *
	 * @var object
	 */
	public $captcha = '';

	/**
	 * Class constructor: decides wheter to create an instance of
	 * Zend_Captcha_Image or Zend_Captcha_ReCaptcha
	 *
	 * @return null
	 */
	function __construct() {
		global $prefs;
		
		if ($prefs['recaptcha_enabled'] == 'y' && !empty($prefs['recaptcha_privkey']) && !empty($prefs['recaptcha_pubkey'])) {
			require_once('lib/core/Zend/Captcha/ReCaptcha.php');
			$this->captcha = new Zend_Captcha_ReCaptcha(array(
				'privkey' => $prefs['recaptcha_privkey'],
				'pubkey' => $prefs['recaptcha_pubkey'],
				'theme' => 'clean'
			));

			$this->type = 'recaptcha';

			$this->recaptchaCustomTranslations();
		} else if (extension_loaded('gd') && function_exists('imagepng') && function_exists('imageftbbox')) {
			$this->captcha = new Zend_Captcha_Image(array(
				'wordLen' => $prefs['captcha_wordLen'],
				'timeout' => 600,
				'font' => dirname(__FILE__) . '/DejaVuSansMono.ttf',
				'imgdir' => 'temp/public/',
				'suffix' => '.captcha.png',
				'width' => $prefs['captcha_width'],
				'dotNoiseLevel' => $prefs['captcha_noise'],
			));
			$this->type = 'default';
		} else {
			require_once('lib/core/Zend/Captcha/Dumb.php');
			$this->captcha = new Zend_Captcha_Dumb;
			$this->type = 'dumb';
		}

		$this->setErrorMessages();
	}

	/**
	 * Create the default captcha
	 *
	 * @return void
	 */
	function generate() {
		try {
			$this->captcha->generate();
			if ($this->type == 'default') {
				// the following needed to keep session active for ajax checking 
				$session = $this->captcha->getSession();
				$session->setExpirationHops(2, null, true);
				$this->captcha->setSession($session);
				$this->captcha->setKeepSession(false);
			}
		} catch (Zend_Exception $e) {
		}
	}

	/** Return captcha ID
	 *
	 * @return string captcha ID
	 */
	function getId() {
		return $this->captcha->getId();
	}

	/**
	 * HTML code for the captcha
	 *
	 * @return string
	 */
	function render() {
		return $this->captcha->render();
	}

	/**
	 * Validate user input for the captcha
	 *
	 * @return bool true or false 
	 *
	 */
	function validate() {
		if ($this->type == 'recaptcha') {
			return $this->captcha->isValid(array(
				'recaptcha_challenge_field' => $_REQUEST['recaptcha_challenge_field'],
				'recaptcha_response_field' => $_REQUEST['recaptcha_response_field']
			));
		} else {
			return $this->captcha->isValid($_REQUEST['captcha']);
		}
	}

	/**
	 * Return the full path to the captcha image when using default captcha
	 *
	 * @return string full path to default captcha image
	 */
	function getPath() {
		return $this->captcha->getImgDir() . $this->captcha->getId() .  $this->captcha->getSuffix();
	}

	/**
	 * Translate Zend_Captcha_Image, Zend_Captcha_Dumb and Zend_Captcha_ReCaptcha
	 * default error messages
	 *
	 * @return void
	 */
	function setErrorMessages() {
		$errors = array(
			'missingValue' => tra('Empty captcha value'),
			'badCaptcha' => tra('You have mistyped the anti-bot verification code. Please try again.')
		);

		if ($this->type == 'recaptcha')
			$errors['errCaptcha'] = tra('Failed to validate captcha');
		else
			$errors['missingID'] = tra('Captcha ID field is missing');

		$this->captcha->setMessages($errors);
	}

	/**
	 * Convert the errors array into a string and return it
	 *
	 * @return string error messages
	 */
	function getErrors() {
		return implode('<br />', $this->captcha->getMessages());
	}

	/**
	 * Custom translation for ReCaptcha interface
	 *
	 * @return void
	 */
	function recaptchaCustomTranslations() {
		$recaptchaService = $this->captcha->getService();
		$recaptchaService->setOption('custom_translations',
			array(
				'visual_challenge' => tra('Get a visual challenge'),
				'audio_challenge' => tra('Get an audio challenge'),
				'refresh_btn' => tra('Get a new challenge'),
				'instructions_visual' => tra('Type the two words'),
				'instructions_audio' => tra('Type what you hear'),
				'help_btn' => tra('Help'),
				'play_again' => tra('Play sound again'),
				'cant_hear_this' => tra('Download sound as MP3'),
				'incorrect_try_again' => tra('Incorrect. Try again.')
			)
		);
	}
}

global $captchalib;
$captchalib = new Captcha;

