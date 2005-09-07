{tr}Hi{/tr} {$mail_user},

{tr}Someone coming from IP Address{/tr} {$mail_ip} {if $clearpw eq 'y'}{tr}requested a reminder of the password for your account{/tr}{else}{tr}requested password reset for your account{/tr} {/if} ({$mail_site}).

{if $clearpw eq 'y'}
{tr}Since this is your registered email address we inform that the password for this account is{/tr} {$mail_pass}
{else}
{tr}Please follow the steps below to have your password properly reset:

1. Click the following link to confirm you wish to reset your password:
{$mail_machine}?user={$mail_user|escape:'url'}&actpass={$mail_apass}

2. Click the following link to go to the screen where you must enter a new "permanent" password. Please pick a password only you will know, and don't share it with anyone else.
{$mail_machine}/tiki-change_password.php?user={$mail_user}&oldpass={$mail_pass}

Alternatively, go to {$mail_site} and login using your username and temporary password:
 Username:   {$mail_user}
 Temporary password:  {$mail_pass}

3. Done! You should be logged in.{/tr}

{tr}Important: Username & password are CaSe SenSitiVe{/tr}

{tr}Important: The old password remains active if you don't click the link above.{/tr}
{/if}
