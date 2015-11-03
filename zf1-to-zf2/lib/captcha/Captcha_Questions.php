<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Captcha_Questions extends Zend_Captcha_Word
{
	/**
	 * CAPTCHA label
	 * @type string
	 */
	protected $_label = '';

	private $_questions = array();    // array for quesiotns and answers passed to ctor
	private $_current = null;        // int current q & a

	/**
	 * Constructor
	 *
	 * @param  array|$questions
	 */
	public function __construct($questions)
	{
		$this->_questions = $questions;
	}

	/**
	 * Generate new session ID and new word
	 *
	 * @return string session ID
	 */
	public function generate()
	{
		if (!$this->_keepSession) {
			$this->_session = null;
		}
		$id = $this->_generateRandomId();
		$this->_setId($id);
		$word = $this->_generateWord();
		$this->_setWord($word);
		return $id;
	}

	/**
	 * Render the captcha
	 *
	 * @param  Zend_View_Interface $view
	 * @param  mixed $element
	 * @return string
	 */
	public function render(Zend_View_Interface $view = null, $element = null)
	{
		$question = $this->_questions[$this->_current];

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
			$this->_error(self::MISSING_VALUE);
			return false;
		}
		$input = strtolower($value['input']);
		$this->_setValue($input);

		if (!isset($value['id'])) {
			$this->_error(self::MISSING_ID);
			return false;
		}

		$this->_id = $value['id'];
		$word = $this->getWord();

		if (empty($word)) {
			$this->_error(self::MISSING_VALUE);
			return false;
		}

		if (!(preg_match('#^/.*/[imsxADSUXJu]*$#', $word) && preg_match($word, $input)) &&    // regex answer?
			$input !== $word
		) {

			$this->_error(self::BAD_CAPTCHA);
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
		if (empty($this->_word)) {
			$session = $this->getSession();
			$this->_word = $session->word;
		}
		return $this->_word;
	}

	/**
	 * Generate new random answer
	 *
	 * @return string
	 */
	protected function _generateWord()
	{
		$this->_current = array_rand($this->_questions);
		$question = $this->_questions[$this->_current];

		$word = $question[1];

		return $word;
	}

	/**
	 * Set the label for the CAPTCHA
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->_label = $label;
	}

	/**
	 * Retrieve the label for the CAPTCHA
	 * @return string
	 */
	public function getLabel()
	{
		return $this->_label;
	}


}

