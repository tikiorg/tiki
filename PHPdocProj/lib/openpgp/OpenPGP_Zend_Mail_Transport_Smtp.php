<?php

/////////////////////////////////////////////////////////////////////////////
/**
 * Tiki OpenPGP PGP/MIME Mail Enhancement to Zend Framework
 *
 * See lib/core/Zend/Mail/Transport/Smtp.php for original Zend Framework class
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
 * NOTE: NO direct required functionality for PGP/MIME encrypted mail in this 
 * 	 class (all in OpenPGP_Zend_Mail_Transport_Abstract), but due to need 
 *	 for altered class instantiations, this class needs to be pulled from 
 *	 original ZF also.
 *
 * CHANGE HISTORY
 * v0.10
 * 2012-11-04		hollmeer: Initial Tiki OpenPGP version from original ZF class.
 *			File & class references according to OpenPGP instances.
 */
/////////////////////////////////////////////////////////////////////////////


/**
 * @see Zend_Mime
 */
require_once('lib/core/Zend/Mime.php');

/**
 * @see Zend_Mail_Protocol_Smtp
 */
require_once('lib/core/Zend/Mail/Protocol/Smtp.php');

/**
 * @see OpenPGP_Zend_Mail_Transport_Abstract
 */
require_once('lib/openpgp/OpenPGP_Zend_Mail_Transport_Abstract.php');


/**
 * SMTP connection object
 *
 * Loads an instance of Zend_Mail_Protocol_Smtp and forwards smtp transactions
 *
 */
class OpenPGP_Zend_Mail_Transport_Smtp extends OpenPGP_Zend_Mail_Transport_Abstract
{
    /**
     * EOL character string used by transport
     * @var string
     * @access public
     */
    public $EOL = "\n";

    /**
     * Remote smtp hostname or i.p.
     *
     * @var string
     */
    protected $_host;


    /**
     * Port number
     *
     * @var integer|null
     */
    protected $_port;


    /**
     * Local client hostname or i.p.
     *
     * @var string
     */
    protected $_name = 'localhost';


    /**
     * Authentication type OPTIONAL
     *
     * @var string
     */
    protected $_auth;


    /**
     * Config options for authentication
     *
     * @var array
     */
    protected $_config;


    /**
     * Instance of Zend_Mail_Protocol_Smtp
     *
     * @var Zend_Mail_Protocol_Smtp
     */
    protected $_connection;


    /**
     * Constructor.
     *
     * @param  string $host OPTIONAL (Default: 127.0.0.1)
     * @param  array|null $config OPTIONAL (Default: null)
     * @return void
     *
     * @todo Someone please make this compatible
     *       with the SendMail transport class.
     */
    public function __construct($host = '127.0.0.1', Array $config = array())
    {
        if (isset($config['name'])) {
            $this->_name = $config['name'];
        }
        if (isset($config['port'])) {
            $this->_port = $config['port'];
        }
        if (isset($config['auth'])) {
            $this->_auth = $config['auth'];
        }

        $this->_host = $host;
        $this->_config = $config;
    }


    /**
     * Class destructor to ensure all open connections are closed
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->_connection instanceof Zend_Mail_Protocol_Smtp) {
            try {
                $this->_connection->quit();
            } catch (Zend_Mail_Protocol_Exception $e) {
                // ignore
            }
            $this->_connection->disconnect();
        }
    }


    /**
     * Sets the connection protocol instance
     *
     * @param Zend_Mail_Protocol_Abstract $client
     *
     * @return void
     */
    public function setConnection(Zend_Mail_Protocol_Abstract $connection)
    {
        $this->_connection = $connection;
    }


    /**
     * Gets the connection protocol instance
     *
     * @return Zend_Mail_Protocol|null
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Send an email via the SMTP connection protocol
     *
     * The connection via the protocol adapter is made just-in-time to allow a
     * developer to add a custom adapter if required before mail is sent.
     *
     * @return void
     * @todo Rename this to sendMail, it's a public method...
     */
    public function _sendMail()
    {
        // If sending multiple messages per session use existing adapter
        if (!($this->_connection instanceof Zend_Mail_Protocol_Smtp)) {
            // Check if authentication is required and determine required class
            $connectionClass = 'Zend_Mail_Protocol_Smtp';
            if ($this->_auth) {
                $connectionClass .= '_Auth_' . ucwords($this->_auth);
            }
            if (!class_exists($connectionClass)) {
                require_once 'lib/core/Zend/Loader.php';
                Zend_Loader::loadClass($connectionClass);
            }
            $this->setConnection(new $connectionClass($this->_host, $this->_port, $this->_config));
            $this->_connection->connect();
            $this->_connection->helo($this->_name);
        } else {
            // Reset connection to ensure reliable transaction
            $this->_connection->rset();
        }

        // Set sender email address
        $this->_connection->mail($this->_mail->getReturnPath());

        // Set recipient forward paths
        foreach ($this->_mail->getRecipients() as $recipient) {
            $this->_connection->rcpt($recipient);
        }

        // Issue DATA command to client
        $this->_connection->data($this->header . Zend_Mime::LINEEND . $this->body);
    }

    /**
     * Format and fix headers
     *
     * Some SMTP servers do not strip BCC headers. Most clients do it themselves as do we.
     *
     * @access  protected
     * @param   array $headers
     * @return  void
     * @throws  Zend_Transport_Exception
     */
    protected function _prepareHeaders($headers)
    {
        if (!$this->_mail) {
            /**
             * @see Zend_Mail_Transport_Exception
             */
            require_once 'lib/core/Zend/Mail/Transport/Exception.php';
            throw new Zend_Mail_Transport_Exception('_prepareHeaders requires a registered OpenPGP_Zend_Mail object');
        }

        unset($headers['Bcc']);

        // Prepare headers
        parent::_prepareHeaders($headers);
    }
}
