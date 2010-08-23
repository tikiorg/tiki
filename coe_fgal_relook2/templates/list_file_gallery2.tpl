{* $Id: list_file_gallery.tpl 26930 2010-05-05 23:16:15Z nyloth $ *}

<iframe src="about:blank" width="1" height="1" frameborder="0" style="visibility:hidden;position:absolute;" name="fgiframe"></iframe>
<form name="fgalformid" id="fgalform" method="post" action="{$smarty.server.PHP_SELF}{if $filegals_manager neq ''}?filegals_manager={$filegals_manager|escape}{/if}" onsubmit="return FileGallery.open(this.action, this.id);">
	<input type="hidden" name="galleryId" value="{$gal_info.galleryId|escape}" />
	<input type="hidden" name="find" value="{$find|escape}" />
	{if $prefs.fgal_asynchronous_indexing eq 'y'}<input type="hidden" name="fast" value="y" />{/if} 
	{if !empty($sort_mode)}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
	{if isset($file_info)}<input type="hidden" name="fileId" value="{$file_info.fileId|escape}" />{/if}
	{if isset($page)}<input type="hidden" name="page" value="{$page|escape}" />{/if}
	{if isset($view)}<input type="hidden" name="view" value="{$view|escape}" />{/if}
	{assign var=nbCols value=0}
	{assign var=other_columns value=''}
	{assign var=other_columns_selected value=''}
	

	<div class="fg-files-list">
		{if $view eq 'browse'}
			{assign var=show_infos value='y'}
			{include file='browse_file_gallery.tpl'}
		{else}
			{assign var=show_infos value='n'}
			{include file='list_file_gallery_content.tpl'}
		{/if}

		<div class="fg-pager">
			<!-- if $maxRecords > 20 and $cant>$maxRecords -->
			{if $cant>$maxRecords}
				<span style="float:left;display:block;padding-right:5px">{tr}page{/tr}</span>
				{pagination_links cant=$cant step=$maxRecords offset=$offset next='n' prev='n' template='tiki-empty.tpl' htmlelement='fg-jquery-dialog'}{/pagination_links}
			{/if}
		</div>
		
        {if $files and $gal_info.show_checked neq 'n' and ($tiki_p_admin_file_galleries eq 'y' or $tiki_p_upload_files eq 'y' or $tiki_p_assign_perm_file_gallery eq 'y')}
			{if $smarty.request.movesel_x eq '' or isset($file_info) or $perms}
			<div class="fg-actions">
				<span>{tr}Perform action with checked:{/tr}</span>
				{if !isset($file_info)}
					{if $offset}<input type="hidden" name="offset" value="{$offset}" />{/if}
					{if $tiki_p_admin_file_galleries eq 'y'}
						<a class="fg-actions-icon">{icon _id='arrow_right' _tag='input_image' name='movesel' alt='{tr}Move{/tr}' title='{tr}Move Selected Files{/tr}' style='vertical-align: middle;' onclick='document.forms.fgalformid.action+=(document.forms.fgalformid.action.indexOf("?")>0?"&":"?")+"movesel_x=1"'}</a>
					{/if}
				{/if}
				{if $tiki_p_admin_file_galleries eq 'y'}
					<a class="fg-actions-icon">{icon _id='cross' _tag='input_image' ____confirm='{tr}Are you sure you want to delete the selected files?{/tr}' name='delsel' alt='{tr}Delete{/tr}' style='vertical-align: middle;' onclick='if(!confirm("{tr}Are you sure you want to delete the selected files?{/tr}"))return false;document.forms.fgalformid.action+=(document.forms.fgalformid.action.indexOf("?")>0?"&":"?")+"delsel_x=1"'}</a>
				{/if}
				<a class="fg-actions-icon">{icon _id='pics/icons/mime/zip.png' _tag='input_image' name='zipsel' alt='{tr}Download the zip{/tr}' style='vertical-align: middle;'}</a>
				{if $tiki_p_assign_perm_file_gallery eq 'y'}
					<a class="fg-actions-icon">{icon _id='key' _tag='input_image' name='permsel' alt="{tr}Assign Permissions{/tr}" title="{tr}Assign Permissions{/tr}" style='vertical-align: middle;' onclick='document.forms.fgalformid.action+=(document.forms.fgalformid.action.indexOf("?")>0?"&":"?")+"permsel_x=1"'}</a>
				{/if}
			</div>
			{/if}
			{if $smarty.request.movesel_x and !isset($file_info)}
				<div class="fg-actions">
					{tr}Move to{/tr}:
					<select name="moveto">
					{section name=ix loop=$all_galleries}
					<option value="{$all_galleries[ix].id}">{$all_galleries[ix].label|escape}</option>
					{/section}
					</select>
					<input type='submit' name='movesel' value='{tr}Move{/tr}' />
				</div>
			{/if}
			{if $perms}
				<div class="fg-actions">
					{tr}Assign Permissions{/tr}
					<select name="perms[]" multiple="multiple" size="5"}
					{foreach from=$perms item=perm}
					<option value="{$perm.permName|escape}">{$perm.permName|escape}</option>
					{/foreach}
					</select>
					<select name="groups[]" multiple="multiple" size="5"}
					{section name=grp loop=$groups}
					<option value="{$groups[grp].groupName|escape}" {if $groupName eq $groups[grp].groupName }selected="selected"{/if}>{$groups[grp].groupName|escape}</option>
					{/section}
					</select>
					<input type="submit" name="permsel" value="{tr}Assign{/tr}" />
				</div>
			{/if}
		{/if}
	</div>
</form>

{if ( isset($tree) and count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y' ) or ( $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y' ) }
<!--div class="fgal_top_bar" style="height:16px; vertical-align:middle">

{if $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y'}
  <div class="gallerypath" style="vertical-align:middle">&nbsp;&nbsp;{$gallery_path}</div>
{/if}

</div-->
{/if}



{reindex_file_pixel id=$reindex_file_id}<br />
