<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) != FALSE) {
	header('location: index.php');
	exit;
}

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
	 * Class constructor: decides whether to create an instance of
	 * Zend_Captcha_Image or Zend_Captcha_ReCaptcha
	 *
	 * @return null
	 */
	function __construct( $type = '' )
	{
		global $prefs;

		if (empty($type)) {
			if ($prefs['recaptcha_enabled'] == 'y' && !empty($prefs['recaptcha_privkey']) && !empty($prefs['recaptcha_pubkey'])) {
				$type = 'recaptcha';
			} else if (extension_loaded('gd') && function_exists('imagepng') && function_exists('imageftbbox')) {
				$type = 'default';
			} else {
				$type = 'dumb';
			}
		}

		if ($type === 'recaptcha') {
			$this->captcha = new Zend_Captcha_ReCaptcha(
				array(
					'privkey' => $prefs['recaptcha_privkey'],
					'pubkey' => $prefs['recaptcha_pubkey'],
					'theme' => isset($prefs['recaptcha_theme']) ? $prefs['recaptcha_theme'] : 'clean',
				)
			);

			$this->type = 'recaptcha';

			$this->recaptchaCustomTranslations();
		} else if ($type === 'default') {
			$this->captcha = new Zend_Captcha_Image(
				array(
					'wordLen' => $prefs['captcha_wordLen'],
					'timeout' => 600,
					'font' => dirname(__FILE__) . '/DejaVuSansMono.ttf',
					'imgdir' => 'temp/public/',
					'suffix' => '.captcha.png',
					'width' => $prefs['captcha_width'],
					'dotNoiseLevel' => $prefs['captcha_noise'],
				)
			);
			$this->type = 'default';
		} else {		// implied $type==='dumb'
			$this->captcha = new Zend_Captcha_Dumb;
			$this->captcha->setWordlen($prefs['captcha_wordLen']);
			$this->captcha->setLabel(tra('Please type this word backwards'));
			$this->type = 'dumb';
		}

		$this->setErrorMessages();
	}

	/**
	 * Create the default captcha
	 *
	 * @return void
	 */
	function generate()
	{
		try {
			$key = $this->captcha->generate();
			if ($this->type == 'default') {
				// the following needed to keep session active for ajax checking
				$session = $this->captcha->getSession();
				$session->setExpirationHops(2, null, true);
				$this->captcha->setSession($session);
				$this->captcha->setKeepSession(false);
			}
			return $key;
		} catch (Zend_Exception $e) {
		}
	}

	/** Return captcha ID
	 *
	 * @return string captcha ID
	 */
	function getId()
	{
		return $this->captcha->getId();
	}

	/**
	 * HTML code for the captcha
	 *
	 * @return string
	 */
	function render()
	{
		global $access;
		if ($access->is_xml_http_request()) {
			$params = json_encode($this->captcha->getService()->getOptions());
			$id = 1;
			TikiLib::lib('header')->add_js('
Recaptcha.create("' . $this->captcha->getPubKey() . '",
	"captcha' . $id . '",' . $params . '
  );
', 100);
			return '<div id="captcha' . $id . '"></div>';
		} else {
			return $this->captcha->render();
		}
	}

	/**
	 * Validate user input for the captcha
	 *
	 * @param array $input
	 * @return bool true or false
	 */
	function validate($input = null)
	{
		if (is_null($input)) {
			$input = $_REQUEST;
		}
		if ($this->type == 'recaptcha') {
			return $this->captcha->isValid(
				array(
					'recaptcha_challenge_field' => $input['recaptcha_challenge_field'],
					'recaptcha_response_field' => $input['recaptcha_response_field']
				)
			);
		} else {
			return $this->captcha->isValid($input['captcha']);
		}
	}

	/**
	 * Return the full path to the captcha image when using default captcha
	 *
	 * @return string full path to default captcha image
	 */
	function getPath()
	{
		return $this->captcha->getImgDir() . $this->captcha->getId() . $this->captcha->getSuffix();
	}

	/**
	 * Translate Zend_Captcha_Image, Zend_Captcha_Dumb and Zend_Captcha_ReCaptcha
	 * default error messages
	 *
	 * @return void
	 */
	function setErrorMessages()
	{
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
	function getErrors()
	{
		return implode('<br />', $this->captcha->getMessages());
	}

	/**
	 * Custom translation for ReCaptcha interface
	 *
	 * @return void
	 */
	function recaptchaCustomTranslations()
	{
		$recaptchaService = $this->captcha->getService();
		$recaptchaService->setOption(
			'custom_translations',
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

