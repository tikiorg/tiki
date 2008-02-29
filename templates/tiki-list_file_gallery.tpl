{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-list_file_gallery.tpl,v 1.50.2.11 2008-02-29 22:19:26 sylvieg Exp $ *}
{popup_init src="lib/overlib.js"}
<h1><a class="pagetitle" href="tiki-list_file_gallery.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}">{tr}Gallery{/tr}: {$name}</a></h1>

<div name="description">{$description|escape}</div>

<div class="navbar">
{if $user and $prefs.feature_user_watches eq 'y'}
	{if $user_watching_file_gallery eq 'n'}
		<a href="tiki-list_file_gallery.php?galleryId={$galleryId|escape:"url"}&amp;galleryName={$name|escape:"url"}&amp;watch_event=file_gallery_changed&amp;watch_object={$galleryId|escape:"url"}&amp;watch_action=add"{if $filegals_manager eq 'y'}&filegals_manager{/if}>{icon _id='eye' align='right' alt="{tr}Monitor this Gallery{/tr}"}</a>
	{else}
		<a href="tiki-list_file_gallery.php?galleryId={$galleryId|escape:"url"}&amp;galleryName={$name|escape:"url"}&amp;watch_event=file_gallery_changed&amp;watch_object={$galleryId|escape:"url"}&amp;watch_action=remove{if $filegals_manager eq 'y'}&filegals_manager{/if}">{icon _id='no_eye' align='right' alt="{tr}Stop Monitoring this Gallery{/tr}"}</a>
	{/if}
{/if}  
{if $prefs.rss_file_gallery eq 'y'}
	{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
	<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}&amp;ver=PODCAST">
	<img src='img/rss_podcast_80_15.png' border='0' alt="{tr}RSS feed{/tr}" title="{tr}RSS feed{/tr}"  align='right' /></a>
	{else}
	<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}">
	{icon _id='feed' alt="{tr}RSS feed{/tr}" title="{tr}RSS feed{/tr}" align='right'}</a>
	{/if}
{/if}
{if $tiki_p_list_file_galleries eq 'y' or (!isset($tiki_p_list_file_galleries) and $tiki_p_view_file_gallery eq 'y')}<a href="tiki-file_galleries.php{if $filegals_manager eq 'y'}?filegals_manager{/if}" class="linkbut" title="{tr}List Galleries{/tr}">{tr}List Galleries{/tr}</a>{/if}

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user}
  <a href="tiki-file_galleries.php?edit_mode=1&amp;galleryId={$galleryId}{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}" class="linkbut" title="{tr}Edit Gallery{/tr}">{tr}Edit Gallery{/tr}</a>
{/if}

{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user or $gal_info.public eq 'y'}
  {if $tiki_p_upload_files eq 'y'}
    <a href="tiki-upload_file.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}" class="linkbut">{tr}Upload File{/tr}</a>
  {/if}
  {if $prefs.feature_file_galleries_batch eq "y" and $tiki_p_batch_upload_file_dir eq 'y'}
    <a href="tiki-batch_upload_files.php?galleryId={$galleryId}{if $filegals_manager eq 'y'}&amp;filegals_manager{/if}" class="linkbut">{tr}Directory batch{/tr}</a>
  {/if}
{/if}

</div>

{if $filegals_manager eq 'y'}
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>
<div class="rbox-data" name="tip">{tr}Be carefull to set the right permissions on the files you link to{/tr}.</div>
</div>
{/if}


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


{include file='find.tpl' find_show_languages='n' find_show_categories='n'}

<div class="fgal_top_bar" style="height:16px; vertical-align:middle">

{if $prefs.javascript_enabled eq 'y'}

	<div id="fgalexplorer_close" style="float:left; vertical-align:middle; display:{if isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) and $smarty.session.tiki_cookie_jar.show_fgalexplorer eq 'y'}none{else}inline{/if};"><a href="#" onclick="flip('fgalexplorer','');hide('fgalexplorer_close',false);show('fgalexplorer_open',false);return false;">{icon _id='application_side_tree' alt='{tr}Show Tree{/tr}'}</a></div>

	<div id="fgalexplorer_open" style="float:left; vertical-align:middle; display:{if ! isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) or $smarty.session.tiki_cookie_jar.show_fgalexplorer neq 'y'}none{else}inline{/if};"><a href="#" onclick="flip('fgalexplorer','');hide('fgalexplorer_open',false);show('fgalexplorer_close',false);return false;">{icon _id='application_side_contract' alt='{tr}Hide Tree{/tr}'}</a></div>

{else}

	<div style="float:left; vertical-align:middle">
	{if isset($smarty.request.show_fgalexplorer) and $smarty.request.show_fgalexplorer eq 'y'}
		{self_link _icon='application_side_contract' show_fgalexplorer='n'}{tr}Hide Tree{/tr}{/self_link}
	{else}
		{self_link _icon='application_side_tree' show_fgalexplorer='y'}{tr}Show Tree{/tr}{/self_link}
	{/if}
	</div>

{/if}

	<div class="gallerypath" style="vertical-align:middle">&nbsp;&nbsp;{$gallery_path}</div>
</div>

<table border="0" cellpadding="3" cellspacing="3" width="100%" style="clear: both">
	<tr>
		{if isset($tree) and count($tree) gt 0 && $tiki_p_list_file_galleries != 'n'}
		<td width="25%" class="fgalexplorer" id="fgalexplorer" {if ( ! isset($smarty.session.tiki_cookie_jar.show_fgalexplorer) or $smarty.session.tiki_cookie_jar.show_fgalexplorer neq 'y') and ( ! isset($smarty.request.show_fgalexplorer) or $smarty.request.show_fgalexplorer neq 'y' ) }style="display:none"{/if}>
			<div style="overflow-x:auto; overflow-y:hidden">
			{include file='file_galleries.tpl'}
			</div>
		</td>
		<td width="75%" class="fgallisting">
		{else}
		<td width="100%" class="fgallisting">
		{/if}
			<div style="padding:1px; overflow-x:auto; overflow-y:hidden;">
			{include file='list_file_gallery.tpl'}
			</div>
		</td>
	</tr>
</table>

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
