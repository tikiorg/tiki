{* $Id$ *}

{title help="Drawings"}{tr}Admin drawings{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To create a drawing in a Wiki page, use {literal}{draw name=foo}{/literal}, where foo is the name of the drawing. After saving, click on the drawing link.{/tr}{/remarksbox}

{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div align="center">
	<a href='#' onclick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$draw_info.name}','','menubar=no,width=252,height=25');">
		<img width='154' height='98' src='img/wiki/{$draw_info.filename_draw}' alt='{tr}Edit{/tr}' /></a>
</div>
{/if}

<h2>{tr}Available drawings{/tr}:</h2>
{include file='find.tpl'}

<form method="post" action="tiki-admin_drawings.php">
<input type="hidden" name="ver" value="{$smarty.request.ver|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr>
{if $smarty.request.ver}
<th><input type="submit" name="del" value="{tr}x{/tr} " /></th>
{/if}
<th>{tr}Name{/tr}</th>
<th>{tr}Ver{/tr}</th>
<th>{tr}Versions{/tr}</th>
<th>{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
{if $smarty.request.ver}
<td class="{cycle advance=false}">
<input type="checkbox" name="draw[{$items[user].drawId}]" />
</td>
{/if}
<td class="{cycle advance=false}">
{if $smarty.request.ver}
{$items[user].name}
{else}
<a href="tiki-admin_drawings.php?ver={$items[user].name}" class="link">
{$items[user].name}</a>
{/if}
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{$items[user].version}
</td>
<td style="text-align:right;" class="{cycle advance=false}">
{$items[user].versions}
</td>
<td class="{cycle}">
{if not $smarty.request.ver}
<a title="{tr}Edit{/tr}" class="link" href='#' onclick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$items[user].name}','','menubar=no,width=252,height=25');">{icon _id='page_edit'}</a> &nbsp;
{/if}
<a title="{tr}View{/tr}" href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;previewfile={$items[user].drawId}" class="link">
{icon _id='monitor' alt='{tr}View{/tr}'}</a> &nbsp;
{if $smarty.request.ver}
	<a title="{tr}Delete{/tr}" href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;remove={$items[user].drawId}" class="link" >
	{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
{else}
	<a title="{tr}Delete{/tr}" href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;removeall={$items[user].name}" class="link" >
	{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="4" class="odd">{tr}No records found{/tr}</td></tr>
{/section}
</table>
</form>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
