{* $Id$ *}{if $prefs.login_autogenerate eq 'y'}
	<strong>{tr _0=$prefs.mail_template_custom_text}Your %0account ID{/tr} {$username} {tr}has been generated.{/tr}</strong>
{/if}
{tr _0=$prefs.mail_template_custom_text}Thank you for your %0registration.{/tr} <a href="tiki-login_scr.php?clearmenucache=y">{tr}You may log in now.{/tr}</a>
