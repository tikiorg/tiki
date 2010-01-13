{* $Id: tiki-list_file_gallery.tpl 22566 2009-10-22 10:08:49Z nyloth $ *}

<!-- {popup_init src="lib/overlib.js"} -->

<link rel="stylesheet" type="text/css" href="/css/file_gallery.css"/>

{if $filegals_manager eq ''}
	<script> FileGallery.dialogmode = false;; </script>
	<div id="fg-jquery-upload-dialog"></div>
{/if}
<div class="fg-dialog"{if $filegals_manager eq ''} id="fg-jquery-dialog"{/if}>
  <h1>
  {strip}
    {if $edit_mode eq 'y'}
      {if $galleryId eq 0}
        {tr}Create a File Gallery{/tr}
      {else}
        {tr}Edit Gallery:{/tr}
        {if $galleryId eq $prefs.fgal_root_id}
		  {tr}File Galleries{/tr}
	    {else}
		  {$name}
	  {/if}
    {/if}
    {else}
      {if $galleryId eq $prefs.fgal_root_id}
        {tr}File Galleries{/tr}
      {else}
        {tr}Gallery:{/tr} {$name|escape}
      {/if}
    {/if}
  {/strip}
  </h1>
  <a class="fg-quick-insert-button" onclick="FileGallery.upload.extra('{$galleryId}','{$filegals_manager}')">Quick upload and insert button</a>
  <a class="fg-settings-icon"><img src="images/file_gallery/icon-tools.gif" border="0"/></a>
  <a class="fg-tip-icon" target="wikihelp" href="http://doc.tikiwiki.org/File+Galleries" onmouseover="$('.fg-tip').show()" onmouseout="$('.fg-tip').hide()"><img src="images/file_gallery/icon-help.gif" border="0"/></a>
  {if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
	<a class="fg-watch-icon1" href="tiki-object_watches.php?objectId={$galleryId|escape:"url"}&amp;watch_event=file_gallery_changed&amp;objectType=File+Gallery&amp;objectName={$gal_info.name|escape:"url"}&amp;objectHref={'tiki-list_file_gallery.php?galleryId='|cat:$galleryId|escape:"url"}" class="icon">{icon _id='eye_group' alt='{tr}Group Monitor{/tr}' align='right' hspace="1"}</a>
  {/if}
  <div class="fg-tip">
    <h4>Tip</h4>
    <p>{tr}Be careful to set the right permissions on the files you link to{/tr}.</p>
  </div>
  
  {if $user and $prefs.feature_user_watches eq 'y'}
    {if $user_watching_file_gallery eq 'n'}
      {self_link galleryName=$name watch_event='file_gallery_changed' watch_object=$galleryId watch_action='add'}{icon class='fg-watch-icon' _id='eye' align='right' alt="{tr}Monitor this Gallery{/tr}" hspace="1"}{/self_link}
    {else}
      {self_link galleryName=$name watch_event='file_gallery_changed' watch_object=$galleryId watch_action='remove'}{icon class='fg-watch-icon' _id='no_eye' align='right' alt="{tr}Stop Monitoring this Gallery{/tr}" hspace="1"}{/self_link}
    {/if}
  {/if}
  {if $view eq 'browse'}
    {if $show_details eq 'y'}
      {self_link show_details='n'}{icon _id='no_information' class='fg-info-icon' align='right'}{/self_link}
    {else}
      {self_link show_details='y'}{icon _id='information' class='fg-info-icon' align='right'}{/self_link}
    {/if}
  {/if}

  {if $prefs.rss_file_gallery eq 'y'}
    {if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
      <a class="fg-rss-icon" href="tiki-file_gallery_rss.php?galleryId={$galleryId}&amp;ver=PODCAST"><img src='img/rss_podcast_80_15.png' alt="{tr}RSS feed{/tr}" title="{tr}RSS feed{/tr}"  align='right' /></a>
    {else}
      <a class="fg-rss-icon" href="tiki-file_gallery_rss.php?galleryId={$galleryId}">{icon _id='feed' alt="{tr}RSS feed{/tr}" title="{tr}RSS feed{/tr}" align='right'}</a>
    {/if}
  {/if}

	<div class="fg-galleries">
		<div class="fg-pane">Galleries</div>
		<div class="fg-toolbar">
			<div class="fg-toolbar-left">
				<a class="fg-toolbar-icon" onclick="FileGallery.open('tiki-list_file_gallery.php?filegals_manager={$filegals_manager}&view={$view}')"><img src="images/file_gallery/icon-home.gif" border="0"/></a>
				{if $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y'}
					<a class="fg-toolbar-icon" onclick="FileGallery.open('tiki-list_file_gallery.php?edit_mode=1&parentId={$galleryId}&galleryId={$galleryId}&filegals_manager={$filegals_manager}')"><img src="images/file_gallery/icon-edit-folder.gif" border="0"/></a>
					<!--a class="fg-toolbar-icon"><img src="images/file_gallery/icon-delete-folder.gif" border="0"/></a-->
					<a class="fg-toolbar-icon" onclick="FileGallery.open('tiki-list_file_gallery.php?edit_mode=1&parentId={$galleryId}&galleryId=0&filegals_manager={$filegals_manager}')"><img src="images/file_gallery/icon-create-folder.gif" border="0"/></a>
				{/if}
			</div>
			<div class="fg-toolbar-right">
				<a class="fg-toolbar-icon" onclick="FileGallery.tree()"><img src="images/file_gallery/icon-hidegalleries.gif" border="0"/></a>
			</div>
		</div>
		<div class="fg-galleries-list">
			<h3>File galleries</h3>
			{include file='file_galleries.tpl'}
			<!--a class="fg-gallery fg-gallery-open">Root</a>
			<div class="fg-gallery-kids">
				<a class="fg-gallery">Folder 1</a>
				<a class="fg-gallery fg-gallery-closed fg-gallery-selected">Folder 2</a>
			</div-->
		</div>
	</div>
	<div class="fg-files">
		<div class="fg-pane">Media</div>
		<div class="fg-toolbar">
			<div class="fg-toolbar-left">
				<h2>{$name}</h2>
			</div>
			<div class="fg-toolbar-right">
				<input class="fg-toolbar-search-input" type="text" onkeypress="FileGallery.search(event,false,'{$view}','{$filegals_manager}')" value="{$find}"/>
				<a class="fg-toolbar-search-submit" onclick="FileGallery.search(null,true,'{$view}')"><img src="images/file_gallery/icon-search.gif" border="0"/></a>
			</div>
		</div>
		<div class="fg-files-tools">
			<div class="fg-files-stats">{$countgalleries} folders, {$countfiles} files</div>
			<div class="fg-files-count">
				Number of displayed rows
				<input id="fg-files-count-input" type="text" value="{$maxRecords}" onkeypress="FileGallery.limit(event,this.value,'{$view}','{$galleryId}','{$filegal_manager}')"/>
			</div>
		</div>
		<div class="fg-files-buttons">
			<a class="fg-files-button-mode" onclick="FileGallery.open('{$altmode}')">
				{if $view eq 'browse'}
					List files
				{else}
					Browse by image
				{/if}
			</a>
			<a class="fg-files-button-upload" onclick="FileGallery.upload.show('{$galleryId}','{$filegals_manager}')">Upload files</a>
		</div>
		<div id="fg-files-content">
		{if $edit_mode eq 'y'}
			{include file='edit_file_gallery.tpl'}
		{elseif $dup_mode eq 'y'}
			{include file='duplicate_file_gallery.tpl'}
		{else}
			{if $files or ($find ne '')}
				<!-- {include file='find.tpl' find_show_num_rows = 'y'} -->
			{/if}
			{include file='list_file_gallery.tpl'}

			{if $galleryId gt 0
				&& $prefs.feature_file_galleries_comments == 'y'
				&& (($tiki_p_read_comments  == 'y'
					&& $comments_cant != 0)
					||  $tiki_p_post_comments  == 'y'
					||  $tiki_p_edit_comments  == 'y')}
			    <div id="page-bar">
					  {include file='comments_button.tpl'}
			    </div>
				{include file='comments.tpl'}
			{/if}
		{/if}
		</div>
	</div>
</div>


