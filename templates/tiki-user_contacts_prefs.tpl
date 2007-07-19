{*Smarty template*}
<h1><a class="pagetitle" href="tiki-user_contacts_prefs.php">{tr}User Contacts Preferences{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}User+Contacts+Prefs" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}edit user contacts preferences{/tr}">
<img border='0' width='16' height='16' src='pics/icons/help.png' alt="{tr}Help{/tr}" /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_contacts_prefs.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}edit quiz stats tpl{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" height="16" width="16" alt='{tr}Edit template{/tr}' />
</a>
{/if}</h1>

<!-- this bar is created by a ref to {include file=tiki-mytiki_bar.tpl} :) -->
{include file=tiki-mytiki_bar.tpl}
<div style="float:right;margin:5px;">
	<span class="button2"><a href="tiki-contacts.php" class="linkbut" title="{tr}Contacts{/tr}">{tr}Contacts{/tr}</a></span>
</div>

<table class="admin" style="clear:both;"><tr><td>

{if $feature_tabs eq 'y'}
{cycle values="1,2" name=tabs print=false advance=false}
<div id="page-bar">
	<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Options{/tr}</a></span>
	<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Manage Fields{/tr}</a></span>
</div>
{/if}

{cycle name=content values="1,2" print=false advance=false}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

<div class="cbox">
<div class="cbox-title">{tr}Options{/tr}</div>
<div class="cbox-data">
<form method='POST' action='tiki-user_contacts_prefs.php'>
<table class="admin">
	<tr><td class="form">{tr}Default view{/tr}:</td>
	<td class="form">
		<input type='radio' name='user_contacts_default_view' value='list' {if $user_contacts_default_view eq 'list'}checked="checked"{/if}/>{tr}list view{/tr}
		<input type='radio' name='user_contacts_default_view' value='group' {if $user_contacts_default_view neq 'list'}checked="checked"{/if}/>{tr}group view{/tr}
	</td></tr>
	<tr><td colspan="2" class="button">
		<input type='submit' name='prefs' value='{tr}Change preferences{/tr}' />
	</td></tr>
</table>
</form>
</div>
</div>
</div>

<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

<div class="cbox">
<div class="cbox-title">{tr}Manage Fields{/tr}</div>
<div class="cbox-data">
<form method='POST' action='tiki-user_contacts_prefs.php'>
<table class="admin"><tr><td>
<table class="normal">
  <tr>
	<td class="heading" colspan=2>{tr}Order{/tr}</td>
  	<td class="heading">{tr}Field{/tr}</td>
	<td class="heading">{tr}Action{/tr}</td>
  </tr>
{cycle values="odd,even" print=false}
  {foreach from=$exts item=ext key=k name=e}
  <tr>
  	<td class="{cycle advance=false}" width="2%">
		{if not $smarty.foreach.e.first}
		<a href="?ext_up={$ext.fieldId}" title="{tr}up{/tr}"><img src="pics/icons/resultset_up.png" border="0" height="16" width="16" alt='{tr}up{/tr}' /></a>
		{/if}
	</td>
  	<td class="{cycle advance=false}" width="2%">
		{if not $smarty.foreach.e.last}
		<a href="?ext_down={$ext.fieldId}" title="{tr}down{/tr}"><img src="pics/icons/resultset_down.png" border="0" height="16" width="16" alt='{tr}down{/tr}' /></a>
		{/if}
	</td>
	<td class="{cycle advance=false}">{$ext.fieldname|escape}</td>
  	<td class="{cycle advance=true}">
		{if $ext.show eq 'y'}
		<a href="?ext_hide={$ext.fieldId}" style="margin-left:20px;" title="{tr}hide{/tr}"><img src="pics/icons/no_eye.png" border="0" height="16" width="16" alt='{tr}hide{/tr}' /></a>
		{else}
		<a href="?ext_show={$ext.fieldId}" style="margin-left:20px;" title="{tr}show{/tr}"><img src="pics/icons/eye.png" border="0" height="16" width="16" alt='{tr}show{/tr}' /></a>
		{/if}
		<a href="?ext_remove={$ext.fieldId}" style="margin-left:20px;" title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Delete{/tr}' /></a>
	</td>
  </tr>
  {/foreach}
{/cycle}
</table>
</td></tr>
<tr><td colspan="2" class="button">
	{tr}Add{/tr}: <input type='text' name='ext_add' /> <input type='submit' name='add_fields' value='{tr}Add{/tr}' />
</td></tr>
</table>
</form>
</div>
</div>
</div>

</td></tr></table>
