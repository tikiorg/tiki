{* $Id$ *}{tr}Hi{/tr},

{tr}An administrator of the {$prefs.mail_template_custom_text}site below has added you as a new user:{/tr}
	{if !empty($prefs.sitetitle)}{$prefs.sitetitle} - {/if}{$mail_site}

{tr}If you want to confirm your membership in this site, click on the following link to login for the first time:{/tr}
	{$mail_machine}?user={$mail_user|escape:'url'}&pass={$mail_apass}

{tr}Welcome to the site!{/tr}

