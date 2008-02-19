{* $Header: /cvsroot/tikiwiki/tiki/templates/file_galleries.tpl,v 1.26.2.9 2008-02-19 20:11:03 jyhem Exp $ *}
{if !isset($show_find) or $show_find ne 'n'}
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="{$smarty.server.PHP_SELF}">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
     {if $filegals_manager eq 'y'}
     <input type="hidden" name="filegals_manager" value="{$filegals_manager}" />
     {/if}
	 {if isset($galleryId)}<input type="hidden" name="galleryId" value="{$galleryId}" />{/if}
   </form>
   </td>
</tr>
</table>
</div>
{/if}

<form>
<table class="normal">
<tr>
{assign var='cntcol' value=1}
{if $tiki_p_admin_file_galleries eq 'y' or $tiki_p_assign_perm_file_gallery eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">&nbsp;</td>
{/if}
{if $fgal_list_id eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="galleryId"}{tr}ID{/tr}{/self_link}</td>
{/if}
{if $fgal_list_name eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="name"}{tr}Name{/tr}{/self_link}</td>
{/if}
{if $fgal_list_parent eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="parent"}{tr}Parent{/tr}{/self_link}</td>
{/if}
{if $fgal_list_description eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="description"}{tr}Description{/tr}{/self_link}</td>
{/if}
{if $fgal_list_type eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="type"}{tr}Type{/tr}{/self_link}</td>
{/if}
{if $fgal_list_created eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="created"}{tr}Created{/tr}{/self_link}</td>
{/if}
{if $fgal_list_lastmodif eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="lastModif"}{tr}Last modified{/tr}{/self_link}</td>
{/if}
{if $fgal_list_user eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="user"}{tr}User{/tr}{/self_link}</td>
{/if}
{if $fgal_list_files eq 'y'}	
	{assign var='cntcol' value=$cntcol+1}
	<td style="text-align:right;" class="heading">{tr}Files{/tr}</td>
{/if}
{if $fgal_list_hits eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading">{self_link _class="tableheading" _sort_arg="sort_mode" _sort_field="hits"}{tr}Hits{/tr}{/self_link}</td>
{/if}
<td class="heading">{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$galleries}
{if $galleries[changes].visible eq 'y' or $tiki_p_admin_file_galleries eq 'y'}
<tr>
	{if $tiki_p_admin_file_galleries eq 'y' or tiki_p_assign_perm_file_gallery eq 'y'}
		<td class="{cycle advance=false}">
			<input type="checkbox" name="checked[]" value="{$galleries[changes].id|escape}" {if $smarty.request.checked and in_array($galleries[changes].id,$smarty.request.checked)}checked="checked"{/if} />
		</td>
	{/if}
	{if $fgal_list_id eq 'y'}
		<td class="{cycle advance=false}">
			<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}" title="{tr}List{/tr}">{$galleries[changes].galleryId}</a>
		</td>
	{/if}

	{if $fgal_list_name eq 'y'}
		<td class="{cycle advance=false}">
			<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}" title="{tr}List{/tr}">{$galleries[changes].name}</a>
		</td>
	{/if}

	{if $fgal_list_parent eq 'y'}
		<td class="{cycle advance=false}">
			<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].parentId}{if $filegals_manager eq 'y'}&filegals_manager{/if}" title="{tr}List{/tr}">{$galleries[changes].parentName|escape}</a>
		</td>
	{/if}

	{if $fgal_list_description eq 'y'}
		<td class="{cycle advance=false}">
			{$galleries[changes].description|escape}
		</td>
	{/if}

	{if $fgal_list_type eq 'y'}
		<td class="{cycle advance=false}">
			{if $galleries[changes].type eq "default" }<img src='pics/large/file-manager48x48.png' border='0' alt='{tr}File Gallery{/tr}' title='{tr}File Gallery{/tr}' />
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
		<td class="{cycle advance=false}">{$galleries[changes].user|userlink}</td>
	{/if}
	
	{if $fgal_list_files eq 'y'}
		<td style="text-align:right;"  class="{cycle advance=false}">{$galleries[changes].files}&nbsp;</td>
	{/if}
	
	{if $fgal_list_hits eq 'y'}
		<td style="text-align:right;"  class="{cycle advance=false}">{$galleries[changes].hits}&nbsp;</td>
	{/if}
	
	
	<td class="{cycle}" nowrap="nowrap">
	{if $tiki_p_view_file_gallery == 'y' or $tiki_p_admin_file_galleries eq 'y' or $tiki_p_admin eq 'y'}
		<a class="gallink" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].galleryId}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='table' alt='{tr}List{/tr}'}</a>
	{/if}
	{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
		{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
                	{self_link _class="fgallink" _icon='page_edit' edit_mode=1}{/self_link}
		{/if}
	{/if}

	{if $tiki_p_upload_files eq 'y'}
		{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_files eq 'y' ) }
			{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
				<a class="fgallink" href="tiki-upload_file.php?galleryId={$galleries[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='upload'}</a>
			{/if}
		{/if}
	{/if}

	{if $tiki_p_admin eq 'y' || $galleries[changes].individual_tiki_p_assign_perm_file_gallery eq 'y' || ($galleries[changes].individual eq 'n' and $tiki_p_assign_perm_file_gallery eq 'y')}
	    {if $galleries[changes].individual eq 'y'}
		<a class="fgallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleries[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='key_active' alt='{tr}Active Perms{/tr}'}</a>
	    {else}
		<a class="fgallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleries[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='key' alt='{tr}Perms{/tr}'}</a>
	    {/if}
	{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
                {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
                &nbsp;&nbsp; {self_link _class="fgallink" _icon='cross' removegal=$galleries[changes].id}{tr}Delete{/tr}{/self_link}
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
{if $tiki_p_admin_file_galleries eq 'y' or tiki_p_assign_perm_file_gallery eq 'y'}
	<script type="text/javascript"> /* <![CDATA[ */
	document.write('<tr><td colspan="{$cntcol}"><input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'checked[]\',this.checked)"/>');
	document.write('<label for="clickall">{tr}Select All{/tr}</label></td></tr>');
	/* ]]> */</script>
{/if}
</table>
{if $tiki_p_admin_file_galleries eq 'y' or $tiki_p_assign_perm_file_gallery eq 'y'}
	<div>
	{tr}Perform action with checked:{/tr} 
	<select name="batchaction" onchange="show('groups');">
		<option value="">{tr}with checked{/tr}</option>
		{if $tiki_p_admin_file_galleries eq 'y'}<option value="delsel_x">{tr}Delete{/tr}</option>{/if}
		{if $tiki_p_assign_perm_file_gallery eq 'y'}{foreach from=$perms item=perm}<option value="assign_{$perm.permName|escape}">{tr}Assign:{/tr} {$perm.permName|escape}</option>{/foreach}{/if}
	</select>
	<span style="display:none" id="groups">
	<select name="groups[]" multiple="multiple" size="5">
	{section name=grp loop=$groups}
	<option value="{$groups[grp].groupName|escape}" {if $groupName eq $groups[grp].groupName }selected="selected"{/if}>{$groups[grp].groupName|escape}</option>
	{/section}
	</select>
	</span>
	<input type="submit" name="act" value="{tr}OK{/tr}" />
	</div>
{/if}
</form>

<br />
{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
