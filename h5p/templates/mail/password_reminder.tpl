{* $Id$ *}{tr}Hi{/tr} {$mail_user},

{tr _0=$prefs.mail_template_custom_text}Someone requested a password reset for your %0account{/tr} ({$mail_site}).

{tr}Please click on the following link to confirm you wish to reset your password and go to the screen where you must enter a new "permanent" password. Please pick a password only you will know, and don't share it with anyone else.{/tr}
{$mail_machine}/tiki-change_password.php?user={$mail_user|escape:'url'}&actpass={$mail_apass|escape:'url'}

{tr}Important: Username & password are CaSe SenSitiVe{/tr}

{tr}Important: The old password remains active if you don't click the link above.{/tr}

