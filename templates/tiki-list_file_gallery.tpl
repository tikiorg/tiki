{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_file_gallery.tpl,v 1.50.2.1 2007-11-16 20:19:46 sylvieg Exp $ *}
<h1><a class="pagetitle" href="tiki-list_file_gallery.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&filegals_manager{/if}">{tr}Listing Gallery{/tr}: {$name}</a></h1>
{if $filegals_manager eq 'y'}
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
<div class="rbox-data" name="tip">{tr}Be carefull to set the right permissions on the files you link to{/tr}.</div>
</div>
<br />
{/if}

<div class="navbar">
{if $user and $prefs.feature_user_watches eq 'y'}
	{if $user_watching_file_gallery eq 'n'}
		<a href="tiki-list_file_gallery.php?galleryId={$galleryId|escape:"url"}&amp;galleryName={$name|escape:"url"}&amp;watch_event=file_gallery_changed&amp;watch_object={$galleryId|escape:"url"}&amp;watch_action=add"{if $filegals_manager eq 'y'}&filegals_manager{/if}>{html_image file='pics/icons/eye.png' border='0' alt="{tr}Monitor this Gallery{/tr}" title="{tr}Monitor this Gallery{/tr}" align='right'}</a>
	{else}
		<a href="tiki-list_file_gallery.php?galleryId={$galleryId|escape:"url"}&amp;galleryName={$name|escape:"url"}&amp;watch_event=file_gallery_changed&amp;watch_object={$galleryId|escape:"url"}&amp;watch_action=remove{if $filegals_manager eq 'y'}&filegals_manager{/if}">{html_image file='pics/icons/no_eye.png' border='0' alt="{tr}Stop Monitoring this Gallery{/tr}" title="{tr}Stop Monitoring this Gallery{/tr}" align='right'}</a>
	{/if}
{/if}  
{if $prefs.rss_file_gallery eq 'y'}
	{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
	<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}&amp;ver=PODCAST">
	<img src='img/rss_podcast_80_15.png' border='0' alt="{tr}RSS feed{/tr}" title="{tr}RSS feed{/tr}"  align='right' /></a>
	{else}
	<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}">
	<img src='pics/icons/feed.png' border='0' alt="{tr}RSS feed{/tr}" title="{tr}RSS feed{/tr}'"align='right' /></a>
	{/if}
{/if}
{if $tiki_p_list_file_galleries eq 'y' or (!isset($tiki_p_list_file_galleries) and $tiki_p_view_file_gallery eq 'y')}<a href="tiki-file_galleries.php{if $filegals_manager eq 'y'}?filegals_manager{/if}" class="linkbut" title="{tr}List Galleries{/tr}">{tr}List Galleries{/tr}</a>{/if}

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user}
  <a href="tiki-file_galleries.php?edit_mode=1&amp;galleryId={$galleryId}{if $filegals_manager eq 'y'}?filegals_manager{/if}" class="linkbut" title="{tr}Edit Gallery{/tr}">{tr}Edit Gallery{/tr}</a>
{/if}

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user or $gal_info.public eq 'y'}
  {if $tiki_p_upload_files eq 'y'}
    <a href="tiki-upload_file.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&filegals_manager{/if}" class="linkbut">{tr}Upload File{/tr}</a>
  {/if}
  {if $prefs.feature_file_galleries_batch eq "y" and $tiki_p_batch_upload_file_dir eq 'y'}
    <a href="tiki-batch_upload_files.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&filegals_manager{/if}" class="linkbut">{tr}Directory batch{/tr}</a>
  {/if}
{/if}

</div>

<div class="navbar" align="right">
    {if $user and $prefs.feature_user_watches eq 'y'}
        {if $category_watched eq 'y'}
            {tr}Watched by categories{/tr}:
            {section name=i loop=$watching_categories}
			    <a href="tiki-browse_categories?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
            {/section}
        {/if}			
    {/if}
</div>

<table>
<tr><td width="48">
{if $gal_info.type eq "podcast"}
<img src='pics/large/gnome-sound-recorder48x48.png' border='0' alt="{tr}podcast (audio){/tr}" />
{elseif $gal_info.type eq "vidcast"}
<img src='pics/large/mplayer48x48.png' border='0' alt="{tr}podcast (video){/tr}" />
{else}
<img src='pics/large/file-manager48x48.png' border='0' alt="{tr}File Gallery{/tr}" />
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


{if $prefs.feature_file_galleries_comments == 'y'
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
{tr}Add Comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}Hide{/tr})</span>
</a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
