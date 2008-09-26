{* $Id$ *}
{popup_init src="lib/overlib.js"}

{title}
  {tr}File Archive{/tr}: {if empty($file_info.name)}{$file_info.filename|escape}{else}{$file_info.name}{/if}
{/title}

{if $tiki_p_list_file_galleries eq 'y' or ( ! isset($tiki_p_list_file_galleries) and $tiki_p_view_file_gallery eq 'y' )}
	<span class="button2"><a href="tiki-list_file_gallery.php" title="{tr}List Galleries{/tr}">{tr}List Galleries{/tr}</a></span>
{/if}

<span class="button2"><a href="tiki-list_file_gallery.php?galleryId={$gal_info.galleryId}" title="{tr}List Gallery{/tr}">{tr}List Gallery{/tr}</a></span>

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user}
  <span class="button2"><a href="tiki-list_file_gallery.php?edit_mode=1&amp;galleryId={$gal_info.galleryId}" title="{tr}Edit Gallery{/tr}">{tr}Edit Gallery{/tr}</a></span>
{/if}

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user or $gal_info.public eq 'y'}
  {if $tiki_p_upload_files eq 'y'}
    <span class="button2"><a href="tiki-upload_file.php?galleryId={$gal_info.galleryId}">{tr}Upload File{/tr}</a></span>
  {/if}
  {if $prefs.feature_file_galleries_batch eq "y" and $tiki_p_batch_upload_file_dir eq 'y'}
    <span class="button2"><a href="tiki-batch_upload_files.php?galleryId={$gal_info.galleryId}">{tr}Directory batch{/tr}</a></span>
  {/if}
{/if}

<br /><br />
{include file=list_file_gallery.tpl}
