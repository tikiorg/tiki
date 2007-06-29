{* $Header: /cvsroot/tikiwiki/tiki/templates/mail/confirm_user_email.tpl,v 1.2 2007-06-29 13:58:04 sylvieg Exp $ *}
{tr}To validate your account and login to the site, please click on the following link:{/tr}
{$mail_machine}?user={$user|escape:'url'}&pass={$mail_apass}