{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}&quot;Modules&quot; are the boxes of content in the right &amp; left columns{/tr}. {tr}Select{/tr} 
<a class="rbox-link" href="tiki-admin_modules.php">{tr}Admin &gt; Modules{/tr}</a> {tr}from the menu to create and edit modules{/tr}.
{/remarksbox}

<form action="tiki-admin.php?page=module" method="post">
<input type="hidden" name="modulesetup" />
<div class="cbox">
<table class="admin"><tr><td>
<div align="center" style="padding:1em"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
<fieldset><legend>{tr}{$crumbs[$crumb]->description}{/tr}{if $prefs.feature_help eq 'y'} {help crumb=$crumbs[$crumb]}{/if}</legend>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="feature_modulecontrols" name="feature_modulecontrols" {if $prefs.feature_modulecontrols eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_modulecontrols">{tr}Show module controls{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="Module+Control"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" id="user_assigned_modules" name="user_assigned_modules" {if $prefs.user_assigned_modules eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="user_assigned_modules">{tr}Users can configure modules{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="Users+Configure+Modules"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="">{tr}Users can shade modules{/tr}:</label> <select name="user_flip_modules">
		<option value="y" {if $prefs.user_flip_modules eq 'y'}selected="selected"{/if}>{tr}Always{/tr}</option>
		<option value="module" {if $prefs.user_flip_modules eq 'module'}selected="selected"{/if}>{tr}Module decides{/tr}</option>
		<option value="n" {if $prefs.user_flip_modules eq 'n'}selected="selected"{/if}>{tr}Never{/tr}</option>
		</select>{if $prefs.feature_help eq 'y'} {help url="Users+Shade+Modules"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="modallgroups" id="general-modules" {if $prefs.modallgroups eq 'y'}checked="checked"{/if} {popup text="Hint: If you lose your login module, use tiki-login_scr.php to be able to login!" textcolor=red}/></div>
	<div class="adminoptionlabel"><label for="general-modules">{tr}Display modules to all groups always{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="modseparateanon" id="general-anon_modules" {if $prefs.modseparateanon eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="general-anon_modules">{tr}Hide anonymous-only modules from registered users{/tr}.</label></div>
</div>
</fieldset>
<div align="center" style="padding:1em"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>
