<?php

/////////////////////////////////////////////////////////////////////////////
/**
 * Tiki OpenPGP PGP/MIME Mail Enhancement to Zend Framework
 *
 * See Zend/Mail/Transport/Sendmail.php for original Zend Framework class
 *
 * Files of Tiki OpenPGP PGP/MIME Mail Enhancement to Zend Framework are:
 *
 * Tiki OpenPGP		lib/openpgp/openpgplib.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail.php
 * =>	ZF Original	Zend/Mail.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail_Transport_Abstract.php
 * =>	ZF Original	Zend/Mail/Transport/Abstract.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail_Transport_Sendmail.php
 * =>	ZF Original	Zend/Mail/Transport/Sendmail.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail_Transport_Smtp.php
 * =>	ZF Original	Zend/Mail/Transport/Smtp.php
 *
 * PURPOSE:
 * Avoid direct patching to ZF by bringing/changing into lib/openpgp/ versions
 * which instantiate OpenPGP versions of files/classes.
 *
 * NOTE: NO direct required functionality for PGP/MIME encrypted mail in this
 * 	 class (all in OpenPGP_Zend_Mail_Transport_Abstract), but due to need
 *	 for altered class instantiations, this class needs to be pulled from
 *	 original ZF also.
 *
 * CHANGE HISTORY
 * v0.10
 * 2012-11-04		hollmeer: Initial Tiki OpenPGP version from original ZF class.
 *			File & class references according to OpenPGP instances.
 *			A [BUG FIX] in _sendMail() function.
 */
/////////////////////////////////////////////////////////////////////////////


/**
 * Class for sending eMails via the PHP internal mail() function
 *
 */
class OpenPGP_Zend_Mail_Transport_Sendmail extends OpenPGP_Zend_Mail_Transport_Abstract
{
	/**
	 * Subject
	 * @var string
	 * @access public
	 */
	public $subject = null;


	/**
	 * Config options for sendmail parameters
	 *
	 * @var string
	 */
	public $parameters;

	/**
	 * EOL character string
	 * @var string
	 * @access public
	 */
	public $EOL = PHP_EOL;

	/**
	 * error information
	 * @var string
	 */
	protected $_errstr;

	/**
	 * Constructor.
	 *
	 * @param  string|array|Zend_Config $parameters OPTIONAL (Default: null)
	 * @return void
	 */
	public function __construct($parameters = null)
	{
		if ($parameters instanceof Zend_Config) {
			$parameters = $parameters->toArray();
		}

		if (is_array($parameters)) {
			$parameters = implode(' ', $parameters);
		}

		$this->parameters = $parameters;
	}


	/**
	 * Send mail using PHP native mail()
	 *
	 * @access public
	 * @return void
	 * @throws Zend_Mail_Transport_Exception if parameters is set
	 *		 but not a string
	 * @throws Zend_Mail_Transport_Exception on mail() failure
	 */
	public function _sendMail()
	{
		if ($this->parameters === null) {
			set_error_handler(array($this, '_handleMailErrors'));

			//[BUG FIX] hollmeer 2012-11-04: must set this as otherwise mail is not delivered!!
			// Set the sender for sendmail and use only the email address when the syntax of return_path is like 'Name <email>'
			$additional_parameters = '-f' . preg_replace('/^.*<(.*?)>.*$/', '$1', $this->_mail->getReturnPath());

			$result = mail(
				$this->recipients,
				$this->_mail->getSubject(),
				$this->body,
				$this->header,
				$additional_parameters
			); // this added!
			restore_error_handler();
		} else {
			if (!is_string($this->parameters)) {
				throw new Zend_Mail_Transport_Exception(
					'Parameters were set but are not a string'
				);
			}

			set_error_handler(array($this, '_handleMailErrors'));
			$result = mail(
				$this->recipients,
				$this->_mail->getSubject(),
				$this->body,
				$this->header,
				$this->parameters
			);
			restore_error_handler();
		}

		if ($this->_errstr !== null || !$result) {
			throw new Zend_Mail_Transport_Exception('Unable to send mail. ' . $this->_errstr);
		}
	}


	/**
	 * Format and fix headers
	 *
	 * mail() uses its $to and $subject arguments to set the To: and Subject:
	 * headers, respectively. This method strips those out as a sanity check to
	 * prevent duplicate header entries.
	 *
	 * @access  protected
	 * @param   array $headers
	 * @return  void
	 * @throws  Zend_Mail_Transport_Exception
	 */
	protected function _prepareHeaders($headers)
	{
		if (!$this->_mail) {
			throw new Zend_Mail_Transport_Exception('_prepareHeaders requires a registered OpenPGP_Zend_Mail object');
		}

		// mail() uses its $to parameter to set the To: header, and the $subject
		// parameter to set the Subject: header. We need to strip them out.
		if (0 === strpos(PHP_OS, 'WIN')) {
			// If the current recipients list is empty, throw an error
			if (empty($this->recipients)) {
				throw new Zend_Mail_Transport_Exception('Missing To addresses');
			}
		} else {
			// All others, simply grab the recipients and unset the To: header
			if (!isset($headers['To'])) {
				throw new Zend_Mail_Transport_Exception('Missing To header');
			}

			unset($headers['To']['append']);
			$this->recipients = implode(',', $headers['To']);
		}

		// Remove recipient header
		unset($headers['To']);

		// Remove subject header, if present
		if (isset($headers['Subject'])) {
			unset($headers['Subject']);
		}

		// Prepare headers
		parent::_prepareHeaders($headers);

		// Fix issue with empty blank line ontop when using Sendmail Trnasport
		$this->header = rtrim($this->header);
	}

	/**
	 * Temporary error handler for PHP native mail().
	 *
	 * @param int	$errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param string $errline
	 * @param array  $errcontext
	 * @return true
	 */
	public function _handleMailErrors($errno, $errstr, $errfile = null, $errline = null, array $errcontext = null)
	{
		$this->_errstr = $errstr;
		return true;
	}

}
