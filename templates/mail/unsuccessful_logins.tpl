{* $Header: /cvsroot/tikiwiki/tiki/templates/mail/unsuccessful_logins.tpl,v 1.3 2007-08-31 11:41:48 sylvieg Exp $ *}
{$msg}
{tr}Please visit this link before login again:{/tr}
{$mail_machine}?user={$user|escape:'url'}&pass={$mail_apass}