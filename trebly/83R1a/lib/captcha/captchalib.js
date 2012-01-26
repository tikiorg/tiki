//$Id: captchalib.js 30824 2010-11-20 22:47:08Z jonnybradley $

jQuery(document).ready(function() {
	jQuery('#captchaRegenerate').click(function() {
		generateCaptcha();
		return false;
	});
});

function generateCaptcha() {
	jQuery('#captchaImg').attr('src', 'img/spinner.gif').show();
	jQuery('body').css('cursor', 'progress');
	jQuery.ajax({
		url: 'antibot.php',
		dataType: 'json',
		success: function(data) {
			jQuery('#captchaImg').attr('src', data.captchaImgPath);
			jQuery('#captchaId').attr('value', data.captchaId);
			jQuery('body').css('cursor', 'auto');
		}
	});
}
