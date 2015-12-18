<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * A simple class to switch between Zend\Captcha\Image and
 * Zend\Captcha\ReCaptcha based on admin preference
 */
class Captcha
{

	/**
	 * The type of the captch ('default' when using Zend\Captcha\Image
	 * or 'recaptcha' when using Zend\Captcha\ReCaptcha)
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * An instance of Zend\Captcha\Image or Zend\Captcha\ReCaptcha
	 * depending on the value of $this->type
	 *
	 * @var object
	 */
	public $captcha = '';

	/**
	 * Class constructor: decides whether to create an instance of
	 * Zend\Captcha\Image or Zend\Captcha\ReCaptcha or Captcha_Question
	 *
	 * @param string $type recaptcha|questions|default|dumb
	 */
	function __construct( $type = '' )
	{
		global $prefs;

		if (empty($type)) {
			if ($prefs['recaptcha_enabled'] == 'y' && !empty($prefs['recaptcha_privkey']) && !empty($prefs['recaptcha_pubkey'])) {
				if ($prefs['recaptcha_version'] == '2') {
					$type = 'recaptcha20';
				} else {
					$type = 'recaptcha';
				}
			} else if ($prefs['captcha_questions_active'] == 'y' && !empty($prefs['captcha_questions'])) {
				$type = 'questions';
			} else if (extension_loaded('gd') && function_exists('imagepng') && function_exists('imageftbbox')) {
				$type = 'default';
			} else {
				$type = 'dumb';
			}
		}

		if ($type === 'recaptcha') {
			$this->captcha = new Zend\Captcha\ReCaptcha(
				array(
					'private_key' => $prefs['recaptcha_privkey'],
					'public_key' => $prefs['recaptcha_pubkey'],
				)
			);
			$this->captcha->getService()->setOption('theme', isset($prefs['recaptcha_theme']) ? $prefs['recaptcha_theme'] : 'clean');

			$this->captcha->setOption('ssl', true);

			$this->type = $type;

			$this->recaptchaCustomTranslations();
		} else if ($type === 'recaptcha20') {

			include_once('lib/captcha/Captcha_ReCaptcha20.php');

			$this->captcha = new Captcha_ReCaptcha20(
				array(
					'privkey' => $prefs['recaptcha_privkey'],
					'pubkey' => $prefs['recaptcha_pubkey'],
					'theme' => isset($prefs['recaptcha_theme']) ? $prefs['recaptcha_theme'] : 'clean',
				)
			);

			$this->captcha->setOption('ssl', true);

			$this->type = $type;

			$this->recaptchaCustomTranslations();
		} else if ($type === 'default') {
			$this->captcha = new Zend\Captcha\Image(
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
		} else if ($type === 'questions') {

			$this->type = 'questions';

			$questions = array();
			$lines = explode("\n", $prefs['captcha_questions']);

			foreach ($lines as $line) {
				$line = explode(':', $line, 2);
				if (count($line) === 2) {
					$questions[] = array(trim($line[0]), trim($line[1]));
				}
			}

			include_once('lib/captcha/Captcha_Questions.php');
			$this->captcha = new Captcha_Questions($questions);



		} else {		// implied $type==='dumb'
			$this->captcha = new Zend\Captcha\Dumb;
			$this->captcha->setWordlen($prefs['captcha_wordLen']);
			$this->captcha->setLabel(tra('Please type this word backwards'));
			$this->type = 'dumb';
		}

		$this->setErrorMessages();
	}

	/**
	 * Create the default captcha
	 *
	 * @return string
	 */
	function generate()
	{
		$key = '';
		try {
			$key = $this->captcha->generate();
			if ($this->type == 'default' || $this->type == 'questions') {
				// the following needed to keep session active for ajax checking
				$session = $this->captcha->getSession();
				$session->setExpirationHops(2, null, true);
				$this->captcha->setSession($session);
				$this->captcha->setKeepSession(false);
			}
		} catch (Zend\Captcha\Exception\ExceptionInterface $e) {
		}
		return $key;
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
		$access = TikiLib::lib('access');
		if ($access->is_xml_http_request()) {
			if ($this->type == 'recaptcha20') {
				return $this->captcha->renderAjax();
			} else {
				$params = json_encode($this->captcha->getService()->getOptions());
				$id = 1;
				TikiLib::lib('header')->add_js('
Recaptcha.create("' . $this->captcha->getPubKey() . '",
	"captcha' . $id . '",' . $params . '
  );
', 100);
				return '<div id="captcha' . $id . '"></div>';
			}
		} else {
			if ($this->captcha instanceof Captcha_ReCaptcha20) {
				return $this->captcha->render();
			} else if ($this->captcha instanceof Zend\Captcha\ReCaptcha){
				return $this->captcha->getService()->getHtml();
			}
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
		if ($this->type == 'recaptcha' || $this->type == 'recaptcha20') {
			// Temporary workaround of zend/http client uses arg_separator.output for making POST request body
			// which fails with Google recaptcha services if used with '&amp;' value
			// should be fixed in zend/http (pull request submitted)
			// or remove ini_get('arg_separator.output', '&amp;') we have in tiki code tiki-setup_base.php:31
			$oldVal = ini_get('arg_separator.output');
			ini_set('arg_separator.output', '&');
			$result = $this->captcha->isValid($input);
			ini_set('arg_separator.output', $oldVal);
			return $result;
		}	else {
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
	 * Translate Zend\Captcha\Image, Zend\Captcha\Dumb and Zend\Captcha\ReCaptcha
	 * default error messages
	 *
	 * @return void
	 */
	function setErrorMessages()
	{
		$errors = array(
			'missingValue' => tra('Empty CAPTCHA value'),
			'badCaptcha' => tra('You have mistyped the anti-bot verification code. Please try again.')
		);

		if ($this->type == 'recaptcha' || $this->type == 'recaptcha20')
			$errors['errCaptcha'] = tra('Failed to validate CAPTCHA');
		else
			$errors['missingID'] = tra('CAPTCHA ID field is missing');

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
				'cant_hear_this' => tra('Download audio as an MP3 file'),
				'incorrect_try_again' => tra('Incorrect. Try again.')
			)
		);
	}
}


