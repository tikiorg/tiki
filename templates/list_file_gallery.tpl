{strip}
{* $Header: /cvsroot/tikiwiki/tiki/templates/list_file_gallery.tpl,v 1.31.2.7 2008-02-15 13:52:25 nyloth Exp $ *}
{* param:$gal_info, $files, $show_find *}

{if !isset($show_find) or $show_find ne 'n'}
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="{$smarty.server.PHP_SELF}">
     {if $filegals_manager eq 'y'}
     <input type="hidden" name="filegals_manager" value="{$filegals_manager}" />
     {/if}
     <input type="hidden" name="galleryId" value="{$gal_info.galleryId|escape}" />
	 {if isset($file_info)}<input type="hidden" name="fileId" value="{$file_info.fileId}" />{/if}
     <input type="text" name="{$ext}find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     {if !empty($sort_mode)}<input type="hidden" name="{$ext}sort_mode" value="{$sort_mode|escape}" />{/if}
	 {if isset($page)}<input type="hidden" name="page" value="{$page|escape}" />{/if}
   </form>
   </td>
</tr>
</table>
{/if}

<form  name="checkboxes_on" method="post" action="{$smarty.server.PHP_SELF}{if $filegals_manager eq 'y'}?filegals_manager{/if}">
	<input type="hidden" name="galleryId" value="{$gal_info.galleryId|escape}" />
    <input type="hidden" name="{$ext}find" value="{$find|escape}" />
    {if !empty($sort_mode)}<input type="hidden" name="{$ext}sort_mode" value="{$sort_mode|escape}" />{/if}
	{if isset($file_info)}<input type="hidden" name="fileId" value="{$file_info.fileId|escape}" />{/if}
	{if isset($page)}<input type="hidden" name="page" value="{$page|escape}" />{/if}

{assign var=nbCols value=`2`}

<table class="normal">
<tr>
{if $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y'}
<td  class="heading">&nbsp;</td>
{/if}
{if $gal_info.show_id eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="fileId"}{tr}ID{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.name eq ''}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="fileId"}{tr}Gallery{/tr}{/self_link}</td>
	{assign var=nb value=`$nb+1`}
{/if}
{if $gal_info.show_icon eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="filetype"}{tr}Type{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_name eq 'a' || $gal_info.show_name eq 'n'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="name"}{tr}Name{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_name eq 'a' || $gal_info.show_name eq 'f'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="filename"}{tr}Filename{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_size eq 'y'}
	<td style="text-align:right;" class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="filesize"}{tr}Filesize{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_description eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="description"}{tr}Description{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_created eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="created"}{tr}Uploaded{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_creator eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="user"}{tr}Creator{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_author eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="author"}{tr}Author{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if isset($gal_info.show_modified) and $gal_info.show_modified eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="lastModif"}{tr}Modified{/tr}{/self_link}</td>
{/if}
{if isset($gal_info.show_last_user) and $gal_info.show_last_user eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="lastModifUser"}{tr}Last editor{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if isset($gal_info.show_comment) and $gal_info.show_comment eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="comment"}{tr}Comment{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_dl eq 'y'}
	<td style="text-align:right;" class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="downloads"}{tr}Dls{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if $gal_info.show_lockedby eq 'y' and $gal_info.lockable eq 'y'}
	<td class="heading">{self_link _class="tableheading" _sort_arg="`$ext`sort_mode" _sort_field="lockedby"}{tr}Locked by{/tr}{/self_link}</td>
	{assign var=nbCols value=`$nbCols+1`}
{/if}
{if empty($show_action) or $show_action eq 'y'}
	<td class="heading">{tr}Actions{/tr}</td>
{else}
	<td class="heading">{tr}Actions{/tr}</td>
{/if}
</tr>


{cycle values="odd,even" print=false}
{section name=changes loop=$files}
<tr>

{if $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y'}
<td  style="text-align:center;" class="{cycle advance=false}">
	<input type="checkbox" name="file[]" value="{$files[changes].fileId|escape}"  {if $smarty.request.file and in_array($files[changes].fileId,$smarty.request.file)}checked="checked"{/if} />
</td>
{/if}

{if $gal_info.show_id eq 'y'}
	<td class="{cycle advance=false}">{$files[changes].fileId}</td>
{/if}

{if $gal_info.name eq '' or $gal_info.show_gallery eq 'y'}
	<td class="{cycle advance=false}"><a href="tiki-list_file_gallery.php?galleryId={$files[changes].galleryId}{if $filegals_manager eq 'y'}&filegals_manager{/if}" title="{tr}List{/tr}">{tr}{$files[changes].gallery}{/tr}</a></td>
{/if}

{if $gal_info.show_icon eq 'y'}
	<td style="text-align:center;" class="{cycle advance=false}">
		{$files[changes].filename|iconify}
	</td>
{/if}
	

{if $gal_info.show_name eq 'a' || $gal_info.show_name eq 'n'}
	<td class="{cycle advance=false}">
		{if $tiki_p_download_files eq 'y'}
			{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
				<a class="fgalname" href="{$download_path}{$files[changes].path}">
			{else}
				{if $filegals_manager eq 'y'}
				<a class="fgalname" href="javascript:window.opener.SetUrl('{$url_path}tiki-download_file.php?fileId={$files[changes].fileId}&display');javascript:window.close() ;">
				{else}
				<a class="fgalname" href="tiki-download_file.php?fileId={$files[changes].fileId}">
				{/if}
			{/if}
		{/if}
		{$files[changes].name|escape}
		{if $tiki_p_download_files eq 'y'}</a>{/if}
	</td>
{/if}
{if $gal_info.show_name eq 'a' || $gal_info.show_name eq 'f'}
	<td class="{cycle advance=false}">
		{if $tiki_p_download_files eq 'y'}
			{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
				<a class="fgalname" href="{$download_path}{$files[changes].path}">
			{else}
				{if $filegals_manager eq 'y'}
				<a class="fgalname" href="javascript:window.opener.SetUrl('{$url_path}tiki-download_file.php?fileId={$files[changes].fileId}&display');javascript:window.close() ;">
				{else}
				<a class="fgalname" href="tiki-download_file.php?fileId={$files[changes].fileId}">
				{/if}
			{/if}
		{/if}
		{$files[changes].filename|escape}
		{if $tiki_p_download_files eq 'y'}</a>{/if}
	</td>
{/if}

{if $gal_info.show_size eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$files[changes].filesize|kbsize}</td>
{/if}

{if $gal_info.show_description eq 'y'}
	<td class="{cycle advance=false}">{$files[changes].description|truncate:$gal_info.max_desc:"..."|escape}</td>
{/if}
	
{if $gal_info.show_created eq 'y'}
	<td class="{cycle advance=false}">{$files[changes].created|tiki_short_date}</td>
{/if}
{if $gal_info.show_creator eq 'y'}
	<td class="{cycle advance=false}">
		{if $gal_info.show_userlink eq 'n'}
			{$files[changes].user|username}
		{else}
			{$files[changes].user|userlink}
		{/if}
	</td>
{/if}
{if $gal_info.show_author eq 'y'}
	<td class="{cycle advance=false}">
		{if $files[changes].author}
			{if $gal_info.show_userlink eq 'n'}
				{$files[changes].author|username}
			{else}
				{$files[changes].author|userlink}
			{/if}
		{elseif $gal_info.show_creator ne 'y'}
			{if $gal_info.show_userlink eq 'n'}
				{$files[changes].user|username}
			{else}
				{$files[changes].user|userlink}
			{/if}
		{/if}
	</td>
{/if}

{if $gal_info.show_modified eq 'y'}
	<td class="{cycle advance=false}">
		{if $gal_info.show_created ne 'y' or $files[changes].created ne $files[changes].lastModif}
			{$files[changes].lastModif|tiki_short_date}
		{/if}
	</td>
{/if}

{if $gal_info.show_last_user eq 'y'}
	<td class="{cycle advance=false}">
		{if $gal_info.show_created ne 'y' or $files[changes].created ne $files[changes].lastModif}
			{if $gal_info.show_userlink eq 'n'}
				{$files[changes].lastModifUser|username}
			{else}
				{$files[changes].lastModifUser|userlink}
			{/if}
		{/if}
	</td>
{/if}

{if isset($gal_info.show_comment) and $gal_info.show_comment eq 'y'}
	<td class="{cycle advance=false}">{$files[changes].comment|escape}</td>
{/if}

{if $gal_info.show_dl eq 'y'}
	<td style="text-align:right;" class="{cycle advance=false}">{$files[changes].downloads}</td>
{/if}

{if $gal_info.show_lockedby eq 'y' and $gal_info.lockable eq 'y'}
	<td class="{cycle advance=false}">{$files[changes].lockedby|escape}</td>
{/if}

<td class="{cycle}">
	{if (isset($files[changes].p_download_files) and  $files[changes].p_download_files eq 'y')
	 or (!isset($files[changes].p_download_files) and $tiki_p_download_files eq 'y')}
		{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
			<a class="fgalname" href="{$download_path}{$files[changes].path}" title="{tr}Download{/tr}">
		{else}
			<a class="fgalname" href="tiki-download_file.php?fileId={$files[changes].fileId}" title="{tr}Download{/tr}">
		{/if}
		{icon _id='disk' alt="{tr}Download{/tr}"}</a> 
	{/if}
	{if empty($show_action) or $show_action eq 'y'}
	{if $files[changes].nbArchives gt 0}
		<a href="tiki-file_archives.php?fileId={$files[changes].fileId}" title="{tr}Archives{/tr}({$files[changes].nbArchives})">{icon _id='disk_multiple' alt="{tr}Archives{/tr}"}</a>
	{elseif $gal_info.archives gt -1}
		{icon _id='disk_multiple_gray' alt="{tr}Archives{/tr}" class=""}
	{/if}
	{* can edit if I am admin or the owner of the file or the locker of the file or if I have the perm to edit file on this gall *}
	{if $tiki_p_admin_file_galleries eq 'y'
		or ($files[changes].lockedby and $files[changes].lockedby eq $user)
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or $tiki_p_edit_file_gallery eq 'y')) }
		{if $files[changes].archiveId == 0}
			<a class="link" href="tiki-upload_file.php?galleryId={$gal_info.galleryId}&amp;fileId={$files[changes].fileId}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='page_edit' alt="{tr}Properties{/tr}"}</a>

			{if $gal_info.lockable eq 'y'}
				{if $files[changes].lockedby}
					{self_link _class="link" _icon="lock_delete" lock="n" fileId=$files[changes].fileId}{tr}Unlock{/tr}{/self_link}
				{else}
					{self_link _class="link" _icon="lock_add" lock="y" fileId=$files[changes].fileId}{tr}Lock{/tr}{/self_link}
				{/if}
			{/if}
		{/if}
	{/if}
	{if $tiki_p_admin_file_galleries eq 'y'
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or $tiki_p_edit_file_gallery eq 'y')) }
			{self_link _class="link" _icon="cross" remove=$files[changes].fileId}{tr}Delete{/tr}{/self_link}
	{/if}
	{/if}
</td>
</tr>
{sectionelse}
<tr><td colspan="{$nbCols}">
<b>{tr}No records found{/tr}</b>
</td></tr>
{/section}
{if $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y'}
	<script type="text/javascript"> /* <![CDATA[ */
	document.write("<tr><td colspan=\"{$nbCols}\"><input name=\"switcher\" id=\"clickall\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'file[]',this.checked)\"/>");
	document.write("<label for=\"clickall\">{tr}Select All{/tr}</label></td></tr>");
	/* ]]> */</script>
{/if}
</table>

{if $files and $gal_info.show_checked ne 'n' and $tiki_p_admin_file_galleries eq 'y'}
<div>
<div style="float:left">
{tr}Perform action with checked:{/tr} 
{if !isset($file_info)}
	{if $file_offset}<input type="hidden" name="file_offset" value="{$file_offset}" />{/if}
	<input style="vertical-align: middle;" type="image" name="movesel" src="pics/icons/arrow_right.png" alt='{tr}Move{/tr}' title='{tr}Move Selected Files{/tr}' />
{/if}
<input  style="vertical-align: middle;" type="image" name="delsel" src='pics/icons/cross.png' alt='{tr}Delete{/tr}' title='{tr}Delete{/tr}' onclick="return confirm('{tr}Are you sure you want to delete the selected files?{/tr}')" />
</div>
{if $smarty.request.movesel_x and !isset($file_info)} 
<div>
	{tr}Move to{/tr}:
	<select name="moveto">
		{section name=ix loop=$all_galleries}
			{if $all_galleries[ix].galleryId ne $gal_info.galleryId}
				<option value="{$all_galleries[ix].galleryId|escape}">{$all_galleries[ix].name}</option>
			{/if}
		{/section}
	</select>
	<input type='submit' name='movesel' value='{tr}Move{/tr}' />
</div>
{/if}
</div>
<br style="clear:both"/>
{/if}
</form>

<br />
{pagination_links cant=$file_cant step=$maxRecords offset=$file_offset offset_arg="file_offset"}{/pagination_links}

{/strip}
