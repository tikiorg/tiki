{* $Id: tiki-list_file_gallery.tpl 26930 2010-05-05 23:16:15Z nyloth $ *}

<link rel="stylesheet" type="text/css" href="css/file_gallery.css"/>
<!--[if lte IE 7]>
<link rel="stylesheet" type="text/css" href="css/file_gallery_ie6.css"/>
<![endif]-->

{if $edit_mode eq 'y'}
	{include file='edit_file_gallery.tpl'}
{else}

<div id="fg-jquery-upload-dialog"></div>
<div id="fg-jquery-gallery-dialog"></div>

<div class="fg-dialog fg-standalone" id="fg-jquery-dialog">

  {if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
	<a class="fg-watch-icon1" href="tiki-object_watches.php?objectId={$galleryId|escape:"url"}&amp;watch_event=file_gallery_changed&amp;objectType=File+Gallery&amp;objectName={$gal_info.name|escape:"url"}&amp;objectHref={'tiki-list_file_gallery.php?galleryId='|cat:$galleryId|escape:"url"}" class="icon">{icon _id='eye_group' alt='{tr}Group Monitor{/tr}' align='right' hspace="1"}</a>
  {/if}
  
  {if $user and $prefs.feature_user_watches eq 'y'}
    {if $user_watching_file_gallery eq 'n'}
      {self_link galleryName=$name watch_event='file_gallery_changed' watch_object=$galleryId watch_action='add'}{icon class='fg-watch-icon' _id='eye' align='right' alt="{tr}Monitor this Gallery{/tr}" hspace="1"}{/self_link}
    {else}
      {self_link galleryName=$name watch_event='file_gallery_changed' watch_object=$galleryId watch_action='remove'}{icon class='fg-watch-icon' _id='no_eye' align='right' alt="{tr}Stop Monitoring this Gallery{/tr}" hspace="1"}{/self_link}
    {/if}
  {/if}
  {if $view eq 'browse'}
    {if $show_details eq 'y'}
      <a class="fgalaction" href="tiki-list_file_gallery.php?galleryId={$galleryId}&view={$view}&filegals_manager={$filegals_manager}&show_details=n">{icon _id='no_information' class='fg-info-icon' align='right'}</a>
    {else}
      <a class="fgalaction" href="tiki-list_file_gallery.php?galleryId={$galleryId}&view={$view}&filegals_manager={$filegals_manager}&show_details=y">{icon _id='information' class='fg-info-icon' align='right'}</a>
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
		<div class="fg-pane">{tr}Galleries{/tr}</div>
		<div class="fg-toolbar">
			<div class="fg-toolbar-left">
				<a class="fg-toolbar-icon fgalaction" href="tiki-list_file_gallery.php?filegals_manager={$filegals_manager}&view={$view}"><img src="images/file_gallery/icon-home.gif" border="0" alt="{tr}Home{/tr}" title="{tr}Home{/tr}"/></a>
				{if $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y'}
					<a class="fg-toolbar-icon" onclick="FileGallery.editGallery('tiki-list_file_gallery.php?edit_mode=1&parentId={$galleryId}&galleryId={$galleryId}&filegals_manager={$filegals_manager}')"><img src="images/file_gallery/icon-edit-folder.gif" border="0" alt="{tr}Edit gallery{/tr}" title="{tr}Edit gallery{/tr}"/></a>
					<!--a class="fg-toolbar-icon"><img src="images/file_gallery/icon-delete-folder.gif" border="0"/></a-->
					<a class="fg-toolbar-icon" onclick="FileGallery.createGallery('tiki-list_file_gallery.php?edit_mode=1&parentId={$galleryId}&galleryId=0&filegals_manager={$filegals_manager}')"><img src="images/file_gallery/icon-create-folder.gif" border="0" alt="{tr}Create gallery{/tr}" title="{tr}Create gallery{/tr}"/></a>
				{/if}
			</div>
			<div class="fg-toolbar-right">
				<a class="fg-toolbar-icon" onclick="FileGallery.tree()"><img src="images/file_gallery/icon-hidegalleries.gif" border="0" alt="{tr}Hide/show galleries{/tr}" title="{tr}Switch galleries pane{/tr}"/></a>
			</div>
		</div>
		<div class="fg-galleries-list">
			{include file='file_galleries.tpl'}
		</div>
	</div>
	<div class="fg-files">
		<div class="fg-pane">{tr}Media{/tr}</div>
		<div class="fg-toolbar">
			<div class="fg-toolbar-left">
				<h2>{$name}</h2>
			</div>
			<div class="fg-toolbar-right">
				<input class="fg-toolbar-search-input" id="fg-toolbar-search-input" type="text" onkeypress="FileGallery.search(event,false,'{$view}','{$filegals_manager}')" value="{$find}"/>
				<a class="fg-toolbar-search-submit" onclick="FileGallery.search(null,true,'{$view}')"><img src="images/file_gallery/icon-search.gif" border="0"/></a>
			</div>
		</div>
		<div class="fg-files-tools">
			<div class="fg-files-stats">{$countgalleries} {tr}folders{/tr}, {$countfiles} {tr}files{/tr}</div>
			<div class="fg-files-count">
				{tr}Number of displayed rows{/tr}
				<input id="fg-files-count-input" type="text" value="{$maxRecords}" onkeypress="FileGallery.limit(event,this.value,'{$view}','{$galleryId}','{$filegals_manager}')"/>
			</div>
		</div>
		<div class="fg-files-buttons">
			<a class="fg-files-button-mode fgalaction" href="{$altmode}">
				{if $view eq 'browse'}
					{tr}List files{/tr}
				{else}
					{tr}Browse by image{/tr}
				{/if}
			</a>
			<a class="fg-files-button-upload" onclick="FileGallery.upload.show('{$galleryId}','{$filegals_manager}')">{tr}Upload files{/tr}</a>
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
			{include file='list_file_gallery2.tpl'}

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

{/if}
