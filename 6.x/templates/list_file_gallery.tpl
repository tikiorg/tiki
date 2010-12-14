{* $Id$ *}

{if ( isset($tree) and count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y' ) or ( $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y' ) }

	<div class="fgal_top_bar" style="height:16px; vertical-align:middle">

		{if isset($tree) and count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y'}
			{if $prefs.javascript_enabled eq 'y'}
				<div id="fgalexplorer_close" style="float:left; vertical-align:middle; display:{if ! isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) or $smarty.session.tiki_cookie_jar.show_fgalexplorer eq 'y'}none{else}inline{/if};">
					<a href="#" onclick="flip('fgalexplorer','');hide('fgalexplorer_close',false);show('fgalexplorer_open',false);return false;">{icon _id='application_side_tree' alt="{tr}Show Tree{/tr}"}</a>
				</div>

				<div id="fgalexplorer_open" style="float:left; vertical-align:middle; display:{if isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) and $smarty.session.tiki_cookie_jar.show_fgalexplorer neq 'y'}none{else}inline{/if};">
					<a href="#" onclick="flip('fgalexplorer','');hide('fgalexplorer_open',false);show('fgalexplorer_close',false);return false;">{icon _id='application_side_contract' alt="{tr}Hide Tree{/tr}"}</a>
				</div>

			{else}

				<div style="float:left; vertical-align:middle">
					{if isset($smarty.request.show_fgalexplorer) and $smarty.request.show_fgalexplorer eq 'y'}
						{self_link _icon='application_side_contract' show_fgalexplorer='n'}{tr}Hide Tree{/tr}{/self_link}
					{else}
						{self_link _icon='application_side_tree' show_fgalexplorer='y'}{tr}Show Tree{/tr}{/self_link}
					{/if}
				</div>
			{/if}
		{/if}

		{if $gallery_path neq '' && $fgal_options.show_path.value eq 'y' && $tiki_p_view_fgal_path eq 'y'}
			<div class="gallerypath" style="vertical-align:middle">&nbsp;&nbsp;{$gallery_path}</div>
		{/if}
	</div>
{/if}

<table border="0" cellpadding="3" cellspacing="3" width="100%" style="clear: both">
	<tr>
		{if isset($tree) && count($tree) gt 0 && $tiki_p_list_file_galleries != 'n' && $fgal_options.show_explorer.value eq 'y' && $tiki_p_view_fgal_explorer eq 'y'}
			<td width="25%" class="fgalexplorer" id="fgalexplorer" style="{if ( isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) and $smarty.session.tiki_cookie_jar.show_fgalexplorer neq 'y') and ( ! isset($smarty.request.show_fgalexplorer) or $smarty.request.show_fgalexplorer neq 'y' ) }display:none;{/if} width: 25%">
				<div style="overflow-x:auto; overflow-y:hidden">
					{include file='file_galleries.tpl'}
				</div>
			</td>

			<td width="75%" class="fgallisting">
		{else}
			<td width="100%" class="fgallisting">
		{/if}

		<div style="padding:1px; overflow-x:auto; overflow-y:hidden;">
			{if $maxRecords > 20 and $cant > $maxRecords}
				<div class="clearboth" style="margin-bottom: 3px;">
					{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
				</div>
			{/if}

			<form name="fgalformid" id="fgalform" method="post" action="{$smarty.server.PHP_SELF}{if $filegals_manager neq ''}?filegals_manager={$filegals_manager|escape}{/if}" enctype="multipart/form-data">
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

				{if $view eq 'browse'}
					{assign var=show_infos value='y'}
					{include file='browse_file_gallery.tpl'}
				{else}
					{assign var=show_infos value='n'}
					{include file='list_file_gallery_content.tpl'}
				{/if}

				{if $files and $gal_info.show_checked neq 'n' and ($tiki_p_admin_file_galleries eq 'y' or $tiki_p_upload_files eq 'y' or $tiki_p_assign_perm_file_gallery eq 'y')}
					<div id="sel">
						<div>
							{tr}Perform action with checked:{/tr}
							{if !isset($file_info)}
								{if $offset}<input type="hidden" name="offset" value="{$offset}" />{/if}
								{if $tiki_p_admin_file_galleries eq 'y'}
									{icon _id='arrow_right' _tag='input_image' name='movesel' alt="{tr}Move{/tr}" title="{tr}Move Selected Files{/tr}" style='vertical-align: middle;'}
								{/if}
							{/if}
							
							{if $tiki_p_admin_file_galleries eq 'y'}
								{icon _id='cross' _tag='input_image' _confirm="{tr}Are you sure you want to delete the selected files?{/tr}" name='delsel' alt="{tr}Delete{/tr}" style='vertical-align: middle;'}
								{icon _id='arrow_refresh' _tag='input_image' _confirm="{tr}Are you sure you want to reset the default gallery list table settings?{/tr}" name='defaultsel' alt="{tr}Reset to default gallery list table settings{/tr}" style='vertical-align: middle;'}
							{/if}
							
							{icon _id='pics/icons/mime/zip.png' _tag='input_image' name='zipsel' alt="{tr}Download the zip{/tr}" style='vertical-align: middle;'}
							
							{if $tiki_p_assign_perm_file_gallery eq 'y'}
								{icon _id='key' _tag='input_image' name='permsel' alt="{tr}Assign Permissions{/tr}" title="{tr}Assign Permissions{/tr}" style='vertical-align: middle;'}
							{/if}
						
						</div>
						
						{if $smarty.request.movesel_x and !isset($file_info)}
							<div>
								{tr}Move to{/tr}:
								<select name="moveto">
									{section name=ix loop=$all_galleries}
										<option value="{$all_galleries[ix].id}">{$all_galleries[ix].label|escape}</option>
									{/section}
								</select>
								<input type='submit' name='movesel' value="{tr}Move{/tr}" />
							</div>
						{/if}
					</div>
					{if $perms}
						<div>
							{tr}Assign Permissions{/tr}
							<select name="perms[]" multiple="multiple" size="5">
								{foreach from=$perms item=perm}
									<option value="{$perm.permName|escape}">{$perm.permName|escape}</option>
								{/foreach}
							</select>
							<select name="groups[]" multiple="multiple" size="5">
								{section name=grp loop=$groups}
									<option value="{$groups[grp].groupName|escape}" {if $groupName eq $groups[grp].groupName }selected="selected"{/if}>{$groups[grp].groupName|escape}</option>
								{/section}
							</select>
							<input type="submit" name="permsel" value="{tr}Assign{/tr}" />
						</div>
					{/if}
					<br style="clear:both"/>
				{/if}
			</form>

			{reindex_file_pixel id=$reindex_file_id}<br />

			{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
			</div>
		</td>
	</tr>
</table>
