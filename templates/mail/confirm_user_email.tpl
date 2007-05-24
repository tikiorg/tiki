{* $Header: /cvsroot/tikiwiki/tiki/templates/mail/confirm_user_email.tpl,v 1.1 2007-05-24 14:30:47 sylvieg Exp $ *}
{tr}To log on this site, your email must be confirmed.{/tr}
{tr}Please visit this link to confirm:{/tr}
{$mail_machine}?user={$user|escape:'url'}&pass={$mail_apass}