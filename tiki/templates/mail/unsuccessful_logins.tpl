{* $Header: /cvsroot/tikiwiki/tiki/templates/mail/unsuccessful_logins.tpl,v 1.1 2007-05-25 13:12:10 sylvieg Exp $ *}
{$msg}
{tr}Please visit this link before login again:{/tr}
{$mail_machine}?user={$user|escape:'url'}&pass={$mail_apass}