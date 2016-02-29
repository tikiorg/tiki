{* $Id$ *}
{strip}
	{if $files[$changes].isgal eq 1}
		{if $files[$changes].perms.tiki_p_view_file_gallery eq 'y'}
			{self_link _icon_name='file-archive-open' _menu_text=$menu_text _menu_icon=$menu_icon galleryId=$files[$changes].id}
				{tr}Open{/tr}
			{/self_link}
		{/if}

		{if $files[$changes].perms.tiki_p_create_file_galleries eq 'y'}
			{self_link _icon_name='edit' _menu_text=$menu_text _menu_icon=$menu_icon edit_mode=1 galleryId=$files[$changes].id}
				{tr}Properties{/tr}
			{/self_link}
		{/if}

		{if $files[$changes].perms.tiki_p_upload_files eq 'y'
			and ( $files[$changes].perms.tiki_p_admin_file_galleries eq 'y' or ($user and $files[$changes].user eq $user)
			or $files[$changes].public eq 'y' )}
			<a href="tiki-upload_file.php?galleryId={$files[$changes].id}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}">
				<div class="iconmenu">
					{icon name='export'} {tr}Export{/tr}
				</div>
			</a>
		{/if}

		{if $files[$changes].perms.tiki_p_assign_perm_file_gallery eq 'y'}
			<div class="iconmenu">
				{if $files[$changes].public neq 'y'}
					{permission_link mode=text type="file gallery" permType="file galleries" id=$files[$changes].id title=$files[$changes].name}
				{else}
					{permission_link mode=text type="file gallery" permType="file galleries" id=$files[$changes].id title=$files[$changes].name}
				{/if}
			</div>
		{/if}
		{if $prefs.feature_webdav eq 'y'}
			{assign var=virtual_path value=$files[$changes].id|virtual_path:'filegal'}
			<a style="behavior: url(#default#AnchorClick);" href="{$virtual_path}" folder="{$virtual_path}">
				{icon name="file-archive-open"}{tr}Open as WebFolder{/tr}
			</a>
		{/if}

		{if $files[$changes].perms.tiki_p_create_file_galleries eq 'y'}
			{self_link _icon_name='remove' _menu_text=$menu_text _menu_icon=$menu_icon removegal=$files[$changes].id}
				{tr}Delete{/tr}
			{/self_link}
		{/if}
	{else}
		{if $prefs.javascript_enabled eq 'y'}
			{if $menu_text eq 'y' or $menu_icon eq 'y'}
				{* This form tag is needed when placed in a popup box through the popup function.
				If placed in a column, there is already a form tag around the whole table *}
				<form class="upform" name="form{$files[$changes].fileId}" method="post" action="tiki-list_file_gallery.php?galleryId={$gal_info.galleryId}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}{if $prefs.fgal_asynchronous_indexing eq 'y'}&amp;fast{/if}" enctype="multipart/form-data">
			{/if}
			{if $menu_text neq 'y'}
				{* This is needed for the 'Upload New Version' action to be correctly displayed
				when there is only an icon menu (or actions in a column of the table) *}
				<div style="float:left">
			{/if}
		{/if}

		{if $files[$changes].type|truncate:6:'':true eq 'image/' and $files[$changes].perms.tiki_p_download_files eq 'y'}
			<a href="{$files[$changes].id|sefurl:display}">
				{icon name='view' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Display{/tr}"}
			</a>
			{if $files[$changes].perms.tiki_p_upload_files eq 'y' and $prefs.feature_draw eq 'y'}
				{if
					$files[$changes].type eq 'image/svg+xml' 	or
					$files[$changes].type eq 'image/jpeg' 		or
					$files[$changes].type eq 'image/gif' 		or
					$files[$changes].type eq 'image/png' 		or
					$files[$changes].type eq 'image/tiff'
				}
					<a class="draw dialog" data-name="{$files[$changes].filename}" title="{tr}Edit: {/tr}{$files[$changes].filename}" href="tiki-edit_draw.php?fileId={$files[$changes].id}&galleryId={$files[$changes].galleryId}" data-fileid='{$files[$changes].id}' data-galleryid='{$files[$changes].galleryId}' onclick='$(document).trigger("hideCluetip"); return $(this).ajaxEditDraw();'>
						{icon name='edit' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Edit{/tr}"}
					</a>
				{/if}
			{/if}
		{elseif $files[$changes].type eq 'text/csv' and $prefs.feature_sheet eq 'y'}
			<a href="tiki-view_sheets.php?fileId={$files[$changes].id}">
				{icon name='view' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Display{/tr}"}
			</a>
		{elseif $prefs.fgal_viewerjs_feature eq 'y' and ($files[$changes].type eq 'application/pdf' or $files[$changes].type|strpos:'application/vnd.oasis.opendocument.' !== false)}
			<a href="{$prefs.fgal_viewerjs_uri}#{$base_url}{$files[$changes].id|sefurl:display}">
				{icon name='view' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Display{/tr}"}
			</a>
		{elseif ($files[$changes].type eq 'application/vnd.oasis.opendocument.text'
			or $files[$changes].type eq 'application/octet-stream') and $prefs.feature_docs eq 'y'}
			<a href="tiki-edit_docs.php?fileId={$files[$changes].id}">
				{icon name='view' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Display{/tr}"}
			</a>
			{if $files[$changes].perms.tiki_p_upload_files eq 'y'}
				<a href="tiki-edit_docs.php?fileId={$files[$changes].id}&edit">
					{icon name='edit' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Edit{/tr}"}
				</a>
			{/if}
		{/if}

		{if (isset($files[$changes].p_download_files) and $files[$changes].p_download_files eq 'y')
			or (!isset($files[$changes].p_download_files) and $files[$changes].perms.tiki_p_download_files eq 'y')}
			{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
				<a href="{$download_path}{$files[$changes].path}">
			{else}
				<a href="{$files[$changes].id|sefurl:file}">
			{/if}
				{if $prefs.feature_file_galleries_save_draft eq 'y' and $files[$changes].nbDraft gt 0}
					{assign var=download_action_title value="{tr}Download current version{/tr}"}
				{else}
					{assign var=download_action_title value="{tr}Download{/tr}"}
				{/if}
				{icon _menu_text=$menu_text _menu_icon=$menu_icon name='floppy' alt="$download_action_title"}
			</a>
		{/if}

		{if $gal_info.archives gt -1}
			{if isset($files[$changes].nbArchives) and $files[$changes].nbArchives gt 0}
				{assign var=nb_archives value=$files[$changes].nbArchives}
				<a href="tiki-file_archives.php?fileId={$files[$changes].fileId}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}">
					{icon _menu_text=$menu_text _menu_icon=$menu_icon name='file-archive' alt="{tr}Archives{/tr} ($nb_archives)"}
				</a>
			{else}
				{icon _menu_text=$menu_text _menu_icon=$menu_icon name='file-archive' alt="{tr}Archives{/tr}"}
			{/if}
			{assign var=replace_action_title value="{tr}Upload new version{/tr}"}
		{else}
			{assign var=replace_action_title value="{tr}Replace{/tr}"}
		{/if}

		{if $prefs.feature_file_galleries_save_draft eq 'y'}
			{if $files[$changes].nbDraft gt 0}
				{assign var=replace_action_title value="{tr}Replace draft{/tr}"}
			{else}
				{assign var=replace_action_title value="{tr}Upload draft{/tr}"}
			{/if}
		{/if}
		{* can edit if I am admin or the owner of the file or the locker of the file or if I have the perm to edit file on this gallery *}
		{if $files[$changes].perms.tiki_p_admin_file_galleries eq 'y'
			or ($files[$changes].lockedby and $files[$changes].lockedby eq $user)
			or (!$files[$changes].lockedby and (($user and $user eq $files[$changes].user)
			or $files[$changes].perms.tiki_p_edit_gallery_file eq 'y'))}
			{if $files[$changes].archiveId == 0}
				{if $prefs.feature_file_galleries_save_draft eq 'y' and $files[$changes].nbDraft gt 0}
					{self_link _icon_name='ok' _menu_text=$menu_text _menu_icon=$menu_icon validate=$files[$changes].fileId galleryId=$files[$changes].galleryId}
						{tr}Validate your draft{/tr}
					{/self_link}
					{self_link _icon_name='remove' _menu_text=$menu_text _menu_icon=$menu_icon draft=remove remove=$files[$changes].fileId galleryId=$files[$changes].galleryId}
						{tr}Delete your draft{/tr}
					{/self_link}
				{/if}

				{if $files[$changes].perms.tiki_p_admin_file_galleries eq 'y' or empty($files[$changes].locked)
					or (isset($files[$changes].locked) and $files[$changes].locked and $files[$changes].lockedby eq $user)
					or $gal_info.lockable ne 'y'}
					{if $prefs.javascript_enabled eq 'y'}
						{* if javascript is available on client, add a menu item that will directly open a file selector, automatically upload the file after selection and that replace the current file with the uploaded one *}

						{if $menu_text neq 'y'}</div>{/if}

						{if $prefs.fgal_display_replace eq 'y'}
							<div class="upspan {if $menu_text eq 'y'}upspantext{/if}" style="display: inline; position:relative{if $menu_text eq 'y'}; position:absolute{else}; float:left{/if}; overflow:hidden" title="{$replace_action_title}">
								<input type="file" style="position:absolute; z-index:1001; right:0; top:0; font-size:600px; opacity:0; -moz-opacity:0; filter:alpha(opacity=0); cursor:pointer; margin: 0; padding: 0" name="upfile{$files[$changes].id}" onchange="this.form.submit(); return false;">
								<input type="hidden" name="fileId" value="{$files[$changes].fileId}">
								<a href="#">
									{icon _menu_text=$menu_text _menu_icon=$menu_icon name='export' alt=$replace_action_title}
								</a>
							</div>

							{if $menu_text eq 'y'}
								{* the line above is used to give enough space to the real 'Upload New Version' button *}
								<a style="visibility: hidden">
									{icon _menu_text=$menu_text _menu_icon=$menu_icon name='export' alt=$replace_action_title}
								</a>
							{/if}
						{/if}

					{else}
						{* for the moment, no-javascript version is simply a link to the edit page where you can also upload *}
						<a href="tiki-upload_file.php?galleryId={$files[$changes].galleryId}&amp;fileId={$files[$changes].id}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}">
							{icon _menu_text=$menu_text _menu_icon=$menu_icon name='export' alt="{tr}Upload new version{/tr}"}
						</a>
					{/if}

					{if $prefs.fgal_display_properties eq 'y'}
						{if $view != 'page'}
							{$pageoffset = $changes - $subcount + $offset}
							<a href="tiki-list_file_gallery.php?galleryId={$files[$changes].galleryId}&offset={$pageoffset}&fileId={$files[$changes].id}&view=page">
								{icon _menu_text=$menu_text _menu_icon=$menu_icon name='textfile' alt="{tr}Page view{/tr}"}
							</a>
						{/if}
						<a href="tiki-upload_file.php?galleryId={$files[$changes].galleryId}&amp;fileId={$files[$changes].id}{if !empty($filegals_manager)}&amp;filegals_manager={$filegals_manager|escape}{/if}">
							{icon _menu_text=$menu_text _menu_icon=$menu_icon name='edit' alt="{tr}Edit properties{/tr}"}
						</a>
						{* using &amp; causes an error for some reason - therefore using plain & *}
						<a href="tiki-list_file_gallery.php?galleryId={$files[$changes].galleryId}&fileId={$files[$changes].id}&action=refresh_metadata{if isset($view)}&view={$view}{/if}">
							{icon _menu_text=$menu_text _menu_icon=$menu_icon name='tag' alt="{tr}Refresh metadata{/tr}"}
						</a>
					{/if}
				{/if}

				{if $gal_info.lockable eq 'y' and $files[$changes].isgal neq 1}
					{if $files[$changes].lockedby}
						{self_link _icon_name='unlock' _menu_text=$menu_text _menu_icon=$menu_icon lock='n' fileId=$files[$changes].fileId galleryId=$files[$changes].galleryId}
							{tr}Unlock{/tr}
						{/self_link}
					{else}
						{if (isset($files[$changes].p_download_files) and $files[$changes].p_download_files eq 'y')
							or (!isset($files[$changes].p_download_files) and $files[$changes].perms.tiki_p_download_files eq 'y')}
							{if $prefs.javascript_enabled eq 'y'}
								{* with javascript, the main page will be reloaded to lock the file and change it's lockedby informations *}
								<a href="#" onclick="window.open('{$files[$changes].fileId|sefurl:file:with_next}lock=y'); document.location.href = '{self_link _type='absolute_uri' _tag='n' fileId=$files[$changes].fileId lock=y galleryId=$files[$changes].galleryId}{/self_link}'; return false;">
									{icon _menu_text=$menu_text _menu_icon=$menu_icon name='import' alt="{tr}Download and lock{/tr}"}
								</a>
							{else}
								{* without javascript, the lockedby information won't be refreshed until the user do it itself *}
								<a href="{$files[$changes].fileId|sefurl:file:with_next}lock=y">
									{icon _menu_text=$menu_text _menu_icon=$menu_icon name='import' alt="{tr}Download and lock{/tr}"}
								</a>
							{/if}
						{/if}
						{self_link _icon_name='lock' _menu_text=$menu_text _menu_icon=$menu_icon lock='y' fileId=$files[$changes].fileId galleryId=$files[$changes].galleryId}
							{tr}Lock{/tr}
						{/self_link}
					{/if}
				{/if}
			{/if}
		{/if}

		{if $prefs.feature_webdav eq 'y'}
			{assign var=virtual_path value=$files[$changes].fileId|virtual_path}

			{if $prefs.feature_file_galleries_save_draft eq 'y'}
				{self_link _icon_name="file-archive-open" _menu_text=$menu_text _menu_icon=$menu_icon _script="javascript:open_webdav('$virtual_path')" _noauto="y" _ajax="n"}
					{tr}Open your draft in WebDAV{/tr}
				{/self_link}
			{else}
				{self_link _icon_name="file-archive-open" _menu_text=$menu_text _menu_icon=$menu_icon _script="javascript:open_webdav('$virtual_path')" _noauto="y" _ajax="n"}
					{tr}Open in WebDAV{/tr}
				{/self_link}
			{/if}
		{/if}

		{if $prefs.feature_share eq 'y' and $tiki_p_share eq 'y'}
			<a href="tiki-share.php?url={$tikiroot}{$files[$changes].id|sefurl:file|escape:'url'}">
				{icon _menu_text=$menu_text _menu_icon=$menu_icon name='share' alt="{tr}Share a link to this file{/tr}"}
			</a>
		{/if}
		{if $prefs.feature_tell_a_friend eq 'y' and $tiki_p_tell_a_friend eq 'y'}
			<a href="tiki-tell_a_friend.php?url={$tikiroot}{$files[$changes].id|sefurl:file|escape:'url'}">
				{icon _menu_text=$menu_text _menu_icon=$menu_icon name='envelope' alt="{tr}Email a link to this file{/tr}"}
			</a>
		{/if}

		{if $files[$changes].perms.tiki_p_admin_file_galleries eq 'y'
			or (!$files[$changes].lockedby and (($user and $user eq $files[$changes].user)
			or ($files[$changes].perms.tiki_p_edit_gallery_file eq 'y'
			and $files[$changes].perms.tiki_p_remove_files eq 'y')))}
				<a href="tiki-list_file_gallery.php?remove={$files[$changes].fileId}&galleryId={$files[$changes].galleryId}">
					{icon _menu_text=$menu_text _menu_icon=$menu_icon name='remove' alt="{tr}Delete{/tr}"}
				</a>
		{/if}

		{if $prefs.javascript_enabled eq 'y'}
			{if $menu_text eq 'y' or $menu_icon eq 'y'}
				</form>
			{/if}
		{/if}
	{/if}
{/strip}
