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
require_once('lib/core/lib/Zend/Captcha/Dumb.php');

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
		
		if ($prefs['recaptcha_enabled'] == 'y' && !empty($prefs['recaptcha_privkey']) && !empty($prefs['recaptcha_pubkey'])) {
			$this->captcha = new Zend_Captcha_ReCaptcha(array(
				'privkey' => $prefs['recaptcha_privkey'],
				'pubkey' => $prefs['recaptcha_pubkey'],
				'theme' => 'clean'
			));

			$this->type = 'recaptcha';

			$this->recaptchaCustomTranslations();
		} else if (extension_loaded('gd') && function_exists('imagepng') && function_exists('imageftbbox')) {
			$this->captcha = new Zend_Captcha_Image_Tiki(array(
				'wordLen' => 6,
				'timeout' => 600,
				'font' => __DIR__ . '/DejaVuSansMono.ttf',
				'imgdir' => 'temp/public/',
				'suffix' => '.captcha.png',
			));
			$this->type = 'default';
		} else {
			$this->captcha = new Zend_Captcha_Dumb;
			$this->type = 'dumb';
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
	 * Custom translation for ReCaptcha
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

/**
 * extending Zend_Captcha_Image to replace function _gc()
 * while the issue with the garbage collector is not fixed in
 * the Zend Framework itself (see issue http://framework.zend.com/issues/browse/ZF-10006)
 */
class Zend_Captcha_Image_Tiki extends Zend_Captcha_Image {
	
	protected function _gc()
    {
        $expire = time() - $this->getExpiration();
        $imgdir = $this->getImgDir();
        if(!$imgdir || strlen($imgdir) < 2) {
            // safety guard
            return;
        }
        foreach (new DirectoryIterator($imgdir) as $file) {
            if (!$file->isDot() && !$file->isDir()) {
				if ($file->getMTime() < $expire) {
	                $len = strlen($this->_suffix);
                    // only deletes files ending with $this->_suffix
                    if (substr($file->getFilename(), -($len), $len) == $this->_suffix) {
                        unlink($file->getPathname());
                    }
                }
            }
        }
    }

}

global $captchalib;
$captchalib = new Captcha;

