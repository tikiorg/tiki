<form action="tiki-admin.php?page=video" method="post">

{tabset name="admin_video"}
{tab name="{tr}Kaltura{/tr}"}
{remarksbox type="info" title="{tr}Kaltura Registration{/tr}" }{tr}If you don't have a Kaltura Partner Id, {/tr}<a href="http://corp.kaltura.com/about/signup">{tr}click here{/tr}</a> {tr}to register.{/tr}{/remarksbox}

<fieldset class="admin">
<legend>{tr}Activate the feature{/tr}</legend>
	{preference name=feature_kaltura}
</fieldset>

<fieldset class="admin">
<legend>{tr}Kaltura Partner Settings{/tr}</legend>
	{preference name=partnerId}
	{preference name=secret}
	{preference name=adminSecret}
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Dynamic Player{/tr}</legend>
	{preference name=kdpUIConf}
	{preference name=kdpWidget}
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Contribution Wizard{/tr}</legend>
	{preference name=kcwUIConf}
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Remix Editors{/tr}</legend>
	{preference name=kseUIConf}
	{preference name=kaeUIConf}
</fieldset>

<br/>

<div align="center" style="padding:1em;"><input type="submit" name="kaltura" value="{tr}Save{/tr}" /></div>
</form>
{/tab}
{tab name="{tr}Ustream Watershed{/tr}"}
{remarksbox type="info" title="{tr}Ustream Watershed Registration{/tr}" }{tr}If you don't have a Watershed account, {/tr}<a href="https://watershed.ustream.tv/">{tr}you can find out more about it here{/tr}.</a>{/remarksbox}
Feature coming soon...
{/tab}
{/tabset}