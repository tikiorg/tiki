<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Captcha_Questions extends Zend\Captcha\AbstractWord
{

	protected $name = "questions";

	/**
	 * CAPTCHA label
	 * @type string
	 */
	protected $label = '';

	private $questions = array();    // array for quesiotns and answers passed to ctor
	private $current = null;        // int current q & a

	/**#@+
	 * Error codes
	 */
	const MISSING_VALUE = 'missingValue';
	const ERR_CAPTCHA   = 'errCaptcha';
	const BAD_CAPTCHA   = 'badCaptcha';
	const MISSING_ID    = 'missingID';
	/**#@-*/

	/**
	 * Error messages
	 * @var array
	 */
	protected $messageTemplates = array(
		self::MISSING_VALUE => 'Missing captcha fields',
		self::ERR_CAPTCHA   => 'Failed to validate CAPTCHA',
		self::BAD_CAPTCHA   => 'Captcha value is wrong: %value%',
		self::MISSING_ID    => 'missingID',
	);

	/**
	 * Constructor
	 *
	 * @param  array|$questions
	 */
	public function __construct($questions)
	{
		$this->questions = $questions;
		$this->abstractOptions['messageTemplates'] = $this->messageTemplates;
	}

	/**
	 * Generate new session ID and new word
	 *
	 * @return string session ID
	 */
	public function generate()
	{
		if (!$this->keepSession) {
			$this->session = null;
		}
		$id = $this->generateRandomId();
		$this->setId($id);
		$word = $this->generateWord();
		$this->setWord($word);
		return $id;
	}

	/**
	 * Render the captcha
	 *
	 * @param  $view
	 * @param  mixed $element
	 * @return string
	 */
	public function render($view = null, $element = null)
	{
		$question = $this->questions[$this->current];

		return tra($question[0]);
	}

	/**
	 * Validate the answer
	 *
	 * @see    Zend\Validator\Interface::isValid()
	 * @param  mixed $value
	 * @param  array|null $context
	 * @return boolean
	 */
	public function isValid($value, $context = null)
	{
		if (!is_array($value) && !is_array($context)) {
			$this->_error(self::MISSING_VALUE);
			return false;
		}
		if (!is_array($value) && is_array($context)) {
			$value = $context;
		}

		$name = $this->getName();

		if (isset($value[$name])) {
			$value = $value[$name];
		}

		if (!isset($value['input'])) {
			$this->error(self::MISSING_VALUE);
			return false;
		}
		$input = strtolower($value['input']);
		$this->setValue($input);

		if (!isset($value['id'])) {
			$this->error(self::MISSING_ID);
			return false;
		}

		$this->id = $value['id'];
		$word = $this->getWord();

		if (empty($word)) {
			$this->error(self::MISSING_VALUE);
			return false;
		}

		if (!(preg_match('#^/.*/[imsxADSUXJu]*$#', $word) && preg_match($word, $input)) &&    // regex answer?
			$input !== $word
		) {

			$this->error(self::BAD_CAPTCHA);
			return false;
		}

		return true;
	}

	/**
	 * Get captcha word (actually answer in this case)
	 *
	 * @return string
	 */
	public function getWord()
	{
		if (empty($this->word)) {
			$session = $this->getSession();
			$this->word = $session->word;
		}
		return $this->word;
	}

	/**
	 * Generate new random answer
	 *
	 * @return string
	 */
	protected function generateWord()
	{
		$this->current = array_rand($this->questions);
		$question = $this->questions[$this->current];

		$word = $question[1];

		return $word;
	}

	/**
	 * Set the label for the CAPTCHA
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}

	/**
	 * Retrieve the label for the CAPTCHA
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}


}

