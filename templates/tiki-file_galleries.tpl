<a class="pagetitle" href="tiki-file_galleries.php?galleryId={$galleryId}">{tr}File Galleries{/tr}</a><br/><br/>
{if $tiki_p_create_file_galleries eq 'y'}
{if $edit_mode eq 'y'}
<h3>{tr}Create or edit a file gallery using this form{/tr}</h3>
{if $individual eq 'y'}
<a class="fgallink" href="tiki-objectpermissions.php?objectName=file%20gallery%20{$name}&amp;objectType=file%20gallery&amp;permType=file%20galleries&amp;objectId={$galleryId}">{tr}There are individual permissions set for this file gallery{/tr}</a>
{/if}
<div  align="center">
<form action="tiki-file_galleries.php" method="post">
<input type="hidden" name="galleryId" value="{$galleryId}" />
<table class="editfgalform">
<tr><td class="editfgalform">{tr}Name{/tr}:</td><td class="editfgalform"><input type="text" name="name" value="{$name}"/></td></tr>
<tr><td class="editfgalform">{tr}Description{/tr}:</td><td class="editfgalform"><textarea rows="5" cols="40" name="description">{$description}</textarea></td></tr>
<!--<tr><td>{tr}Theme{/tr}:</td><td><select name="theme">
       <option value="default" {if $theme eq 'default'}selected="selected"{/if}>default</option>
       <option value="dark" {if $theme eq 'dark'}selected="selected"{/if}>dark</option>
       </select></td></tr>-->
{if $tiki_p_admin_file_galleries eq 'y'}       
<tr><td class="editfgalform">{tr}Gallery is visible to non-admin users?{/tr}</td><td class="editfgalform"><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if} /></td></tr>       
{/if}
<tr>
	<td class="editfgalform">{tr}Listing configuration{/tr}</td>
	<td class="editfgalform">
		<table width="100%">
			<tr>
				<td class="editfgalform">{tr}icon{/tr}</td>
				<td class="editfgalform">{tr}id{/tr}</td>
				<td class="editfgalform">{tr}name{/tr}</td>
				<td class="editfgalform">{tr}size{/tr}</td>
				<td class="editfgalform">{tr}description{/tr}</td>
				<td class="editfgalform">{tr}created{/tr}</td>
				<td class="editfgalform">{tr}downloads{/tr}</td>
			</tr>
			<tr>
				<td class="editfgalform"><input type="checkbox" name="show_icon" {if $show_icon eq 'y'} checked="checked"{/if} /></td>
				<td class="editfgalform"><input type="checkbox" name="show_id" {if $show_id eq 'y'} checked="checked"{/if} /></td>
				<td class="editfgalform">
					<select name="show_name">
						<option value="a" {if $show_name eq 'n'}selected="selected"{/if}>{tr}Name-filename{/tr}</option>
						<option value="n" {if $show_name eq 'f'}selected="selected"{/if}>{tr}Name{/tr}</option>
						<option value="f" {if $show_name eq 'f'}selected="selected"{/if}>{tr}Filename only{/tr}</option>
					</select>
				</td>
				<td class="editfgalform"><input type="checkbox" name="show_size" {if $show_size eq 'y'} checked="checked"{/if} /></td>
				<td class="editfgalform"><input type="checkbox" name="show_description" {if $show_description eq 'y'} checked="checked"{/if} /></td>
				<td class="editfgalform"><input type="checkbox" name="show_created" {if $show_created eq 'y'} checked="checked"{/if} />{/tr}</td>
				<td class="editfgalform"><input type="checkbox" name="show_dl" {if $show_dl eq 'y'} checked="checked"{/if} /></td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td class="editfgalform">{tr}Max description display size{/tr}</td>
	<td class="editfgalform"><input type="text" name="max_desc" value="{$max_desc}" /></td>
</tr>
<tr><td class="editfgalform">{tr}Max Rows per page{/tr}:</td><td class="editfgalform"><input type="text" name="maxRows" value="{$maxRows}" /></td></tr>
{include file=categorize.tpl}
<tr><td class="editfgalform">{tr}Other users can upload files to this gallery{/tr}:</td><td class="editfgalform"><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="editfgalform">&nbsp;</td><td class="editfgalform"><input type="submit" value="{tr}edit/create{/tr}" name="edit" /></td></tr>
</table>
</form>
</div>
<br/>
{/if}
{/if}
{if $galleryId>0}
{if $edited eq 'y'}
<div class="wikitext">
{tr}You can access the file gallery using the following URL{/tr}: <a class="fgallink" href="{$url}?galleryId={$galleryIdl}">{$url}?galleryId={$galleryId}</a>
</div>
{/if}
{/if}

<h2>{tr}Available File Galleries{/tr}</h2>
<a class="fgallink" href="tiki-file_galleries.php?edit_mode=1&amp;galleryId=0">{tr}create new gallery{/tr}</a><br/><br/>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-file_galleries.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
   </form>
   </td>
</tr>
</table>
<table class="listfgal">
<tr>
{if $fgal_list_name eq 'y'}
	<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
{/if}
{if $fgal_list_description eq 'y'}
	<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a></td>
{/if}
{if $fgal_list_created eq 'y'}
	<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $fgal_list_lastmodif eq 'y'}
	<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last modified{/tr}</a></td>
{/if}
{if $fgal_list_user eq 'y'}
	<td class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></td>
{/if}
{if $fgal_list_files eq 'y'}	
	<td style="text-align:right;" class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'files_desc'}files_asc{else}files_desc{/if}">{tr}Files{/tr}</a></td>
{/if}
{if $fgal_list_hits eq 'y'}
	<td style="text-align:right;"  class="listfgalheading"><a class="llisfgalheading" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></td>
{/if}
<td width="15%" class="listfgalheading">{tr}Actions{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$galleries}
{if $galleries[changes].visible eq 'y' or $tiki_p_admin_file_galleries eq 'y'}
<tr>
	{if $fgal_list_name eq 'y'}
		<td class="listfgalname{cycle advance=false}">
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
		<td class="listfgaldescription{cycle advance=false}">
			{$galleries[changes].description}
		</td>
	{/if}

	{if $fgal_list_created eq 'y'}	
		<td class="listfgalcreated{cycle advance=false}">{$galleries[changes].created|tiki_short_datetime}&nbsp;</td>
	{/if}

	{if $fgal_list_lastmodif eq 'y'}
		<td class="listfgallastModif{cycle advance=false}">{$galleries[changes].lastModif|tiki_short_datetime}&nbsp;</td>
	{/if}
	
	{if $fgal_list_user eq 'y'}
		<td class="listfgaluser{cycle advance=false}">{$galleries[changes].user}&nbsp;</td>
	{/if}
	
	{if $fgal_list_files eq 'y'}
		<td style="text-align:right;"  class="listfgalfiles{cycle advance=false}">{$galleries[changes].files}&nbsp;</td>
	{/if}
	
	{if $fgal_list_hits eq 'y'}
		<td style="text-align:right;"  class="listfgalvisits{cycle advance=false}">{$galleries[changes].hits}&nbsp;</td>
	{/if}
	
	
	<td class="listfgalactions{cycle}">
	{if $tiki_p_admin_file_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
		{if ($tiki_p_admin eq 'y') or ($galleries[changes].individual eq 'n') or ($galleries[changes].individual_tiki_p_create_file_galleries eq 'y' ) }
			<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].id}"><img src='img/icons/config.gif' border='0' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' /></a>
			<a class="fgallink" href="tiki-file_galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].id}"><img src='img/icons2/delete.gif' border='0' alt='delete' title='delete' /></a>
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
	    {if $galleries[changes].individual eq 'y'}({/if}<a class="fgallink" href="tiki-objectpermissions.php?objectName=file%20gallery%20{$galleries[changes].name}&amp;objectType=file%20gallery&amp;permType=file%20galleries&amp;objectId={$galleries[changes].id}"><img src='img/icons/key.gif' alt='{tr}perms{/tr}' title='{tr}perms{/tr}' border='0' /></a>{if $galleries[changes].individual eq 'y'}){/if}
	{/if}
	
	</td>
</tr>
{/if}

{sectionelse}
<tr><td colspan="6">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
</table>
<br/>
<div class="mini">
{if $prev_offset >= 0}
[<a class="fgalprevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="fgalprevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-file_galleries.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{if $tiki_p_admin eq 'y'}
<br/><br/>
<a href="tiki-admin.php?page=fgal"><img src='img/icons/config.gif' border='0'  alt="{tr}configure listing{/tr}" title="{tr}configure listing{/tr}" /></a>
{/if}

