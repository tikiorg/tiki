{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_file_gallery.tpl,v 1.36 2006-11-29 18:40:51 sylvieg Exp $ *}
<h1><a class="pagetitle" href="tiki-list_file_gallery.php?galleryId={$galleryId}">{tr}Listing Gallery{/tr}: {$name}</a></h1>

<table><tr>
<td style="vertical-align:top;">

<a href="tiki-file_galleries.php" class="linkbut" title="{tr}list galleries{/tr}">{tr}list galleries{/tr}</a>

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user}
  <a href="tiki-file_galleries.php?edit_mode=1&amp;galleryId={$galleryId}" class="linkbut" title="{tr}edit gallery{/tr}">{tr}edit gallery{/tr}</a>
{/if}

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user or $gal_info.public eq 'y'}
  {if $tiki_p_upload_files eq 'y'}
    <a href="tiki-upload_file.php?galleryId={$galleryId}" class="linkbut">{tr}upload file{/tr}</a>
  {/if}
  {if $feature_file_galleries_batch eq "y" and $tiki_p_batch_upload_file_dir eq 'y'}
    <a href="tiki-batch_upload_files.php?galleryId={$galleryId}" class="linkbut">{tr}Directory batch{/tr}</a>
  {/if}
{/if}

{if $rss_file_gallery eq 'y'}
	{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
	<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}&amp;ver=PODCAST">
	<img src='img/rss_podcast_80_15.png' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
	{else}
	<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}">
	<img src='pics/icons/feed.png' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a>
	{/if}
{/if}

</td>
<td style="text-align:right;width:142px;wrap:nowrap">

       {if $user and $feature_user_watches eq 'y'}
		{if $user_watching_file_gallery eq 'n'}
			<a href="tiki-list_file_gallery.php?galleryId={$galleryId|escape:"url"}&amp;galleryName={$name|escape:"url"}&amp;watch_event=file_gallery_changed&amp;watch_object={$galleryId|escape:"url"}&amp;watch_action=add">{html_image file='pics/icons/eye.png' border='0' alt='{tr}monitor this gallery{/tr}' title='{tr}monitor this gallery{/tr}'}</a>
		{else}
			<a href="tiki-list_file_gallery.php?galleryId={$galleryId|escape:"url"}&amp;galleryName={$name|escape:"url"}&amp;watch_event=file_gallery_changed&amp;watch_object={$galleryId|escape:"url"}&amp;watch_action=remove">{html_image file='pics/icons/no_eye.png' border='0' alt='{tr}stop monitoring this gallery{/tr}' title='{tr}stop monitoring this gallery{/tr}'}</a>
		{/if}
	{/if}  

</td></tr></table>

<table>
<tr><td width="48">
{if $gal_info.type eq "podcast"}
<img src='pics/large/gnome-sound-recorder48x48.png' border='0' alt='{tr}podcast (audio){/tr}' />
{elseif $gal_info.type eq "vidcast"}
<img src='pics/large/mplayer48x48.png' border='0' alt='{tr}podcast (video){/tr}' />
{else}
<img src='pics/large/file-manager48x48.png' border='0' alt='{tr}file gallery{/tr}' />
{/if}
</td>
<td style="vertical-align:top; text-align:left;" width="100%">
{$description|escape}
</td></tr></table>

<h2>{tr}Gallery Files{/tr}</h2>
{include file=list_file_gallery.tpl ext="file_" offset=$file_offset find=$file_find sort_mode=$file_sort_mode prev_offset=$file_prev_offset next_offset=$file_next_offset cant_pages=$file_cant_pages actual_page=$file_actual_page}

{if isset($galleries)}
<h2>{tr}Sub-galleries{/tr}</h2>
{include file=file_galleries.tpl}
{/if}


{if $feature_file_galleries_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
<a href="#" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}add comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}non
e{/if};">({tr}close{/tr})</span>
</a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
