<form action="tiki-admin.php?page=kaltura" method="post">

<br/>
{remarksbox type="info" title="{tr}Kaltura Registration{/tr}" }{tr}If you don't have a Kaltura Partner Id, {/tr}<a href="http://corp.kaltura.com/about/signup">{tr}click here{/tr}</a> {tr}to register.{/tr}{/remarksbox}
<fieldset class="admin">
<legend>{tr}Kaltura Partner Settings{/tr}</legend>
<div class="adminoptionlabel">
<label for="partnerId">{tr}Partner Id{/tr}: </label><input type="text" name="partnerId" id="partnerId" value="{$partnerId|escape}" /></div>
<div class="adminoptionlabel">
<label for="secret">{tr}User Secret{/tr}: </label><input type="text" name="secret" id="secret" value="{$secret|escape}" size=45 /></div>
<div class="adminoptionlabel">
<label for="adminSecret">{tr}Admin Secret{/tr}: </label><input type="text" name="adminSecret" id="adminSecret" value="{$adminSecret|escape}" size=45 /></div>
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Dynamic Player{/tr}</legend>
<div class="adminoptionlabel">
<label for="kdpUIConf">{tr}KDP UI Configuration Id{/tr}: </label><input type="text" name="kdpUIConf" id="kdpUIConf" value="{$kdpUIConf|escape}" /></div>
<div class="adminoptionlabel">
<label for="kdpWidget">{tr}KDP Widget Id{/tr}: </label><input type="text" name="kdpWidget" id="kdpWidget" value="{$kdpWidget|escape}" /></div>
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Contribution Wizard{/tr}</legend>
<div class="adminoptionlabel">
<label for="kcwUIConf">{tr}KCW UI Configuration Id{/tr}: </label><input type="text" name="kcwUIConf" id="kcwUIConf" value="{$kcwUIConf|escape}" /></div>
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Remix Editors{/tr}</legend>
<div class="adminoptionlabel">
<label for="kseUIConf">{tr}Kaltura Simple Editor UI Configuration Id{/tr}: </label><input type="text" name="kseUIConf" id="kseUIConf" value="{$kseUIConf|escape}" /></div>
<div class="adminoptionlabel">
<label for="kaeUIConf">{tr}Kaltura Advance Editor UI Configuration Id{/tr}: </label><input type="text" name="kaeUIConf" id="kaeUIConf" value="{$kaeUIConf|escape}" /></div>

</fieldset>

<br/>

<div align="center" style="padding:1em;"><input type="submit" name="kaltura" value="{tr}Save{/tr}" /></div>
</form>
