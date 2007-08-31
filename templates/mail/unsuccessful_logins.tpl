{* $Header: /cvsroot/tikiwiki/tiki/templates/mail/unsuccessful_logins.tpl,v 1.2 2007-08-31 04:24:10 sampaioprimo Exp $ *}
{$msg}
{tr}Please visit this link before login again{/tr}:
{$mail_machine}?user={$user|escape:'url'}&pass={$mail_apass}