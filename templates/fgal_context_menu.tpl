{strip}

{if $files[changes].isgal eq 1}
	{if $files[changes].perms.tiki_p_view_file_gallery eq 'y'}
		{self_link _icon='folder_go' _menu_text=$menu_text _menu_icon=$menu_icon galleryId=$files[changes].id}{tr}Go to{/tr}{/self_link}
	{/if}

	{if $files[changes].perms.tiki_p_create_file_galleries eq 'y'}
		{self_link _icon='page_edit' _menu_text=$menu_text _menu_icon=$menu_icon edit_mode=1 galleryId=$files[changes].id}{tr}Properties{/tr}{/self_link}
	{/if}

	{if $files[changes].perms.tiki_p_upload_files eq 'y' and ( $files[changes].perms.tiki_p_admin_file_galleries eq 'y' or ($user and $files[changes].user eq $user) or $files[changes].public eq 'y' )}
		<a href="tiki-upload_file.php?galleryId={$files[changes].id}{if $filegals_manager neq ''}&amp;filegals_manager={$filegals_manager|escape}{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='upload'}</a>
	{/if}

	{if $files[changes].perms.tiki_p_assign_perm_file_gallery eq 'y'}
            <a href="tiki-objectpermissions.php?objectName={$files[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$files[changes].id}{if $filegals_manager neq ''}&amp;filegals_manager={$filegals_manager|escape}{/if}">
            {if $files[changes].public neq 'y'}
							{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='key_private' alt="{tr}Private Gallery{/tr}"}
            {elseif $files[changes].perms.has_special_perm eq 'y'}
							{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='key_active' alt="{tr}Active Perms{/tr}"}
            {else}
                {icon _menu_text=$menu_text _menu_icon=$menu_icon _id='key' alt="{tr}Perms{/tr}"}
            {/if}
						</a>
        {/if}
	  {if $prefs.feature_webdav eq 'y'}
    	{assign var=virtual_path value=$files[changes].id|virtual_path:'filegal'}
			<a style="behavior: url(#default#AnchorClick);" href="{$virtual_path}" folder="{$virtual_path}">{icon _id="tree_folder_open"}{tr}Open as WebFolder{/tr}</a>
  	{/if}


	{if $files[changes].perms.tiki_p_create_file_galleries eq 'y'}
		{self_link _icon='cross' _menu_text=$menu_text _menu_icon=$menu_icon removegal=$files[changes].id}{tr}Delete{/tr}{/self_link}
	{/if}
{else}
	{if $prefs.javascript_enabled eq 'y'}
		{if $menu_text eq 'y' or $menu_icon eq 'y'}
			{* This form tag is needed when placed in a popup box through the popup function.
			If placed in a column, there is already a form tag around the whole table *}

			<form class="upform" name="form{$files[changes].fileId}" method="post" action="tiki-list_file_gallery.php?galleryId={$gal_info.galleryId}{if $filegals_manager neq ''}&amp;filegals_manager={$filegals_manager|escape}{/if}{if $prefs.fgal_asynchronous_indexing eq 'y'}&amp;fast{/if}" enctype="multipart/form-data">

		{/if}
		{if $menu_text neq 'y'}
			{* This is needed for the 'Upload New Version' action to be correctly displayed
			when there is only an icon menu (or actions in a column of the table) *}
		<div style="float:left">
		{/if}
	{/if}

	{if $files[changes].type|truncate:6:'':true eq 'image/'}
		<a href="{$files[changes].id|sefurl:display}">
		{icon _id='magnifier' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Display{/tr}"}
		</a>
		{if $files[changes].type eq 'image/svg+xml'}
			<a href="tiki-edit_draw.php?paramurl=tiki-download_file.php?fileId={$files[changes].id}">
			{icon _id='page_edit' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Edit{/tr}"}
			</a>
		{/if}
	{elseif $files[changes].type eq 'text/csv'}
		<a href="tiki-view_sheets.php?fileId={$files[changes].id}">
		{icon _id='magnifier' _menu_text=$menu_text _menu_icon=$menu_icon alt="{tr}Display{/tr}"}
		</a>
	{/if}
	
	{if (isset($files[changes].p_download_files) and  $files[changes].p_download_files eq 'y')
	 or (!isset($files[changes].p_download_files) and $files[changes].perms.tiki_p_download_files eq 'y')}
		{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
			<a href="{$download_path}{$files[changes].path}">
		{else}
			<a href="{$files[changes].id|sefurl:file}">
		{/if}
		{if $prefs.feature_file_galleries_save_draft eq 'y' and $files[changes].nbDraft gt 0}
			{assign var=download_action_title value="{tr}Download current version{/tr}"}
		{else}
			{assign var=download_action_title value="{tr}Download{/tr}"}
		{/if}

		{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk' alt="$download_action_title"}</a>
	{/if}

	{if $gal_info.archives gt -1}
		{if $files[changes].nbArchives gt 0}
			{assign var=nb_archives value=$files[changes].nbArchives}
			<a href="tiki-file_archives.php?fileId={$files[changes].fileId}{if $filegals_manager neq ''}&amp;filegals_manager={$filegals_manager|escape}{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_multiple' alt="{tr}Archives{/tr} ($nb_archives)"}</a>
		{else}
			{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_multiple_gray' alt="{tr}Archives{/tr}"}
		{/if}
		{assign var=replace_action_title value="{tr}Upload New Version{/tr}"}
	{else}
		{assign var=replace_action_title value="{tr}Replace{/tr}"}
	{/if}

	{if $prefs.feature_file_galleries_save_draft eq 'y'}
		{if $files[changes].nbDraft gt 0}
			{assign var=replace_action_title value="{tr}Replace your draft{/tr}"}
		{else}
			{assign var=replace_action_title value="{tr}Upload your draft{/tr}"}
		{/if}
	{/if}
	{* can edit if I am admin or the owner of the file or the locker of the file or if I have the perm to edit file on this gallery *}
	{if $files[changes].perms.tiki_p_admin_file_galleries eq 'y'
		or ($files[changes].lockedby and $files[changes].lockedby eq $user)
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or $files[changes].perms.tiki_p_edit_gallery_file eq 'y'))}
		{if $files[changes].archiveId == 0}
			{if $prefs.feature_file_galleries_save_draft eq 'y' and $files[changes].nbDraft gt 0}
				{self_link _icon='accept' _menu_text=$menu_text _menu_icon=$menu_icon validate=$files[changes].fileId galleryId=$files[changes].galleryId}{tr}Validate your draft{/tr}{/self_link}
				{self_link _icon='cross' _menu_text=$menu_text _menu_icon=$menu_icon draft=remove remove=$files[changes].fileId galleryId=$files[changes].galleryId}{tr}Delete your draft{/tr}{/self_link}
			{/if}

			{if $files[changes].perms.tiki_p_admin_file_galleries eq 'y' or !$files[changes].locked or ($files[changes].locked and $files[changes].lockedby eq $user) or $gal_info.lockable ne 'y'}
			{if $prefs.javascript_enabled eq 'y'}
				{* if javascript is available on client, add a menu item that will directly open a file selector, automatically upload the file after selection and that replace the current file with the uploaded one *}

				{if $menu_text neq 'y'}</div>{/if}
				
				{if $prefs.fgal_display_replace eq 'y'}
					<div class="upspan {if $menu_text eq 'y'}upspantext{/if}" style="display: inline; position:relative{if $menu_text eq 'y'}; position:absolute{else}; float:left{/if}; overflow:hidden" title="{$replace_action_title}">
						<input type="file" style="position:absolute; z-index:1001; right:0; top:0; font-size:600px; opacity:0; -moz-opacity:0; filter:alpha(opacity=0); cursor:pointer" name="upfile{$files[changes].id}" onchange="this.form.submit(); return false;"/>
						<a href="#">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='database_refresh' alt=$replace_action_title}</a>
					</div>

					{if $menu_text eq 'y'}
						{* the line above is used to give enough space to the real 'Upload New Version' button *}
						<a style="visibility: hidden">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='database_refresh' alt=$replace_action_title}</a>
					{/if}
				{/if}

			{else}
				{* for the moment, no-javascript version is simply a link to the edit page where you can also upload *}
				<a href="tiki-upload_file.php?galleryId={$files[changes].galleryId}&amp;fileId={$files[changes].id}{if $filegals_manager eq 'y'}&amp;filegals_manager={$filegals_manager|escape}{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='database_refresh' alt="{tr}Upload New Version{/tr}"}</a>
				
			{/if}

			{if $prefs.fgal_display_properties eq 'y'}
				<a href="tiki-upload_file.php?galleryId={$files[changes].galleryId}&amp;fileId={$files[changes].id}{if $filegals_manager neq ''}&amp;filegals_manager={$filegals_manager|escape}{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='page_edit' alt="{tr}Properties{/tr}"}</a>
			{/if}
			{/if}

			{if $gal_info.lockable eq 'y' and $files[changes].isgal neq 1}
				{if $files[changes].lockedby}
					{self_link _icon='lock_delete' _menu_text=$menu_text _menu_icon=$menu_icon lock='n' fileId=$files[changes].fileId galleryId=$files[changes].galleryId}{tr}Unlock{/tr}{/self_link}
				{else}
					{if $prefs.javascript_enabled eq 'y'}

					{* with javascript, the main page will be reloaded to lock the file and change it's lockedby informations *}
					<a href="#" onclick="window.open('{$files[changes].fileId|sefurl:file}&lock=y'); document.location.href = '{self_link _type='absolute_uri' _tag='n' fileId=$files[changes].fileId lock=y galleryId=$files[changes].galleryId}{/self_link}'; return false;">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_lock' alt="{tr}Download and lock{/tr}"}</a>

					{else}

					{* without javascript, the lockedby informations won't be refreshed until the user do it itself *}
					<a href="{$files[changes].fileId|sefurl:file}&amp;lock=y">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_lock' alt="{tr}Download and lock{/tr}"}</a>

					{/if}
					{self_link _icon='lock_add' _menu_text=$menu_text _menu_icon=$menu_icon lock='y' fileId=$files[changes].fileId galleryId=$files[changes].galleryId}{tr}Lock{/tr}{/self_link}
				{/if}
			{/if}
		{/if}
	{/if}

	{if $prefs.feature_webdav eq 'y'}
		{assign var=virtual_path value=$files[changes].fileId|virtual_path}

		{if $prefs.feature_file_galleries_save_draft eq 'y'}
			{self_link _icon="tree_folder_open" _menu_text=$menu_text _menu_icon=$menu_icon _script="javascript:open_webdav('$virtual_path')" _noauto="y" _ajax="n"}{tr}Open your draft in WebDAV{/tr}{/self_link}
		{else}
			{self_link _icon="tree_folder_open" _menu_text=$menu_text _menu_icon=$menu_icon _script="javascript:open_webdav('$virtual_path')" _noauto="y" _ajax="n"}{tr}Open in WebDAV{/tr}{/self_link}
		{/if}
	{/if}

	{if $prefs.feature_share eq 'y' and $tiki_p_share eq 'y'}
		<a href="tiki-share.php?url={$tikiroot}{$files[changes].id|sefurl:file|escape:'url'}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='share_link' alt="{tr}Share a link to this file{/tr}"}</a>
	{/if}
	{if $prefs.feature_tell_a_friend eq 'y' and $tiki_p_tell_a_friend eq 'y'}
		<a href="tiki-tell_a_friend.php?url={$tikiroot}{$files[changes].id|sefurl:file|escape:'url'}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='email_go' alt="{tr}Email a link to this file{/tr}"}</a>
	{/if}

	{if $files[changes].perms.tiki_p_admin_file_galleries eq 'y'
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or ($files[changes].perms.tiki_p_edit_gallery_file eq 'y' and $files[changes].perms.tiki_p_remove_file eq 'y')))}
			{self_link _icon='cross' _menu_text=$menu_text _menu_icon=$menu_icon remove=$files[changes].fileId galleryId=$files[changes].galleryId}{tr}Delete{/tr}{/self_link}
	{/if}

	{if $prefs.javascript_enabled eq 'y'}
		{if $menu_text eq 'y' or $menu_icon eq 'y'}
			</form>
		{/if}
	{/if}
{/if}

{/strip}
