<?php

function validator_captcha($input, $parameter = '', $message = '') {
	global $prefs, $captchalib;
	$_REQUEST['captcha'] = array('input' => $input, 'id' => $parameter);
	if (!$captchalib->validate()) {
		// the following needed to keep session active for ajax checking 
		$session = $captchalib->captcha->getSession();
		$session->setExpirationHops(2, null, true);
		$captchalib->captcha->setSession($session);
		$captchalib->captcha->setKeepSession(false);
		// now return errors
		return $captchalib->getErrors();
	}
	// the following needed to keep session active for ajax checking 
	$session = $captchalib->captcha->getSession();
	$session->setExpirationHops(2, null, true);
	$captchalib->captcha->setSession($session);
	$captchalib->captcha->setKeepSession(false);
	// now return ok
	return true;
}