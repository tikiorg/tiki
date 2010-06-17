<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id $

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

require_once('lib/core/lib/Zend/Captcha/Image.php');
require_once('lib/core/lib/Zend/Captcha/ReCaptcha.php');
require_once('lib/core/lib/Zend/Loader.php');

/**
 * A simple class to switch between Zend_Captcha_Image and
 * Zend_Captcha_ReCaptcha based on admin preference
 */
class Captcha {

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
	function Captcha() {
		global $prefs;
		$prefs['recaptcha_enabled'] = 'n';
		if ($prefs['recaptcha_enabled'] == 'y' && !empty($prefs['recaptcha_privkey']) && !empty($prefs['recaptcha_pubkey'])) {
			$this->captcha = new Zend_Captcha_ReCaptcha(array(
				'privkey' => $prefs['recaptcha_privkey'],
				'pubkey' => $prefs['recaptcha_pubkey'],
				'theme' => 'clean'
			));
			$this->type = 'recaptcha';
		} else {
			$this->captcha = new Zend_Captcha_Image(array(
				'wordLen' => 6,
				'timeout' => 600,
				'font' => __DIR__ . '/arial.ttf',
				'imgdir' => 'lib/captcha/captchas'
			));
			$this->type = 'default';
		}
	}

	/**
	 * Create the default captcha
	 *
	 * @return void
	 */
	function generate() {
		$this->captcha->generate();
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
	 * @param string $input User input for the captcha
	 * @return bool true or false 
	 *
	 */
	function validate($input) {
		return $this->captcha->isValid($input);
	}

	/**
	 * Return the full path to the captcha image when using default captcha
	 *
	 * @return string full path to default captcha image
	 */
	function getPath() {
		return $this->captcha->getImgDir() . $this->captcha->getId() .  $this->captcha->getSuffix();
	}

}

if (!isset($captchalib))
	$captchalib = new Captcha;

?>
