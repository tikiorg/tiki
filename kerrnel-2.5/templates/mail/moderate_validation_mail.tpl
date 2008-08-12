{tr}Hi{/tr},

{if $mail_again}
{tr}{$mail_user} <{$mail_email}> has requested a new password on {$mail_site}, but you need to validate his account first{/tr}
{else}
{$mail_user} <{$mail_email}> {tr}has requested an account on{/tr} {$mail_site}
{/if}

{tr}To validate that account, please follow the link:{/tr}
{$mail_machine}?user={$mail_user|escape:'url'}&pass={$mail_apass}


{tr}best regards{/tr},
{tr}your Tikiwiki{/tr}

            
