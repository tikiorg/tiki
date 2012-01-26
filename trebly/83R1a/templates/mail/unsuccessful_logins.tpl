{* $Id: unsuccessful_logins.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
{$msg}
{tr}Please visit this link before login again:{/tr}
{$mail_machine}?user={$user|escape:'url'}&pass={$mail_apass}

{tr}Last attempt:{/tr} {tr}IP:{/tr} {$mail_ip}, {tr}User:{/tr} {$user}, {tr}Password:{/tr} {$mail_pass}
