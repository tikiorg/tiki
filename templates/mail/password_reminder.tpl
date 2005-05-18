{tr}Hi{/tr} {$mail_user},

{tr}someone coming from IP Address{/tr} {$mail_ip} {if $clearpw eq 'y'}{tr}requested a reminder of the password for the{/tr}{else}{tr}requested a reset of the password for the{/tr} {/if}
{tr}account{/tr} {$mail_user} ({$mail_site}).

{tr}Since this is your registered email address we inform that the password for this account is{/tr} {$mail_pass}

{if $clearpw eq 'n'}
{tr}The old password remains active until you activate the new one by following this link:{/tr}

{$mail_machine}?user={$mail_user|escape:'url'}&actpass={$mail_apass}

{tr}This is only a temporary password. After you logged in with it, you will get to the 'change password' dialog.{/tr}
{/if}