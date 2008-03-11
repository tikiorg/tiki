{strip}

{if $files[changes].isgal eq 1}
	{if $files[changes].perms.tiki_p_view_file_gallery eq 'y'}
		{self_link _icon='folder_go' _menu_text=$menu_text _menu_icon=$menu_icon galleryId=$files[changes].id}{tr}Go to{/tr}{/self_link}
	{/if}

	{if $files[changes].perms.tiki_p_create_file_galleries eq 'y'}
		{self_link _icon='page_edit' _menu_text=$menu_text _menu_icon=$menu_icon edit_mode=1 galleryId=$files[changes].id}{tr}Properties{/tr}{/self_link}
	{/if}

	{if $files[changes].perms.tiki_p_upload_files eq 'y' and ( $files[changes].perms.tiki_p_admin_file_galleries eq 'y' or ($user and $files[changes].user eq $user) or $files[changes].public eq 'y' ) }
		<a href="tiki-upload_file.php?galleryId={$files[changes].id}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='upload'}</a>
	{/if}

	{if $files[changes].perms.tiki_p_assign_perm_file_gallery eq 'y'}
            {if $files[changes].perms.has_special_perm eq 'y'}
                <a href="tiki-objectpermissions.php?objectName={$files[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$files[changes].id}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='key_active' alt='{tr}Active Perms{/tr}'}</a>
            {else}
                <a href="tiki-objectpermissions.php?objectName={$files[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$files[changes].id}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='key' alt='{tr}Perms{/tr}'}</a>
            {/if}
        {/if}

	{if $files[changes].perms.tiki_p_create_file_galleries eq 'y'}
		{self_link _icon='cross' _menu_text=$menu_text _menu_icon=$menu_icon _ajax='n' removegal=$files[changes].id}{tr}Delete{/tr}{/self_link}
	{/if}
{else}
	{if $prefs.javascript_enabled eq 'y'}
		{if $menu_text eq 'y' or $menu_icon eq 'y'}
			{* This form tag is needed when placed in a popup box through overlib.
			If placed in a column, there is already a form tag around the whole table *}
			<form method="post" action="{$smarty.server.PHP_SELF}?galleryId={$gal_info.galleryId}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}{if $prefs.fgal_asynchronous_indexing eq 'y'}&amp;fast{/if}" enctype="multipart/form-data">
		{/if}
		{if $menu_text neq 'y'}
			{* This is needed for the 'Upload New Version' action to be correctly displayed
			when there is only an icon menu (or actions in a column of the table) *}
		<div style="float:left">
		{/if}
	{/if}

	{if (isset($files[changes].p_download_files) and  $files[changes].p_download_files eq 'y')
	 or (!isset($files[changes].p_download_files) and $files[changes].perms.tiki_p_download_files eq 'y')}
		{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
			<a href="{$download_path}{$files[changes].path}">
		{else}
			<a href="tiki-download_file.php?fileId={$files[changes].fileId}">
		{/if}
		{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk' alt='{tr}Download{/tr}'}</a> 
	{/if}

	{if $gal_info.archives gt -1}
		{if $files[changes].nbArchives gt 0}
			{assign var=nb_archives value=$files[changes].nbArchives}
			<a href="tiki-file_archives.php?fileId={$files[changes].fileId}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_multiple' alt="{tr}Archives{/tr} ($nb_archives)"}</a>
		{else}
			{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_multiple_gray' alt='{tr}Archives{/tr}'}
		{/if}
	{/if}

	{* can edit if I am admin or the owner of the file or the locker of the file or if I have the perm to edit file on this gall *}
	{if $files[changes].perms.tiki_p_admin_file_galleries eq 'y'
		or ($files[changes].lockedby and $files[changes].lockedby eq $user)
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or $files[changes].perms.tiki_p_edit_gallery_file eq 'y')) }
		{if $files[changes].archiveId == 0}

			{if $prefs.javascript_enabled eq 'y'}
				{* if javascript is available on client, add a menu item that will directly open a file selector, automatically upload the file after selection and that replace the current file with the uploaded one *}

				{if $menu_text neq 'y'}</div>{/if}
				<div class="upspan" style="position:relative{if $menu_text eq 'y'}; _position:absolute;{else}; float:left{/if}; overflow:hidden" title="{tr}Upload New Version{/tr}">
					<input type="file" style="position:absolute; right:0; top:0; font-size:600px; opacity:0; -moz-opacity:0; filter:alpha(opacity=0); cursor:pointer" name="upfile{$files[changes].id}" onchange="this.form.submit(); return false;"/>
					<a{if $menu_text eq 'y'} style="_display:none"{/if} href="#">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='database_refresh' alt='{tr}Upload New Version{/tr}'}</a>
				</div>

				{if $menu_text eq 'y'}
					{* the line above is to used for IE only *}
					<a style="display:none; _display:block" href="#" onclick="document.getElementById('upfile{$files[changes].id}').click()">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='database_refresh' alt='{tr}Upload New Version{/tr}'}</a>
				{/if}

			{else}
				{* for the moment, no-javascript version is simply a link to the edit page where you can also upload *}
				<a href="tiki-upload_file.php?galleryId={$gal_info.galleryId}&amp;fileId={$files[changes].id}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='database_refresh' alt='{tr}Upload New Version{/tr}'}</a>
				
			{/if}

			<a href="tiki-upload_file.php?galleryId={$gal_info.galleryId}&amp;fileId={$files[changes].id}{if $filegals_manager eq 'y'}&amp;filegals_manager=y{/if}">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='page_edit' alt='{tr}Properties{/tr}'}</a>

			{if $gal_info.lockable eq 'y' and $files[changes].isgal neq 1}
				{if $files[changes].lockedby}
					{self_link _icon='lock_delete' _menu_text=$menu_text _menu_icon=$menu_icon lock='n' fileId=$files[changes].fileId}{tr}Unlock{/tr}{/self_link}
				{else}
					{if $prefs.javascript_enabled eq 'y'}

					{* with javascript, the main page will be reloaded to lock the file and change it's lockedby informations *}
					<a href="#" onclick="window.open('tiki-download_file.php?fileId={$files[changes].fileId}'); document.location.href = '{self_link _type='absolute_uri' _tag='n' fileId=$files[changes].fileId lock=y}{/self_link}'; return false;">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_lock' alt='{tr}Download and lock{/tr}'}</a>

					{else}

					{* without javascript, the lockedby informations won't be refreshed until the user do it itself *}
					<a href="tiki-download_file.php?fileId={$files[changes].fileId}&amp;lock=y">{icon _menu_text=$menu_text _menu_icon=$menu_icon _id='disk_lock' alt='{tr}Download and lock{/tr}'}</a>

					{/if}
					{self_link _icon='lock_add' _menu_text=$menu_text _menu_icon=$menu_icon lock='y' fileId=$files[changes].fileId}{tr}Lock{/tr}{/self_link}
				{/if}
			{/if}
		{/if}
	{/if}

	{if $files[changes].perms.tiki_p_admin_file_galleries eq 'y'
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or $files[changes].perms.tiki_p_edit_gallery_file eq 'y')) }
			{self_link _icon='cross' _menu_text=$menu_text _menu_icon=$menu_icon _ajax='n' remove=$files[changes].fileId}{tr}Delete{/tr}{/self_link}
	{/if}

	{if $prefs.javascript_enabled eq 'y'}
		{if $menu_text eq 'y' or $menu_icon eq 'y'}
			</form>
		{/if}
	{/if}
{/if}

{/strip}
