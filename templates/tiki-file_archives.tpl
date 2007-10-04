{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-file_archives.tpl,v 1.11 2007-10-04 22:17:40 nyloth Exp $ *}

<h1><a class="pagetitle" href="tiki-file_archives.php?fileId={$file_info.fileId}">{tr}File Archive{/tr}: {if empty($file_info.name)}{$file_info.filename|escape}{else}{$file_info.name}{/if}</a></h1>

<table><tr>
<td style="vertical-align:top;">

{if $tiki_p_list_file_galleries eq 'y' or (!isset($tiki_p_list_file_galleries) and $tiki_p_view_file_gallery eq 'y')}<a href="tiki-file_galleries.php" class="linkbut" title="{tr}List Galleries{/tr}">{tr}List Galleries{/tr}</a>{/if}
<a href="tiki-list_file_gallery.php?galleryId={$gal_info.galleryId}" class="linkbut" title="{tr}List Gallery{/tr}">{tr}List Gallery{/tr}</a>

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user}
  <a href="tiki-file_galleries.php?edit_mode=1&amp;galleryId={$gal_info.galleryId}" class="linkbut" title="{tr}Edit Gallery{/tr}">{tr}Edit Gallery{/tr}</a>
{/if}

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user or $gal_info.public eq 'y'}
  {if $tiki_p_upload_files eq 'y'}
    <a href="tiki-upload_file.php?galleryId={$gal_info.galleryId}" class="linkbut">{tr}Upload File{/tr}</a>
  {/if}
  {if $prefs.feature_file_galleries_batch eq "y" and $tiki_p_batch_upload_file_dir eq 'y'}
    <a href="tiki-batch_upload_files.php?galleryId={$gal_info.galleryId}" class="linkbut">{tr}Directory batch{/tr}</a>
  {/if}
{/if}
</td></tr></table>

<h2>{tr}File{/tr}</h2>
{include file=list_file_gallery.tpl files=$file show_find='n' maxRecords=0 gal_info=$file_gal_info}

<h2>{tr}Archives{/tr}</h2>
{include file=list_file_gallery.tpl ext='file_' sort_mode=$file_sort_mode offset=$file_offset prev_offset=$file_prev_offset next_offset=$file_next_offset find=$file_find}
