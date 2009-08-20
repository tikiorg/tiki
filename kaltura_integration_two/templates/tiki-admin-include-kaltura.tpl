<form action="tiki-admin.php?page=kaltura" method="post">

<br/>
<fieldset class="admin">
<legend>{tr}Kaltura Parnter Settings{/tr}</legend>
<div class="adminoptionlabel">
<label for="partnerId">{tr}Partner Id{/tr}: </label><input type="text" name="partnerId" id="partnerId" value="{$partnerId|escape}" /></div>
<div class="adminoptionlabel">
<label for="secret">{tr}Secret{/tr}: </label><input type="text" name="secret" id="secret" value="{$secret|escape}" /></div>
<div class="adminoptionlabel">
<label for="adminSecret">{tr}Admin Secret{/tr}: </label><input type="text" name="adminSecret" id="adminSecret" value="{$adminSecret|escape}" /></div>
</fieldset>

<br/>

<fieldset class="admin">
<legend>{tr}Kaltura Dynacmic Player{/tr}</legend>
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
