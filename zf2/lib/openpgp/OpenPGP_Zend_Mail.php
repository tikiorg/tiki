<?php

/////////////////////////////////////////////////////////////////////////////
/**
 * Tiki OpenPGP PGP/MIME Mail Enhancement to Zend Framework
 *
 * See Zend/Mail.php for original Zend Framework class
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
 * NOTE: NO other direct required functionality for PGP/MIME encrypted mail in THIS 
 * 	 class (all in OpenPGP_Zend_Mail_Transport_Abstract), but due to need for
 * 	 altered class instantiations, THIS needs to be pulled from original ZF also.
 *
 * CHANGE HISTORY
 * v0.10
 * 2012-11-04		hollmeer: Initial Tiki OpenPGP version from original ZF class.
 *			File & class references according to OpenPGP instances.
 */
/////////////////////////////////////////////////////////////////////////////


/**
 * Class for sending an email.
 *
 */
class OpenPGP_Zend_Mail extends Zend\Mail\Message
{
}
