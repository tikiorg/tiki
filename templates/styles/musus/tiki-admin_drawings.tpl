{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-admin_drawings.tpl,v 1.3 2004-01-26 03:46:17 musus Exp $ *}
<a class="pagetitle" href="tiki-admin_drawings.php">{tr}Admin drawings{/tr}</a>
<!-- the help link info -->  
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=DrawingsDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin Drawings{/tr}">{$helpIcon $helpIconDesc}</a>
{/if}
<!-- link to tpl -->
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_drawings.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin Drawings tpl{/tr}"><img alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>{/if}
<!-- begin -->
<br /><br />
{if $preview eq 'y'}
<div align="center">
	<a href="#" onclick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$draw_info.name}','','menubar=no,width=252,height=25');">
		<img src="img/wiki/{$draw_info.filename_draw}" alt="click to edit" />
	</a>
</div>
{/if}

<form method="post" action="tiki-admin_drawings.php">
<input type="hidden" name="ver" value="{$smarty.request.ver|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="findtable"><tr><td>{tr}Find{/tr}:<td><input type="text" name="find" value="{$find|escape}" /></td></tr></table>
</form>
<h3>{tr}Available drawings{/tr}:</h3>
<form method="post" action="tiki-admin_drawings.php">
<input type="hidden" name="ver" value="{$smarty.request.ver|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="find" value="{$find|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table>
<tr class="heading">
{if $smarty.request.ver}
<td><input type="submit" name="del" value="{tr}x{/tr} " /></td>
{/if}
<th>{tr}Name{/tr}</th>
<th>{tr}Ver{/tr}</a></th>
<th>{tr}Versions{/tr}</a></th>
<th>{tr}Action{/tr}</a></th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
{if $smarty.request.ver}
	<td class="{cycle advance=false}"><input type="checkbox" name="draw[{$items[user].drawId}]" /></td>
{/if}
<td class="{cycle advance=false}">
{if $smarty.request.ver}
{$items[user].name}
{else}
<a href="tiki-admin_drawings.php?ver={$items[user].name}">{$items[user].name}</a>
{/if}
</td>
<td style="text-align:right;" class="{cycle advance=false}">{$items[user].version}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$items[user].versions}</td>
<td class="{cycle}">
{if $smarty.request.ver}
	  <a href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;remove={$items[user].drawId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this drawing?{/tr}')" 
title="{tr}Click here to delete this drawing{/tr}"><img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>  
{else}
	  <a href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;removeall={$items[user].name}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this drawing?{/tr}')" 
title="{tr}Click here to delete this drawing{/tr}"><img alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>  
{/if}
<a href="tiki-admin_drawings.php?ver={$smarty.request.ver}&amp;previewfile={$items[user].drawId}"><img src="img/icons/ico_img.gif" alt="{tr}view{/tr}" /></a>
{if not $smarty.request.ver}
<a href="#" onclick="javascript:window.open('tiki-editdrawing.php?path={$path}&amp;drawing={$items[user].name}','','menubar=no,width=252,height=25');"><img alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
{/if}
</td>
</tr>
{sectionelse}
<tr class="odd"><td colspan="4">{tr}No records found{/tr}</td></tr>
{/section}
</table>
</form>

<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_drawings.php?ver={$smart.request.ver}&amp;offset={$prev_offset}&amp;find={$find}">{tr}prev{/tr}</a>] 
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
 [<a class="prevnext" href="tiki-admin_drawings.php?ver={$smart.request.ver}&amp;offset={$next_offset}&amp;find={$find}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_drawings.php?offset={$selector_offset}">{$smarty.section.foo.index_next}</a> 
{/section}
{/if}
</div>
