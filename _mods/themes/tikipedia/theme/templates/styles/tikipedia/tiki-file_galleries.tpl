{* $Header: /cvsroot/tikiwiki/_mods/themes/tikipedia/theme/templates/styles/tikipedia/tiki-file_galleries.tpl,v 1.1 2006-05-02 05:54:38 chibaguy Exp $ *}
<div class="tabt2"> {* div added and buttons (span class added) moved up *}

</div>
<h1><a class="pagetitle" href="tiki-file_galleries.php?galleryId={$galleryId}">{tr}File Galleries{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}File+Galleries" target="tikihelp" class="tikihelp" title="{tr}File Galleries{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-file_galleries.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}File Galleries tpl{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit template{/tr}' /></a>
{/if}</h1>

{if $tiki_p_admin eq 'y'}
<a href="tiki-admin.php?page=fgal"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
<br /><br />
{/if}

{if $tiki_p_create_file_galleries eq 'y'}
{if $edit_mode eq 'y'}
{if $galleryId eq 0}
<h3>{tr}Create a file gallery{/tr}</h3>
{else}
<h3>{tr}Edit this file gallery:{/tr} {$name}</h3>
<a class="linkbut" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0">{tr}create new gallery{/tr}</a>
{/if}
{if $individual eq 'y'}
<a class="fgallink" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleryId}">{tr}There are individual permissions set for this file gallery{/tr}</a>
{/if}
<div  align="center">
<form action="tiki-file_galleries.php" method="post">
<input type="hidden" name="galleryId" value="{$galleryId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea rows="5" cols="40" name="description">{$description|escape}</textarea></td></tr>
<!--<tr><td>{tr}Theme{/tr}:</td><td><select name="theme">
       <option value="default" {if $theme eq 'default'}selected="selected"{/if}>default</option>
       <option value="dark" {if $theme eq 'dark'}selected="selected"{/if}>dark</option>
       </select></td></tr>-->
<tr><td class="formcolor">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="formcolor"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>       
<tr>
	<td class="formcolor">{tr}Listing configuration{/tr}</td>
	<td class="formcolor">
		<table >
			<tr>
				<td class="formcolor">{tr}icon{/tr}</td>
				<td class="formcolor">{tr}id{/tr}</td>
				<td class="formcolor">{tr}name{/tr}</td>
				<td class="formcolor">{tr}size{/tr}</td>
				<td class="formcolor">{tr}description{/tr}</td>
				<td class="formcolor">{tr}created{/tr}</td>
				<td class="formcolor">{tr}downloads{/tr}</td>
			</tr>
			<tr>
				<td class="formcolor"><input type="checkbox" name="show_icon" {if $show_icon eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_id" {if $show_id eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor">
					<select name="show_name">
						<option value="a" {if $show_name eq 'a'}selected="selected"{/if}>{tr}Name-filename{/tr}</option>
						<option value="n" {if $show_name eq 'n'}selected="selected"{/if}>{tr}Name{/tr}</option>
						<option value="f" {if $show_name eq 'f'}selected="selected"{/if}>{tr}Filename only{/tr}</option>
					</select>
				</td>
				<td class="formcolor"><input type="checkbox" name="show_size" {if $show_size eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_description" {if $show_description eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_created" {if $show_created eq 'y'} checked="checked"{/if} /></td>
				<td class="formcolor"><input type="checkbox" name="show_dl" {if $show_dl eq 'y'} checked="checked"{/if} /></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="formcolor">{tr}Max description display size{/tr}</td>
	<td class="formcolor"><input type="text" name="max_desc" value="{$max_desc|escape}" /></td>
</tr>
<tr><td class="formcolor">{tr}Max Rows per page{/tr}:</td><td class="formcolor"><input type="text" name="maxRows" value="{$maxRows|escape}" /></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Other users can upload files to this gallery{/tr}:</td><td class="formcolor"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" value="{tr}save{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br />
{/if}
{/if}
{if $galleryId>0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the file gallery using the following URL{/tr}: <a class="fgallink" href="{$url}?galleryId={$galleryId}">{$url}?galleryId={$galleryId}</a>
</div>
{/if}
{/if}

<h2>{tr}Available File Galleries{/tr}</h2>
{if $tiki_p_create_file_galleries eq 'y'}
<a class="linkbut" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0">{tr}create new gallery{/tr}</a><br /><br />
{/if}
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-file_galleries.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
{assign var='cntcol' value=1}
{if $fgal_list_name eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
{/if}
{if $fgal_list_description eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
{/if}
{if $fgal_list_created eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $fgal_list_lastmodif eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last modified{/tr}</a></td>
{/if}
{if $fgal_list_user eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td class="heading"><a class="tableheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
{/if}
{if $fgal_list_files eq 'y'}	
	{assign var='cntcol' value=$cntcol+1}
	<td style="text-align:right;" class="heading">{tr}Files{/tr}</td>
{/if}
{if $fgal_list_hits eq 'y'}
	{assign var='cntcol' value=$cntcol+1}
	<td style="text-align:right;"  class="heading"><a class="tableheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
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
				<a class="fgalname" href="tiki-list_file_gallery.php?galleryId={$galleries[changes].id}">
			{/if}
			{$galleries[changes].name}
			{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_view_file_gallery eq 'y' ) }
			</a>
			{/if}
		</td>
	{/if}
	
	{if $fgal_list_description eq 'y'}
		<td class="{cycle advance=false}">
			{$galleries[changes].description}
		</td>
	{/if}

	{if $fgal_list_created eq 'y'}	
		<td class="{cycle advance=false}">{$galleries[changes].created|tiki_short_datetime}&nbsp;</td>
	{/if}

	{if $fgal_list_lastmodif eq 'y'}
		<td class="{cycle advance=false}">{$galleries[changes].lastModif|tiki_short_datetime}&nbsp;</td>
	{/if}
	
	{if $fgal_list_user eq 'y'}
		<td class="{cycle advance=false}">{$galleries[changes].user}&nbsp;</td>
	{/if}
	
	{if $fgal_list_files eq 'y'}
		<td style="text-align:right;"  class="{cycle advance=false}">{$galleries[changes].files}&nbsp;</td>
	{/if}
	
	{if $fgal_list_hits eq 'y'}
		<td style="text-align:right;"  class="{cycle advance=false}">{$galleries[changes].hits}&nbsp;</td>
	{/if}
	
	
	<td class="{cycle}" nowrap="nowrap">
	{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
		{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
			<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}"><img src="img/icons/config.gif" border="0" width="16" height="16" alt='{tr}edit{/tr}' title='{tr}edit{/tr}'></a>
		{/if}
	{/if}

	{if $tiki_p_upload_files eq 'y'}
		{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_upload_files eq 'y' ) }
			{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
				<a class="fgallink" href="tiki-upload_file.php?galleryId={$galleries[changes].id}"><img src='img/icons2/upload.gif' border='0' alt='{tr}Upload{/tr}' title='{tr}Upload{/tr}' /></a>
			{/if}
		{/if}
	{/if}

	{if $tiki_p_admin eq 'y'}
	    {if $galleries[changes].individual eq 'y'}
		<a class="fgallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleries[changes].id}"><img src='img/icons/key_active.gif' alt='{tr}active perms{/tr}' title='{tr}active perms{/tr}' border='0' /></a>
	    {else}
		<a class="fgallink" href="tiki-objectpermissions.php?objectName={$galleries[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$galleries[changes].id}"><img src='img/icons/key.gif' alt='{tr}perms{/tr}' title='{tr}perms{/tr}' border='0' /></a>
	    {/if}
	{/if}
{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
                {if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
                &nbsp;&nbsp; <a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}"><img src='img/icons2/delete.gif' border='0' alt='{tr}delete{/tr}' title='{tr}delete{/tr}' /></a>
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
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="fgalprevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="fgalprevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>

