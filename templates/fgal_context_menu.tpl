{strip}
{if $files[changes].isgal eq 1}
	{if $files[changes].perms.tiki_p_view_file_gallery eq 'y'}
		{self_link _script='tiki-list_file_gallery.php' _class='fgalname' _icon='folder_go' galleryId=$files[changes].id}{tr}Go to{/tr}{/self_link}
	{/if}

	{if $files[changes].perms.tiki_p_create_file_galleries eq 'y'}
		{self_link _script='tiki-file_galleries.php' _class='fgalname' _icon='page_edit' edit_mode=1 galleryId=$files[changes].id}{tr}Properties{/tr}{/self_link}
	{/if}

	{if $files[changes].perms.tiki_p_upload_files eq 'y' and ( $files[changes].perms.tiki_p_admin_file_galleries eq 'y' or ($user and $files[changes].user eq $user) or $files[changes].public eq 'y' ) }
		<a class="fgallink" href="tiki-upload_file.php?galleryId={$files[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='upload'}</a>
	{/if}

	{if $files[changes].perms.tiki_p_assign_perm_file_gallery eq 'y'}
            {if $files[changes].perms.has_special_perm eq 'y'}
                <a class="fgallink" href="tiki-objectpermissions.php?objectName={$files[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$files[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='key_active' alt='{tr}Active Perms{/tr}'}</a>
            {else}
                <a class="fgallink" href="tiki-objectpermissions.php?objectName={$files[changes].name|escape:"url"}&amp;objectType=file+gallery&amp;permType=file+galleries&amp;objectId={$files[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='key' alt='{tr}Perms{/tr}'}</a>
            {/if}
        {/if}

	{if $files[changes].perms.tiki_p_create_file_galleries eq 'y'}
		{self_link _script='tiki-file_galleries.php' _class='fgalname' _icon='cross' _ajax='n' removegal=$files[changes].id}{tr}Delete{/tr}{/self_link}
	{/if}
{else}
	{if (isset($files[changes].p_download_files) and  $files[changes].p_download_files eq 'y')
	 or (!isset($files[changes].p_download_files) and $files[changes].perms.tiki_p_download_files eq 'y')}
		{if $gal_info.type eq 'podcast' or $gal_info.type eq 'vidcast'}
			<a class="fgalname" href="{$download_path}{$files[changes].path}" title="{tr}Download{/tr}">
		{else}
			<a class="fgalname" href="tiki-download_file.php?fileId={$files[changes].fileId}" title="{tr}Download{/tr}">
		{/if}
		{icon _id='disk' alt='{tr}Download{/tr}'}</a> 
	{/if}

	{if $files[changes].nbArchives gt 0}
		<a href="tiki-file_archives.php?fileId={$files[changes].fileId}{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}" title="{tr}Archives{/tr}({$files[changes].nbArchives})">{icon _id='disk_multiple' alt='{tr}Archives{/tr}'}</a>
	{elseif $gal_info.archives gt -1}
		{icon _id='disk_multiple_gray' alt='{tr}Archives{/tr}' class=''}
	{/if}

	{* can edit if I am admin or the owner of the file or the locker of the file or if I have the perm to edit file on this gall *}
	{if $files[changes].perms.tiki_p_admin_file_galleries eq 'y'
		or ($files[changes].lockedby and $files[changes].lockedby eq $user)
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or $files[changes].perms.tiki_p_edit_gallery_file eq 'y')) }
		{if $files[changes].archiveId == 0}
			<a class="fgalname" href="tiki-upload_file.php?galleryId={$gal_info.galleryId}&amp;fileId={$files[changes].id}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='page_edit' alt='{tr}Properties{/tr}'}</a>

			{if $gal_info.lockable eq 'y' and $files[changes].isgal neq 1}
				{if $files[changes].lockedby}
					{self_link _script='tiki-list_file_gallery.php' _class='fgalname' _icon='lock_delete' lock='n' fileId=$files[changes].fileId}{tr}Unlock{/tr}{/self_link}
				{else}
					{if $prefs.javascript_enabled eq 'y'}

					{* with javascript, the main page will be reloaded to lock the file and change it's lockedby informations *}
					<a class="fgalname" href="#" title="{tr}Download and lock{/tr}" onclick="window.open('tiki-download_file.php?fileId={$files[changes].fileId}'); document.location.href = '{self_link _type='absolute_uri' _tag='n' fileId=$files[changes].fileId lock=y}{/self_link}'; return true;">{icon _id='disk_lock' alt='{tr}Download and lock{/tr}'}</a>

					{else}

					{* without javascript, the lockedby informations won't be refreshed until the user do it itself *}
					<a class="fgalname" href="tiki-download_file.php?fileId={$files[changes].fileId}&amp;lock=y" title="{tr}Download and lock{/tr}">{icon _id='disk_lock' alt='{tr}Download and lock{/tr}'}</a>

					{/if}
					{self_link _script='tiki-list_file_gallery.php' _class='fgalname' _icon='lock_add' lock='y' fileId=$files[changes].fileId}{tr}Lock{/tr}{/self_link}
				{/if}
			{/if}
		{/if}
	{/if}

	{if $files[changes].perms.tiki_p_admin_file_galleries eq 'y'
		or (!$files[changes].lockedby and (($user and $user eq $files[changes].user) or $files[changes].perms.tiki_p_edit_gallery_file eq 'y')) }
			{self_link _script='tiki-list_file_gallery.php' _class='fgalname' _icon='cross' _ajax='n' remove=$files[changes].fileId}{tr}Delete{/tr}{/self_link}
	{/if}
{/if}
{/strip}
