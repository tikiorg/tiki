{* $Header: /cvsroot/tikiwiki/tiki/templates/file_galleries.tpl,v 1.5 2006-12-12 19:30:34 sylvieg Exp $ *}
{if !isset($show_find) or $show_find ne 'n'}
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="{$smarty.server.PHP_SELF}">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	 {if isset($galleryId)}<input type="hidden" name="galleryId" value="{$galleryId}" />{/if}
   </form>
   </td>
</tr>
</table>
</div>
{/if}

<table class="normal">
<tr>
{assign var='cntcol' value=1}
{if $fgal_list_name eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
{/if}
{if $fgal_list_parent eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'name_desc'}parent_asc{else}parent_desc{/if}">{tr}Parent{/tr}</a></td>
{/if}
{if $fgal_list_description eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
{/if}
{if $fgal_list_type eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'name_type'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
{/if}
{if $fgal_list_created eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $fgal_list_lastmodif eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last modified{/tr}</a></td>
{/if}
{if $fgal_list_user eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
{/if}
{if $fgal_list_files eq 'y'}	
	{assign var='cntcol' value=$cntcol+1}
	<td style="text-align:right;" class="heading">{tr}Files{/tr}</td>
{/if}
{if $fgal_list_hits eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td style="text-align:right;"  class="heading"><a class="tableheading" href="{$smarty.server.PHP_SELF}?offset={$offset}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}{if $find}find={$find}{/if}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
{/if}
<td  class="heading">{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$galleries}
{if $galleries[changes].visible eq 'y' or $tiki_p_admin_file_galleries eq 'y'}
<tr>
	{if $fgal_list_name eq 'y'}
		<td class="{cycle advance=false}">
			{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_file_gallery eq 'y' ) }
				<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}" title="{tr}list{/tr}">
			{/if}
			{$galleries[changes].name|escape}
			{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_file_gallery eq 'y' ) }
			</a>
			{/if}
		</td>
	{/if}

	{if $fgal_list_parent eq 'y'}
		<td class="{cycle advance=false}">
			<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].parentId}" title="{tr}list{/tr}">{$galleries[changes].parentName|escape}</a>
		</td>
	{/if}

	{if $fgal_list_description eq 'y'}
		<td class="{cycle advance=false}">
			{$galleries[changes].description|escape}
		</td>
	{/if}

	{if $fgal_list_type eq 'y'}
		<td class="{cycle advance=false}">
			{if $galleries[changes].type eq "default" }<img src='pics/large/file-manager48x48.png' border='0' alt='{tr}file gallery{/tr}' title='{tr}file gallery{/tr}' />
			{elseif $galleries[changes].type eq "podcast" }<img src='pics/large/gnome-sound-recorder48x48.png' border='0' alt='{tr}podcast (audio){/tr}' title='{tr}podcast (audio){/tr}' />
			{elseif $galleries[changes].type eq "vidcast" }<img src='pics/large/mplayer48x48.png' border='0' alt='{tr}podcast (video){/tr}' title='{tr}podcast (video){/tr}' />{/if}
		</td>
	{/if}

	{if $fgal_list_created eq 'y'}	
		<td class="{cycle advance=false}">{$galleries[changes].created|tiki_short_datetime}&nbsp;</td>
	{/if}

	{if $fgal_list_lastmodif eq 'y'}
		<td class="{cycle advance=false}">{$galleries[changes].lastModif|tiki_short_datetime}&nbsp;</td>
	{/if}
	
	{if $fgal_list_user eq 'y'}
		<td class="{cycle advance=false}">{$galleries[changes].user|escape}</td>
	{/if}
	
	{if $fgal_list_files eq 'y'}
		<td style="text-align:right;"  class="{cycle advance=false}">{$galleries[changes].files}&nbsp;</td>
	{/if}
	
	{if $fgal_list_hits eq 'y'}
		<td style="text-align:right;"  class="{cycle advance=false}">{$galleries[changes].hits}&nbsp;</td>
	{/if}
	
	
	<td class="{cycle}" nowrap="nowrap">
	{if $tiki_p_view_file_gallery == 'y' or $tiki_p_admin_file_galleries eq 'y' or $tiki_p_admin eq 'y'}
		<a class="gallink" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].galleryId}"><img border='0' height="16" width="18" src='pics/icons/table.png' title='{tr}list{/tr}' alt='{tr}list{/tr}' /></a>
	{/if}
	{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
		{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
			<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}"><img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
		{/if}
	{/if}

	{if $tiki_p_upload_files eq 'y'}
		{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_files eq 'y' ) }
			{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
				<a class="fgallink" href="tiki-upload_file.php?galleryId={$galleries[changes].id}"><img src='pics/icons/upload.png' border='0' width='16' height='16' alt='{tr}upload{/tr}' title='{tr}upload{/tr}' /></a>
			{/if}
		{/if}
	{/if}

	{if $tiki_p_admin eq 'y'|| $galleries[changes].individual eq 'n' || $galleries[changes].individual_tiki_p_assign_perm_file_gallery eq 'y'}
	    {if $galleries[changes].individual eq 'y'}
		<a class="fgallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleries[changes].id}"><img border='0' width='16' height='16' src='pics/icons/key_active.png' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' /></a>
	    {else}
		<a class="fgallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleries[changes].id}"><img src='pics/icons/key.png' border='0' width='16' height='16' alt='{tr}perms{/tr}' title='{tr}perms{/tr}' /></a>
	    {/if}
	{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
                {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
                &nbsp;&nbsp; <a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}"><img src='pics/icons/cross.png' border='0' width='16' height='16' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' /></a>
                {/if}
        {/if}
	</td>
</tr>
{/if}
{sectionelse}
<tr><td class="odd" colspan="{$cntcol}">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>

{if $maxRecords > 0}
	<div class="mini" align="center">
	{if $prev_offset >= 0}
		[<a class="fgalprevnext" href="{$smarty.server.PHP_SELF}?{if $find}find={$find}{/if}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
	{/if}
	{tr}Page{/tr}: {$actual_page}/{$cant_pages}
	{if $next_offset >= 0}
		&nbsp;[<a class="fgalprevnext" href="{$smarty.server.PHP_SELF}?{if $find}find={$find}{/if}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
	{/if}
	{if $direct_pagination eq 'y' and $cant_pages gt 1}
		<br />
		{section loop=$cant_pages name=foo}
		{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
		<a class="prevnext" href="{$smarty.server.PHP_SELF}?{if $find}find={$find}{/if}{if isset($galleryId)}&amp;galleryId={$galleryId}{/if}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
		{$smarty.section.foo.index_next}</a>&nbsp;
		{/section}
	{/if}
	</div>
{/if}

