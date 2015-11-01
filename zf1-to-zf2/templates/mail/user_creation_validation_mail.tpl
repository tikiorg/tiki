{tr}Hi{/tr},

{tr}An administrator of the site below has added you as a new user:{/tr}
	{if !empty($prefs.sitetitle)}{$prefs.sitetitle} - {/if}{$mail_site}

{tr}If you want to confirm your membership in this site, click on the following link to login for the first time:{/tr}
	{$mail_machine}?user={$mail_user|escape:'url'}&pass={$mail_apass}

{if !empty($mail_pass)}
{tr}Your authentication credentials are:{/tr}
	{tr}Username:{/tr} {$mail_user}
	{tr}Password:{/tr} {$mail_pass}
{/if}

{tr}Welcome to the site!{/tr}

