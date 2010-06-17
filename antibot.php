<?

require_once ('tiki-setup.php');
if ($prefs['feature_antibot'] == 'n' || $prefs['rnd_num_reg'] == 'n') {
	die;
}

require_once('lib/captcha/captchalib.php');

$captchalib->generate();
$captcha = array('captchaId' => $captchalib->getId(), 'captchaImgPath' => $captchalib->getPath());

echo json_encode($captcha);

?>
