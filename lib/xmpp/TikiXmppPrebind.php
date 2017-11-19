<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiXmppPrebind extends XmppPrebind
{
	const ENCRYPTION_TIKITOKEN = 'TIKITOKEN';

	/**
	 * Connect to XMPP server, but setting TIKITOKEN as preferred
	 * authorization mechanism
	 */
	public function connect($username, $password, $route = false)
	{
		parent::connect($username, $password, $route);

		if (in_array(self::ENCRYPTION_TIKITOKEN, $this->mechanisms)) {
			$this->encryption = self::ENCRYPTION_TIKITOKEN;
			$this->debug($this->encryption, 'encryption used');
		}
	}

	public function auth()
	{

		if ($this->encryption === self::ENCRYPTION_TIKITOKEN) {
			$auth = Auth_SASL::factory(self::ENCRYPTION_PLAIN);
		} else {
			$auth = Auth_SASL::factory($this->encryption);
		}

		switch ($this->encryption) {
			case self::ENCRYPTION_TIKITOKEN:
				$authXml = $this->buildTikiTokenAuth($auth);
				break;
			case self::ENCRYPTION_PLAIN:
				$authXml = $this->buildPlainAuth($auth);
				break;
			case self::ENCRYPTION_DIGEST_MD5:
				$authXml = $this->sendChallengeAndBuildDigestMd5Auth($auth);
				break;
			case self::ENCRYPTION_CRAM_MD5:
				$authXml = $this->sendChallengeAndBuildCramMd5Auth($auth);
				break;
		}
		$response = $this->send($authXml);

		$body = self::getBodyFromXml($response);

		if (! $body->hasChildNodes() || $body->firstChild->nodeName !== 'success') {
			throw new XmppPrebindException("Invalid login");
		}

		$this->sendRestart();
		$this->sendBindIfRequired();
		$this->sendSessionIfRequired();

		return true;
	}

	protected function buildTikiTokenAuth(Auth_SASL_Common $auth)
	{
		$authString = $auth->getResponse(self::getNodeFromJid($this->jid), $this->password);
		$authString = base64_encode($authString);
		$this->debug($authString, 'PLAIN Auth String');

		$domDocument = $this->buildBody();
		$body = self::getBodyFromDomDocument($domDocument);

		$auth = $domDocument->createElement('auth');
		$auth->appendChild(self::getNewTextAttribute($domDocument, 'xmlns', self::XMLNS_SASL));
		$auth->appendChild(self::getNewTextAttribute($domDocument, 'mechanism', 'TIKITOKEN'));
		$auth->appendChild($domDocument->createTextNode($authString));
		$body->appendChild($auth);

		return $domDocument->saveXML();
	}
}
