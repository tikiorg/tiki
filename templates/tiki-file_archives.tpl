{title}
  {tr}File Archive:{/tr} {if empty($file_info.name)}{$file_info.filename|escape}{else}{$file_info.name}{/if}
{/title}

<div class="navbar">
	{if $tiki_p_list_file_galleries eq 'y' or ( ! isset($tiki_p_list_file_galleries) and $tiki_p_view_file_gallery eq 'y' )}
		{button href="tiki-list_file_gallery.php" _text="{tr}List Galleries{/tr}"}
	{/if}

	{assign var=thisgall value=$gal_info.galleryId}
	{button href="tiki-list_file_gallery.php?galleryId=$thisgall" _text="{tr}List Gallery{/tr}"}

	{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user}
		{button href="tiki-list_file_gallery.php?edit_mode=1&amp;galleryId=$thisgall" _text="{tr}Edit Gallery{/tr}"}
	{/if}

	{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user or $gal_info.public eq 'y'}
		{if $tiki_p_upload_files eq 'y'}
			{button href="tiki-upload_file.php?galleryId=$thisgall" _text="{tr}Upload File{/tr}"}
		{/if}
		{if $prefs.feature_file_galleries_batch eq "y" and $tiki_p_batch_upload_file_dir eq 'y'}
			{button href="tiki-batch_upload_files.php?galleryId=$thisgall" _text="{tr}Directory Batch{/tr}"}
		{/if}
	{/if}
</div>

{include file='list_file_gallery.tpl'}
