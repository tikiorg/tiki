<a class="pagetitle" href="tiki-view_tracker.php?trackerId={$trackerId}">{tr}Tracker{/tr}: {$tracker_info.name}</a><br/><br/>
<a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a>
{if $tiki_p_admin_trackers eq 'y'}
<a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a>
<a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a>
{if $user}
<a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;monitor=1" class="linkbut">{tr}{$email_mon}{/tr}</a>
{/if}
{/if}
<br/><br/>
<div class="simplebox">{$tracker_info.description}</div>
{if $mail_msg}
<br/><br/>
<div class="simplebox">{$mail_msg}</div>
{/if}
<form action="tiki-view_tracker.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
{section name=ix loop=$fields}
<input type="hidden" name="{$fields[ix].name|escape}" value="{$fields[ix].value|escape}" />
{/section}

{if $tiki_p_create_tracker_items eq 'y'}
<h3>{tr}Insert new item{/tr}</h3>
<table class="normal">
{section name=ix loop=$ins_fields}
<tr><td class="formcolor">{$ins_fields[ix].name}</td>
<td class="formcolor">
{if $ins_fields[ix].type eq 'u'}
<select name="ins_{$ins_fields[ix].name}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$users}
<option value="{$users[ux]|escape}">{$users[ux]}</option>
{/section}
</select>
{/if}
{if $ins_fields[ix].type eq 'g'}
<select name="ins_{$ins_fields[ix].name}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
<option value="{$groups[ux].groupName|escape}">{$groups[ux].groupName}</option>
{/section}
</select>
{/if}
{if $ins_fields[ix].type eq 'i'}
<input type="file" name="ins_{$ins_fields[ix].name}"/>
{/if}
{if $ins_fields[ix].type eq 't'}
<input type="text" name="ins_{$ins_fields[ix].name}" value="{$ins_fields[ix].value|escape}" />
{/if}
{if $ins_fields[ix].type eq 'a'}
<textarea name="ins_{$ins_fields[ix].name}" rows="4" cols="50">{$ins_fields[ix].value|escape}</textarea>
{/if}
{if $ins_fields[ix].type eq 'f'}
{html_select_date prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value end_year="+1"} {tr}at{/tr} {html_select_time prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value display_seconds=false}
{/if}
{if $ins_fields[ix].type eq 'd'}
<select name="ins_{$ins_fields[ix].name}">
{section name=jx loop=$ins_fields[ix].options_array}
<option value="{$ins_fields[ix].options_array[jx]|escape}" {if $ins_fields[ix].value eq $ins_fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
{/section}
</select>
{/if}
{if $ins_fields[ix].type eq 'c'}
<input type="checkbox" name="ins_{$ins_fields[ix].name}" {if $ins_fields[ix].value eq 'y'}checked="checked"{/if}/>
{/if}
</td>
</tr>
{/section}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
{/if}

<h3>{tr}Tracker Items{/tr}</h3>
<form action="tiki-view_tracker.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<table class="normal">
<tr><td class="heading" colspan="2">{tr}Filters{/tr}</td></tr>
<tr><td class="formcolor">{tr}Status{/tr}</td>
<td class="formcolor">
<select name="status">
<option value="" {if $status eq ''}selected="selected"{/if}>{tr}any{/tr}</option>
<option value="o" {if $status eq 'o'}selected="selected"{/if}>{tr}open{/tr}</option>
<option value="c" {if $status eq 'c'}selected="selected"{/if}>{tr}closed{/tr}</option>
</select>
{if $fields[ix].type ne 'i'}
</td></tr>
{/if}
{section name=ix loop=$fields}
{if $fields[ix].isTblVisible eq 'y' and $fields[ix].type ne 'f'}
{if $fields[ix].type ne 'i'}
<tr><td class="formcolor">{$fields[ix].name}</td>
{/if}
{if $fields[ix].type ne 'i'}
<td class="formcolor">
{/if}
{if $fields[ix].type eq 't' or $fields[ix].type eq 'a'}
<input type="text" name="{$fields[ix].name|escape}" value="{$fields[ix].value|escape}" />
{/if}
{if $fields[ix].type eq 'u'}
<select name="{$fields[ix].name|escape}">
<option value="" {if $fields[ix].value eq ''}selected="selected"{/if}>{tr}any{/tr}</option>
{section name=ux loop=$users}
<option value="{$users[ux]|escape}">{$users[ux]}</option>
{/section}
</select>
{/if}
{if $fields[ix].type eq 'g'}
<select name="{$fields[ix].name|escape}">
<option value="" {if $fields[ix].value eq ''}selected="selected"{/if}>{tr}any{/tr}</option>
{section name=ux loop=$groups}
<option value="{$groups[ux].groupName|escape}">{$groups[ux].groupName}</option>
{/section}
</select>
{/if}
{if $fields[ix].type eq 'd'}
<select name="{$fields[ix].name|escape}">
<option value="" {if $fields[ix].value eq ''}selected="selected"{/if}>{tr}any{/tr}</option>
{section name=jx loop=$fields[ix].options_array}
<option value="{$fields[ix].options_array[jx]|escape}" {if $fields[ix].value eq $fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
{/section}
</select>
{/if}
{if $fields[ix].type eq 'c'}
<select name="{$fields[ix].name|escape}">
<option value="" {if $fields[ix].value eq ''}selected="selected"{/if}>{tr}any{/tr}</option>
<option value="y" {if $fields[ix].value eq 'y'}selected="selected"{/if}>{tr}checked{/tr}</option>
<option value="n" {if $fields[ix].value eq 'n'}selected="selected"{/if}>{tr}unchecked{/tr}</option>
</select>
{/if}
{if $fields[ix].type ne 'i'}
</td>
</tr>
{/if}
{/if}
{/section}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="filter" value="{tr}filter{/tr}" /></td></tr>
</table>
</form>


<br/>

<table class="normal">
<tr>
{if $tracker_info.showStatus eq 'y'}
<td class="heading">&nbsp;</td>
{/if}
{section name=ix loop=$fields}
{if $fields[ix].isTblVisible eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?status={$status}&amp;find={$find}&amp;trackerId={$trackerId}&amp;offset={$offset}{section name=x loop=$fields}{if $fields[x].value}&amp;{$fields[x].name}={$fields[x].value}{/if}{/section}&amp;sort_mode={if $sort_mode eq $fields[x].name|escape:'url'|cat:'_desc'}{$fields[x].name|escape:"url"}_asc{else}{$fields[x].name|escape:"url"}_desc{/if}">{$fields[ix].name}</a></td>
{/if}
{/section}
{if $tracker_info.showCreated eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?status={$status}&amp;find={$find}&amp;trackerId={$trackerId}&amp;offset={$offset}{section name=ix loop=$fields}{if $fields[ix].value}&amp;{$fields[ix].name}={$fields[ix].value}{/if}{/section}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?status={$status}&amp;find={$find}&amp;trackerId={$trackerId}&amp;offset={$offset}{section name=ix loop=$fields}{if $fields[ix].value}&amp;{$fields[ix].name}={$fields[ix].value}{/if}{/section}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}lastModif{/tr}</a></td>
{/if}
{if $tracker_info.useComments eq 'y'}
<td class="heading">{tr}coms{/tr}</td>
{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
{if $tracker_info.showStatus eq 'y'}
<td style="text-align:center;"  class="{cycle advance=false}">
{if $items[user].status eq 'o'}
<img src='img/icons/ofo.gif' border='0' alt='{tr}open{/tr}' title='{tr}open{/tr}' />
{else}
<img src='img/icons/fo.gif' border='0' alt='{tr}closed{/tr}' title='{tr}closed{/tr}' />
{/if}
</td>
{/if}
{section name=ix loop=$items[user].field_values}
{if $items[user].field_values[ix].isTblVisible eq 'y'}
{if $items[user].field_values[ix].isMain eq 'y'}
<td class="{cycle advance=false}">{if $tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y'}<a class="tablename" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{section name=mix loop=$fields}{if $fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}&amp;itemId={$items[user].itemId}">{/if}
{if $items[user].field_values[ix].type eq 'f'}
{$items[user].field_values[ix].value|tiki_short_datetime}
{elseif $items[user].field_values[ix].type eq 'i'}
<img src="{$items[user].field_values[ix].value}"/>
{else}
{$items[user].field_values[ix].value}
{/if}
{if $tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y'}</a>{/if}
{if $tiki_p_admin_trackers eq 'y'}
 [<a class="link" href="tiki-view_tracker.php?status={$status}&amp;trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{section name=mix loop=$fields}{if $fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}&amp;remove={$items[user].itemId}">x</a>]
{/if}
</td>
{else}
<td class="{cycle advance=false}">
{if $items[user].field_values[ix].type eq 'f'}
{$items[user].field_values[ix].value|tiki_short_datetime}
{else}
{$items[user].field_values[ix].value}
{/if}
</td>
{/if}
{/if}
{/section}
{if $tracker_info.showCreated eq 'y'}
<td class="{cycle advance=false}">{$items[user].created|tiki_short_datetime}</td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td class="{cycle advance=false}">{$items[user].lastModif|tiki_short_datetime}</td>
{/if}
{if $tracker_info.useComments eq 'y'}
<td  style="text-align:right;" class="{cycle advance=false}">{$items[user].comments}</td>
{/if}
</tr>
{cycle print=false}
{/section}
</table>
<br/>
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-view_tracker.php?status={$status}&amp;find={$find}&amp;trackerId={$trackerId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}{section name=ix loop=$fields}{if $fields[ix].value}&amp;{$fields[ix].name}={$fields[ix].value}{/if}{/section}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-view_tracker.php?status={$status}&amp;find={$find}&amp;trackerId={$trackerId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}{section name=ix loop=$fields}{if $fields[ix].value}&amp;{$fields[ix].name}={$fields[ix].value}{/if}{/section}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-view_tracker.php?status={$status}&amp;find={$find}&amp;trackerId={$trackerId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}{section name=ix loop=$fields}{if $fields[ix].value}&amp;{$fields[ix].name}={$fields[ix].value}{/if}{/section}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
