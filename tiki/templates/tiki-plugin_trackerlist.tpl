{* $Id: tiki-plugin_trackerlist.tpl,v 1.33.2.3 2008-01-16 14:56:07 sylvieg Exp $ *}
{if $showtitle eq 'y'}<div class="pagetitle">{$tracker_info.name}</div>{/if}
{if $showdesc eq 'y'}<div class="wikitext">{$tracker_info.description}</div>{/if}

{if $cant_pages > 1 or $tr_initial}
<div align="center">
{section name=ini loop=$initials}
{if $tr_initial and $initials[ini] eq $tr_initial}
<span class="button2"><span class="linkbuton">{$initials[ini]|capitalize}</span></span> . 
{else}
<a href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_initial={$initials[ini]}{if $tr_sort_mode}&amp;tr_sort_mode={$tr_sort_mode}{/if}&amp;tr_offset=0" class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_initial={if $tr_sort_mode}&amp;tr_sort_mode={$tr_sort_mode}{/if}&amp;tr_offset=0" class="prevnext">{tr}All{/tr}</a>
</div>
{/if}

{if $checkbox && $items|@count gt 0}<form method="post" action="{$checkbox.action}">{/if}

<table class="normal wikiplugin_trackerlist">
{if $showfieldname ne 'n'}
<tr>
{if $checkbox}<td class="heading">{$checkbox.title}</td>{/if}
{if ($showstatus ne 'n') and ($tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $perms.tiki_p_admin_trackers eq 'y'))}
	<td class="heading auto" style="width:20px;">&nbsp;</td>
{/if}

{foreach key=jx item=ix from=$fields}
{if $ix.isPublic eq 'y' and ($ix.isHidden eq 'n' or $tiki_p_admin_trackers eq 'y') and $ix.type ne 'x' and $ix.type ne 'h'}
{if $ix.type eq 'l'}
<td class="heading auto field{$ix.fieldId}">{$ix.name|default:"&nbsp;"}</td>
{elseif $ix.type eq 's' and $ix.name eq "Rating"}
{if $perms.tiki_p_tracker_view_ratings eq 'y'}
<td class="heading auto field{$ix.fieldId}"{if $perms.tiki_p_tracker_vote_ratings eq 'y'} colspan="2"{/if}>
<a class="tableheading" href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_sort_mode=f_{if 
	$tr_sort_mode eq 'f_'|cat:$ix.fieldId|cat:'_asc'}{$ix.fieldId}_desc{else}{$ix.fieldId}_asc{/if}{if $tr_offset}&amp;tr_offset={$tr_offset}{/if}{if $tr_initial}&amp;tr_initial={$tr_initial}{/if}">{$ix.name|default:"&nbsp;"}</a></td>
{/if}
{else}
<td class="heading auto field{$ix.fieldId}"{if $ix.type eq 's' and $ix.name eq "Rating"} colspan="2"{/if}>
<a class="tableheading" href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_sort_mode=f_{if 
	$tr_sort_mode eq 'f_'|cat:$ix.fieldId|cat:'_asc'}{$ix.fieldId}_desc{else}{$ix.fieldId}_asc{/if}{if $tr_offset}&amp;tr_offset={$tr_offset}{/if}{if $tr_initial}&amp;tr_initial={$tr_initial}{/if}">{$ix.name|default:"&nbsp;"}</a></td>
{/if}
{/if}
{/foreach}
{if $showcreated eq 'y'}
<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_sort_mode={if 
	$tr_sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}{if $tr_offset}&amp;tr_offset={$tr_offset}{/if}{if $tr_initial}&amp;tr_initial={$tr_initial}{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $showlastmodif eq 'y'}
<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_sort_mode={if 
	$tr_sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}{if $tr_offset}&amp;tr_offset={$tr_offset}{/if}{if $tr_initial}&amp;tr_initial={$tr_initial}{/if}">{tr}LastModif{/tr}</a></td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td class="heading" width="5%">{tr}Coms{/tr}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<td class="heading" width="5%">{tr}atts{/tr}</td>
{/if}
</tr>
{/if}

{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr class="{cycle}">
{if $checkbox}<td><input type="checkbox" name="{$checkbox.name}[]" value="{$items[user].field_values[$checkbox.ix].value}" /></td>{/if}
{if ($showstatus ne 'n') and ($tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $perms.tiki_p_admin_trackers eq 'y'))}<td class="auto" style="width:20px;">
{assign var=ustatus value=$items[user].status|default:"c"}
{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}
</td>
{/if}

{* ------------------------------------ *}
{section name=ix loop=$items[user].field_values}
{if $items[user].field_values[ix].isPublic eq 'y' and ($items[user].field_values[ix].isHidden eq 'n' or $tiki_p_admin_trackers eq 'y') and $items[user].field_values[ix].type ne 'x' and $items[user].field_values[ix].type ne 'h'}
<td class="auto">
	{include file="tracker_item_field_value.tpl" item=$items[user] field_value=$items[user].field_values[ix] list_mode="y"
		$tiki_p_view_trackers=$perms.tiki_p_view_trackers $tiki_p_modify_tracker_items=$perms.tiki_p_modify_tracker_items $tiki_p_comment_tracker_items=$perms.tiki_p_comment_tracker_items}
</td>
{/if}
{/section}
{* ------------------------------------ *}

{if $showcreated eq 'y'}
<td>{if $tracker_info.showCreatedFormat}{$items[user].created|tiki_date_format:$tracker_info.showCreatedFormat}{else}{$items[user].created|tiki_short_datetime}{/if}</td>
{/if}
{if showlastmodif eq 'y'}
<td>{if $tracker_info.showLastModifFormat}{$items[user].lastModif|tiki_date_format:$tracker_info.showLastModifFormat}{else}{$items[user].lastModif|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td  style="text-align:center;">{$items[user].comments}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and $tracker_info.showAttachments eq 'y'}
<td  style="text-align:center;"><a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$items[user].itemId}&amp;show=att" 
link="{tr}List Attachments{/tr}"><img src="img/icons/folderin.gif" border="0" alt="{tr}List Attachments{/tr}" 
/></a>{$items[user].attachments}</td>
{/if}
</tr>
{/section}
</table>
{if $items|@count eq 0}
{tr}No records found{/tr}
{elseif $checkbox}
<br />
{if $checkbox.tpl}{include file=$checkbox.tpl}{/if}
<input type="submit" name="{$checkbox.submit}" value="{tr}{$checkbox.title}{/tr}" /></form>
{/if}

{if $cant_pages > 1 or $tr_initial}
<br />
<div class="mini">
{if $tr_prev_offset >= 0}
[<a class="prevnext" href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_offset={$tr_prev_offset}{
	if $tr_initial}&amp;tr_initial={$tr_initial}{/if}{
	if $tr_sort_mode}&amp;tr_sort_mode={$tr_sort_mode}{/if}"
>{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $tr_next_offset >= 0}
&nbsp;[<a class="prevnext" href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_offset={$tr_next_offset}{
	if $tr_initial}&amp;tr_initial={$tr_initial}{/if}{
	if $tr_sort_mode}&amp;tr_sort_mode={$tr_sort_mode}{/if}"
>{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="{$smarty.server.PHP_SELF}?{if $page}page={$page|escape:url}&amp;{/if}tr_offset={$selector_offset}{
	if $tr_initial}&amp;tr_initial={$tr_initial}{/if}{
	if $tr_sort_mode}&amp;tr_sort_mode={$tr_sort_mode}{/if}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
{/if}

