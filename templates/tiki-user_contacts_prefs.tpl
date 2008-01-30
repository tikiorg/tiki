{*Smarty template*}
<h1><a class="pagetitle" href="tiki-user_contacts_prefs.php">{tr}User Contacts Preferences{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}User+Contacts+Prefs" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Edit User Contacts Preferences{/tr}">
{icon _id='help'}</a>{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_contacts_prefs.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Edit Quiz Stats Tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}
</a>
{/if}</h1>

<!-- this bar is created by a ref to {include file=tiki-mytiki_bar.tpl} :) -->
{include file=tiki-mytiki_bar.tpl}
<div style="float:right;margin:5px;">
	<span class="button2"><a href="tiki-contacts.php" class="linkbut" title="{tr}Contacts{/tr}">{tr}Contacts{/tr}</a></span>
</div>

<table class="admin" style="clear:both;"><tr><td>

{if $prefs.feature_tabs eq 'y'}
{cycle values="1,2" name=tabs print=false advance=false}
<div id="page-bar">
	<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Options{/tr}</a></span>
	<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Manage Fields{/tr}</a></span>
</div>
{/if}

{cycle name=content values="1,2" print=false advance=false}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

<div class="cbox">
<div class="cbox-title">{tr}Options{/tr}</div>
<div class="cbox-data">
<form method='POST' action='tiki-user_contacts_prefs.php'>
<table class="admin">
	<tr><td class="form">{tr}Default view{/tr}:</td>
	<td class="form">
		<input type='radio' name='user_contacts_default_view' value='list' {if $user_contacts_default_view eq 'list'}checked="checked"{/if}/>{tr}List View{/tr}
		<input type='radio' name='user_contacts_default_view' value='group' {if $user_contacts_default_view neq 'list'}checked="checked"{/if}/>{tr}Group View{/tr}
	</td></tr>
	<tr><td colspan="2" class="button">
		<input type='submit' name='prefs' value='{tr}Change preferences{/tr}' />
	</td></tr>
</table>
</form>
</div>
</div>
</div>

<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

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
		<a href="?ext_up={$ext.fieldId}" title="{tr}Up{/tr}">{icon _id='resultset_up'}</a>
		{/if}
	</td>
  	<td class="{cycle advance=false}" width="2%">
		{if not $smarty.foreach.e.last}
		<a href="?ext_down={$ext.fieldId}" title="{tr}Down{/tr}">{icon _id='resultset_down'}</a>
		{/if}
	</td>
	<td class="{cycle advance=false}">{tr}{$ext.fieldname|escape}{/tr}</td>
  	<td class="{cycle advance=true}">
		{if $ext.show eq 'y'}
		<a href="?ext_hide={$ext.fieldId}" style="margin-left:20px;" title="{tr}Hide{/tr}">{icon _id='no_eye' alt='{tr}Hide{/tr}'}</a>
		{else}
		<a href="?ext_show={$ext.fieldId}" style="margin-left:20px;" title="{tr}Show{/tr}">{icon _id='eye' alt='{tr}Show{/tr}'}</a>
		{/if}
		<a href="?ext_remove={$ext.fieldId}" style="margin-left:20px;" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
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
