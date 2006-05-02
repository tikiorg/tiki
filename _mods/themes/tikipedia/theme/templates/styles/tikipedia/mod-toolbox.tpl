{* $Header: /cvsroot/tikiwiki/_mods/themes/tikipedia/theme/templates/styles/tikipedia/mod-toolbox.tpl,v 1.1 2006-05-02 05:54:38 chibaguy Exp $ *}
{* Module layout with controls *}

<div class="box box-toolbox|escape}">
<div class="box-title">

{if $module_flip eq 'y'}
<table class="box-title">
  <tr>
    <td ondblclick="javascript:icntoggle('mod-toolbox','mo.png');">
<span class="box-titletext">{/if}{tr}toolbox{/tr}{if $module_flip eq 'y'}</span>
    </td>
    <td width="11">
      <a title="{tr}Hide module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-toolbox','mo.png');"><img name="mod-toolboxicn" class="flipmodimage" src="img/icons/omo.png" border="0" alt="[{tr}hide{/tr}]" /></a>
</td>
</tr>
</table>
{/if}

</div><div id="mod-toolbox" style="display: block" class="box-data">
{if $print_page ne 'y'}
	{if $cached_page eq 'y'}
		<a title="{tr}refresh{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;refresh=1" class="linkmenu">{tr}refresh{/tr}</a><br />
	{/if}
	{if $feature_wiki_print eq 'y'}
		<a title="{tr}print{/tr}" href="tiki-print.php?page={$page|escape:"url"}" class="linkmenu">{tr}print{/tr}</a><br />
	{/if}
	{if $feature_wiki_pdf eq 'y'}
		<a title="{tr}create pdf{/tr}" href="tiki-config_pdf.php?page={$page|escape:"url"}" class="linkmenu">{tr}pdf{/tr}</a><br />
	{/if}
	{if $user and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<a title="{tr}Save to notepad{/tr}" href="tiki-index.php?page={$page|escape:"url"}&amp;savenotepad=1" class="linkmenu">{tr}save{/tr}</a><br />
	{/if}
	{if $wiki_feature_3d eq 'y'}
		<a title="{tr}3d browser{/tr}" href="javascript:wiki3d_open('{$page|escape}',{$wiki_3d_width}, {$wiki_3d_height})" class="linkmenu">{tr}3d browser{/tr}</a><br />
	{/if}
	{if $user and $feature_user_watches eq 'y'}
		{if $user_watching_page eq 'n'}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=add" class="linkmenu">{tr}monitor this page{/tr}</a><br />
		{else}
			<a href="tiki-index.php?page={$page|escape:"url"}&amp;watch_event=wiki_page_changed&amp;watch_object={$page|escape:"url"}&amp;watch_action=remove" class="linkmenu">{tr}stop monitoring this page{/tr}</a><br />
		{/if}
	{/if}
		{if $feature_backlinks eq 'y' and $backlinks}
		<div class="linkmenu">
	<form action="tiki-index.php" method="post">
	<select name="page" onchange="page.form.submit()">
	<option>{tr}backlinks{/tr}...</option>
	{section name=back loop=$backlinks}
		<option value="{$backlinks[back].fromPage}">{$backlinks[back].fromPage}</option>
	{/section}
	</select>
	</form>
	</div>
		{/if}
		{if !$page_ref_id and count($showstructs) ne 0}
	<div class="linkmenu">
	<form action="tiki-index.php" method="post">
	<select name="page_ref_id" onchange="page_ref_id.form.submit()">
	<option>{tr}Structures{/tr}...</option>
	{section name=struct loop=$showstructs}
		<option value="{$showstructs[struct].req_page_ref_id}">
			{if $showstructs[struct].page_alias} 
				{$showstructs[struct].page_alias}
			{else}
				{$showstructs[struct].pageName}
			{/if}
		</option>
	{/section}
	</select>
	</form>
	</div>
		{/if}
	{if $feature_multilingual == 'y'}
		{include file="translated-lang.tpl" td='y'}
	{/if}
	{if $feature_multilingual eq 'y' and $tiki_p_edit eq 'y' and !$lock}
		<a href="tiki-edit_translation.php?page={$page|escape:'url'}" class="linkmenu">{tr}translation{/tr}</a>
	{/if}
{/if}	
</div>
</div>
