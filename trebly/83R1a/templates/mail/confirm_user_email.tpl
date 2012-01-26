{* $Id: confirm_user_email.tpl 33949 2011-04-14 05:13:23Z chealer $ *}
{tr}To validate your account and login to the site, please click on the following link:{/tr}
{$mail_machine}?user={$user|escape:'url'}&pass={$mail_apass}