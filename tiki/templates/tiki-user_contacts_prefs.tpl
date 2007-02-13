{*Smarty template*}
<h1><a class="pagetitle" href="tiki-user_contacts_prefs.php">{tr}User Contacts Preferences{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}User+Contacts+Prefs" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}edit user contacts preferences{/tr}">
<img border='0' width='16' height='16' src='pics/icons/help.png' alt="{tr}help{/tr}" /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_contacts_prefs.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit quiz stats tpl{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" height="16" width="16" alt='{tr}Edit template{/tr}' />
</a>
{/if}</h1>

<!-- this bar is created by a ref to {include file=tiki-mytiki_bar.tpl} :) -->
{include file=tiki-mytiki_bar.tpl}
<h2>{tr}User Contacts Preferences{/tr}</h2>

<h3>Contacts Possible Fields:</h3>
<form method='POST' action='tiki-user_contacts_prefs.php'>
<table class="normal">
  <tr>
  	<td class="heading">{tr}Field{/tr}</td>
	<td class="heading">{tr}Action{/tr}</td>
  </tr>
{cycle values="odd,even" print=false}
  {foreach from=$exts item=ext key=k}
  <tr>
	<td class="{cycle advance=false}">{$ext.fieldname|escape}</td>
  	<td class="{cycle advance=true}">
		{if $ext.show eq 'y'}
		<a href="?ext_hide={$ext.fieldId}" style="margin-left:20px;" title="{tr}hide{/tr}"><img src="pics/icons/no_eye.png" border="0" height="16" width="16" alt='{tr}hide{/tr}' /></a>
		{else}
		<a href="?ext_show={$ext.fieldId}" style="margin-left:20px;" title="{tr}show{/tr}"><img src="pics/icons/eye.png" border="0" height="16" width="16" alt='{tr}show{/tr}' /></a>
		{/if}
		<a href="?ext_remove={$ext.fieldId}" style="margin-left:20px;" title="{tr}delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
	</td>
  </tr>
  {/foreach}
{/cycle}
</table>
add: <input type='text' name='ext_add' /> <input type='submit' value='Add' />
</form>
