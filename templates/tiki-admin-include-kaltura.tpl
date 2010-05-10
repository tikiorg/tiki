<form action="tiki-admin.php?page=kaltura" method="post">

<br/>
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
