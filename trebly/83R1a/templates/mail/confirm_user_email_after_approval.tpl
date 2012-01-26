{* $Id: confirm_user_email_after_approval.tpl 34646 2011-05-27 02:35:57Z chealer $ *}
{tr}The administrator approved your account.{/tr}
{tr}To validate your account and login to the site, please click on the following link:{/tr}
{$mail_machine}?user={$mail_user|escape:'url'}&pass={$mail_apass}