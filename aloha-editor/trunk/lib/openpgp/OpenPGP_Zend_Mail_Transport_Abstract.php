<?php

/////////////////////////////////////////////////////////////////////////////
/**
 * Tiki OpenPGP PGP/MIME Mail Enhancement to Zend Framework
 *
 * See lib/core/Zend/Mail/Transport/Abstract.php for original Zend Framework class
 *
 * Files of Tiki OpenPGP PGP/MIME Mail Enhancement to Zend Framework are:
 *
 * Tiki OpenPGP		lib/openpgp/openpgplib.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail.php
 * =>	ZF Original	lib/core/Zend/Mail.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail_Transport_Abstract.php
 * =>	ZF Original	lib/core/Zend/Mail/Transport/Abstract.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail_Transport_Sendmail.php
 * =>	ZF Original	lib/core/Zend/Mail/Transport/Sendmail.php
 *
 * Tiki OpenPGP		lib/openpgp/OpenPGP_Zend_Mail_Transport_Smtp.php
 * =>	ZF Original	lib/core/Zend/Mail/Transport/Smtp.php
 *
 * PURPOSE:
 * Avoid direct patching to ZF by bringing/changing into lib/openpgp/ versions
 * which instantiate OpenPGP versions of files/classes.
 *
 * NOTE: All direct required functionality for PGP/MIME encrypted mail in THIS 
 * 	 class (all in Abstract), but due to need for altered class instantiations,
 *	 Sendmail and Smtp classes (and Mail) need to be pulled from original ZF also.
 *
 * CHANGE HISTORY
 * v0.10
 * 2012-11-04		hollmeer: Initial Tiki OpenPGP version from original ZF class.
 *			File & class references according to OpenPGP instances.
 *			Required functionality for PGP/MIME encrypted mail.
 */
/////////////////////////////////////////////////////////////////////////////


/**
 * @see Zend_Mime
 */
require_once('lib/core/Zend/Mime.php');


/**
 * Abstract for sending eMails through different
 * ways of transport
 *
 */
abstract class OpenPGP_Zend_Mail_Transport_Abstract
{
    /**
     * Mail body
     * @var string
     * @access public
     */
    public $body = '';

    /**
     * MIME boundary
     * @var string
     * @access public
     */
    public $boundary = '';

    /**
     * Mail header string
     * @var string
     * @access public
     */
    public $header = '';

    /**
     * Array of message headers
     * @var array
     * @access protected
     */
    protected $_headers = array();

    /**
     * Message is a multipart message
     * @var boolean
     * @access protected
     */
    protected $_isMultipart = false;

    /**
     * OpenPGP_Zend_Mail object
     * @var false|Zend_Mail
     * @access protected
     */
    protected $_mail = false;

    /**
     * Array of message parts
     * @var array
     * @access protected
     */
    protected $_parts = array();

    /**
     * Recipients string
     * @var string
     * @access public
     */
    public $recipients = '';

    /**
     * EOL character string used by transport
     * @var string
     * @access public
     */
    public $EOL = "\r\n";

    /**
     * Send an email independent from the used transport
     *
     * The requisite information for the email will be found in the following
     * properties:
     *
     * - {@link $recipients} - list of recipients (string)
     * - {@link $header} - message header
     * - {@link $body} - message body
     */
    abstract protected function _sendMail();

    /**
     * Return all mail headers as an array
     *
     * If a boundary is given, a multipart header is generated with a
     * Content-Type of either multipart/alternative or multipart/mixed depending
     * on the mail parts present in the {@link $_mail OpenPGP_Zend_Mail object} present.
     *
     * @param string $boundary
     * @return array
     */
    protected function _getHeaders($boundary)
    {
        if (null !== $boundary) {
            // Build multipart mail
            $type = $this->_mail->getType();
            if (!$type) {
                if ($this->_mail->hasAttachments) {
                    $type = Zend_Mime::MULTIPART_MIXED;
                } elseif ($this->_mail->getBodyText() && $this->_mail->getBodyHtml()) {
                    $type = Zend_Mime::MULTIPART_ALTERNATIVE;
                } else {
                    $type = Zend_Mime::MULTIPART_MIXED;
                }
            }

            $this->_headers['Content-Type'] = array(
                $type . ';'
                . $this->EOL
                . " " . 'boundary="' . $boundary . '"'
            );
            $this->boundary = $boundary;
        }

        $this->_headers['MIME-Version'] = array('1.0');

        return $this->_headers;
    }

    /**
     * Prepend header name to header value
     *
     * @param string $item
     * @param string $key
     * @param string $prefix
     * @static
     * @access protected
     * @return void
     */
    protected static function _formatHeader(&$item, $key, $prefix)
    {
        $item = $prefix . ': ' . $item;
    }

    /**
     * Prepare header string for use in transport
     *
     * Prepares and generates {@link $header} based on the headers provided.
     *
     * @param mixed $headers
     * @access protected
     * @return void
     * @throws Zend_Mail_Transport_Exception if any header lines exceed 998
     * characters
     */
    protected function _prepareHeaders($headers)
    {
        if (!$this->_mail) {
            /**
             * @see Zend_Mail_Transport_Exception
             */
            require_once 'lib/core/Zend/Mail/Transport/Exception.php';
            throw new Zend_Mail_Transport_Exception('Missing OpenPGP_Zend_Mail object in _mail property');
        }

        $this->header = '';

        foreach ($headers as $header => $content) {
            if (isset($content['append'])) {
                unset($content['append']);
                $value = implode(',' . $this->EOL . ' ', $content);
                $this->header .= $header . ': ' . $value . $this->EOL;
            } else {
                array_walk($content, array(get_class($this), '_formatHeader'), $header);
                $this->header .= implode($this->EOL, $content) . $this->EOL;
            }
        }

        // Sanity check on headers -- should not be > 998 characters
        $sane = true;
        foreach (explode($this->EOL, $this->header) as $line) {
            if (strlen(trim($line)) > 998) {
                $sane = false;
                break;
            }
        }
        if (!$sane) {
            /**
             * @see Zend_Mail_Transport_Exception
             */
            require_once 'lib/core/Zend/Mail/Transport/Exception.php';
            throw new Zend_Mail_Exception('At least one mail header line is too long');
        }
    }

    /**
     * Generate MIME compliant message from the current configuration
     *
     * If both a text and HTML body are present, generates a
     * multipart/alternative Zend_Mime_Part containing the headers and contents
     * of each. Otherwise, uses whichever of the text or HTML parts present.
     *
     * The content part is then prepended to the list of Zend_Mime_Parts for
     * this message.
     *
     * @return void
     */
	protected function _buildBody()
    {

	////////////////////////////////////////////////////////////////////////
	//                                                                    //
	// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION                  //
	// USING lib/openpgp/opepgplib.php                                    //
	// AS THE Subject-header is hidden and contains a hash only in a      //
	// pgp/mime encrypted message subject-header extract the original     //
	// subject and prepend it below into the text & html parts            //
	//                                                                    //
	////////////////////////////////////////////////////////////////////////

    	// get from globals (set in tiki-setup.php)
	global $openpgplib;
	$ret = $openpgplib->getPrependOriginalSubject($this->_mail);
	$prepend_to_text = $ret[0];
	$prepend_to_html = $ret[1];

	////////////////////////////////////////////////////////////////////////
	//                                                                    //
	// ALPHAFIELDS 2012-11-03: ..END ADD PGP/MIME ENCRYPTION PREPARATION  //
	// USING lib/openpgp/opepgplib.php                                    //
	//                                                                    //
	////////////////////////////////////////////////////////////////////////

        if (($text = $this->_mail->getBodyText())
            && ($html = $this->_mail->getBodyHtml()))
        {
            // Generate unique boundary for multipart/alternative
            $mime = new Zend_Mime(null);
            $boundaryLine = $mime->boundaryLine($this->EOL);
            $boundaryEnd  = $mime->mimeEnd($this->EOL);

            $text->disposition = false;
            $html->disposition = false;
	
            $body = $boundaryLine
                  . $text->getHeaders($this->EOL)
                  . $this->EOL
		///////////////////////////////////////////////////////////////
		// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION
		// USING lib/openpgp/opepgplib.php
		// PREPEND ORIG HEADER
		  . $prepend_to_text
		// ..END ADD
		///////////////////////////////////////////////////////////////
                  . $text->getContent($this->EOL)
                  . $this->EOL
                  . $boundaryLine
                  . $html->getHeaders($this->EOL)
                  . $this->EOL
		///////////////////////////////////////////////////////////////
		// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION
		// USING lib/openpgp/opepgplib.php
		// PREPEND ORIG HEADER
		  . $prepend_to_html
		// ..END ADD
		///////////////////////////////////////////////////////////////
                  . $html->getContent($this->EOL)
                  . $this->EOL
                  . $boundaryEnd;

            $mp           = new Zend_Mime_Part($body);
            $mp->type     = Zend_Mime::MULTIPART_ALTERNATIVE;
            $mp->boundary = $mime->boundary();

            $this->_isMultipart = true;

            // Ensure first part contains text alternatives
            array_unshift($this->_parts, $mp);

            // Get headers
            $this->_headers = $this->_mail->getHeaders();
            return;
        }

        // If not multipart, then get the body
        if (false !== ($body = $this->_mail->getBodyHtml())) {
		///////////////////////////////////////////////////////////////
		// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION
		// USING lib/openpgp/opepgplib.php
		// PREPEND ORIG HEADER
		$body = $prepend_to_html . $body;
		// ..END ADD
		///////////////////////////////////////////////////////////////
        	array_unshift($this->_parts, $body);
        } elseif (false !== ($body = $this->_mail->getBodyText())) {
		///////////////////////////////////////////////////////////////
		// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION
		// USING lib/openpgp/opepgplib.php
		// PREPEND ORIG HEADER
		$body = $prepend_to_text . $body;
		// ..END ADD
		///////////////////////////////////////////////////////////////
            array_unshift($this->_parts, $body);
        }

        if (!$body) {
            /**
             * @see Zend_Mail_Transport_Exception
             */
            require_once 'lib/core/Zend/Mail/Transport/Exception.php';
            throw new Zend_Mail_Transport_Exception('No body specified');
        }

        // Get headers
        $this->_headers = $this->_mail->getHeaders();
        $headers = $body->getHeadersArray($this->EOL);
        foreach ($headers as $header) {
            // Headers in Zend_Mime_Part are kept as arrays with two elements, a
            // key and a value
            $this->_headers[$header[0]] = array($header[1]);
        }
    }

    /**
     * Send a mail using this transport
     *
     * @param  OpenPGP_Zend_Mail $mail
     * @access public
     * @return void
     * @throws Zend_Mail_Transport_Exception if mail is empty
     */
	public function send(OpenPGP_Zend_Mail $mail)
    {
        $this->_isMultipart = false;
        $this->_mail        = $mail;
        $this->_parts       = $mail->getParts();
        $mime               = $mail->getMime();

        // Build body content
        $this->_buildBody();

        // Determine number of parts and boundary
        $count    = count($this->_parts);
        $boundary = null;
        if ($count < 1) {
            /**
             * @see Zend_Mail_Transport_Exception
             */
            require_once 'lib/core/Zend/Mail/Transport/Exception.php';
            throw new Zend_Mail_Transport_Exception('Empty mail cannot be sent');
        }

        if ($count > 1) {
            // Multipart message; create new MIME object and boundary
            $mime     = new Zend_Mime($this->_mail->getMimeBoundary());
            $boundary = $mime->boundary();
        } elseif ($this->_isMultipart) {
            // multipart/alternative -- grab boundary
            $boundary = $this->_parts[0]->boundary;
        }

        // Determine recipients, and prepare headers
        $this->recipients = implode(',', $mail->getRecipients());
        $this->_prepareHeaders($this->_getHeaders($boundary));

        // Create message body
        // This is done so that the same OpenPGP_Zend_Mail object can be used in
        // multiple transports
        $message = new Zend_Mime_Message();
        $message->setParts($this->_parts);
        $message->setMime($mime);
        $this->body = $message->generateMessage($this->EOL);

	////////////////////////////////////////////////////////
	//                                                    //
	// ALPHAFIELDS 2012-11-03: ADDED PGP/MIME ENCRYPTION  //
	// USING lib/openpgp/opepgplib.php                    //
	//                                                    //
	////////////////////////////////////////////////////////

    	// get from globals (set in tiki-setup.php)
	global $openpgplib;
	$pgpmime_msg = $openpgplib->prepareEncryptWithZendMail($this->header,$this->body,$mail->getRecipients());
	$this->header = $pgpmime_msg[0]; // set pgp/mime headers from result array
	$this->body = $pgpmime_msg[1];    // set pgp/mime encrypted message body from result array

	////////////////////////////////////////////////////////
	//                                                    //
	// ALPHAFIELDS 2012-11-03: ..END PGP/MIME ENCRYPTION  //
	//                                                    //
	////////////////////////////////////////////////////////

        // Send to transport!
        $this->_sendMail();
    }
    
}
