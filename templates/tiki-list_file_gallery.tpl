{* $Id$ *}
{title help="File Galleries" admpage="fgal"}
	{if $edit_mode eq 'y' and $galleryId eq 0}
		{tr}Create a File Gallery{/tr}
	{else}
		{if $edit_mode eq 'y'}
			{tr}Edit Gallery:{/tr}
		{/if}
		{$name}
	{/if}
{/title}
{if $prefs.javascript_enabled != 'y'}
	{$js = 'n'}
{else}
	{$js = 'y'}
{/if}
<div class="t_navbar margin-bottom-md">
	<div class="btn-group pull-right">
		{if $js == 'n'}<ul class="cssmenu_horiz"><li>{/if}
		<a class="btn btn-link" data-toggle="dropdown" data-hover="dropdown" href="#">
			{icon name='menu-extra'}
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			{if $edit_mode neq 'y' and $dup_mode neq 'y'}
				<li class="divider"></li>
				<li class="dropdown-title">
					{tr}Views{/tr}
				</li>
				<li class="divider"></li>
				{if $view neq 'admin' and $tiki_p_admin_file_galleries eq 'y'}
					<li>
						{self_link _icon_name="wrench" _text="{tr}Admin{/tr}" view="admin" galleryId=$galleryId}{/self_link}
					</li>
				{/if}
				{if $view neq 'browse'}
					<li>
						{self_link _icon_name="view" _text="{tr}Browse{/tr}" view="browse" galleryId=$galleryId}{/self_link}
					</li>
				{/if}
				{if $view neq 'finder' and $prefs.fgal_elfinder_feature eq 'y'}
					<li>
						{self_link _icon_name="file-archive-open" _text="{tr}Finder{/tr}" view="finder" galleryId=$galleryId}{/self_link}
					</li>
				{/if}
				{if $view neq 'list'}
					<li>
						{self_link _icon_name="list" _text="{tr}List{/tr}" view="list" galleryId=$galleryId}{/self_link}
					</li>
				{/if}
				{if $view neq 'page' and $filescount gt 0}
					<li>
						{self_link _icon_name="textfile" _text="{tr}Page{/tr}" view="page" galleryId=$galleryId}{/self_link}
					</li>
				{/if}
			{/if}
			<li class="divider"></li>
			<li class="dropdown-title">
				{tr}Gallery actions{/tr}
			</li>
			<li class="divider"></li>
			{if $edit_mode neq 'y' or $dup_mode neq 'y'}
				{if $tiki_p_create_file_galleries eq 'y' or (not empty($user) and $user eq $gal_info.user and $gal_info.type eq 'user' and $tiki_p_userfiles eq 'y')}
					<li>
						<a href="tiki-list_file_gallery.php?edit_mode=1&galleryId={$galleryId}">{icon name="edit"} {tr}Edit{/tr}</a>
					</li>
				{/if}
			{/if}
			{if $tiki_p_create_file_galleries eq 'y' and $dup_mode ne 'y' and $gal_info.type neq 'user'}
				<li>
					<a href="tiki-list_file_gallery.php?dup_mode=1&galleryId={$galleryId}">{icon name="copy"} {tr}Duplicate{/tr}</a>
				</li>
			{/if}
			{if $tiki_p_assign_perm_file_gallery eq 'y'}
				<li>
					{permission_link mode=text type="file gallery" permType="file galleries" id=$galleryId}
				</li>
			{/if}
			{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
				<li>
					<a href="tiki-object_watches.php?objectId={$galleryId|escape:"url"}&amp;watch_event=file_gallery_changed&amp;objectType=File+Gallery&amp;objectName={$gal_info.name|escape:"url"}&amp;objectHref={'tiki-list_file_gallery.php?galleryId='|cat:$galleryId|escape:"url"}" class="icon">
						{icon name='watch-group'} {tr}Group monitor{/tr}
					</a>
				</li>
			{/if}
			{if $user and $prefs.feature_user_watches eq 'y'}
				<li>
					{if !isset($user_watching_file_gallery) or $user_watching_file_gallery eq 'n'}
						<a href="{query _type='relative' galleryName=$name watch_event='file_gallery_changed' watch_object=$galleryId watch_action='add'}" title="{tr}Monitor this gallery{/tr}">
							{icon name='watch'} {tr}Monitor{/tr}
						</a>
					{else}
						<a href="{query _type='relative' galleryName=$name watch_event='file_gallery_changed' watch_object=$galleryId watch_action='remove'}" title="{tr}Stop monitoring this gallery{/tr}">
							{icon name='stop-watching'} {tr}Stop monitoring{/tr}
						</a>
					{/if}
				</li>
			{/if}
			{if $prefs.feed_file_gallery eq 'y'}
				<li>
					{if $gal_info.type eq "podcast" or $gal_info.type eq "vidcast"}
						<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}&amp;ver=PODCAST">
							{icon name='rss'} {tr}RSS feed{/tr}
						</a>
					{else}
						<a href="tiki-file_gallery_rss.php?galleryId={$galleryId}">
							{icon name='rss'} {tr}RSS feed{/tr}
						</a>
					{/if}
				</li>
			{/if}
			{if $view eq 'browse'}
				<li>
					{if $show_details eq 'y'}
						<a href="{query _type='relative' show_details='n'}" title="{tr}Hide file information from list view{/tr}">
							{icon name='ban' align='right' alt="{tr}Hide file information from list view{/tr}"} {tr}Hide list view information{/tr}
						</a>
					{else}
						<a href="{query _type='relative' show_details='y'}" title="{tr}Show file information from list view{/tr}">
							{icon name='view' align='right' alt="{tr}Show file information from list view{/tr}"} {tr}Show list view information{/tr}
						</a>
					{/if}
				</li>
			{/if}
		</ul>
		{if $js == 'n'}</li></ul>{/if}
	</div>
	{if $galleryId gt 0}
	{* main navigation buttons under the page title *}
	{*	{if $treeRootId eq $prefs.fgal_root_id && ( $tiki_p_list_file_galleries eq 'y'
			or (!isset($tiki_p_list_file_galleries) and $tiki_p_view_file_gallery eq 'y') )}
			{button _icon_name="list" _text="{tr}List{/tr}" href="?"}
		{/if} *}
		{if $tiki_p_create_file_galleries eq 'y' and $edit_mode ne 'y'}
			{button _keepall='y' _icon_name="create" _type="link" _text="{tr}Create{/tr}" edit_mode=1 parentId=$galleryId cookietab=1}
		{/if}
		{if $tiki_p_admin_file_galleries eq 'y' or (not empty($user) and $user eq $gal_info.user and $gal_info.type eq 'user' and $tiki_p_userfiles eq 'y')}
			{if $edit_mode eq 'y' or $dup_mode eq 'y'}
				{button _keepall='y'  _icon_name="view" _text="{tr}Browse{/tr}" galleryId=$galleryId}
			{/if}
		{/if}
		{if $tiki_p_admin_file_galleries eq 'y' or $user eq $gal_info.user or $gal_info.public eq 'y'}
			{if $tiki_p_upload_files eq 'y'}
				{button _keepall='y' _icon_name="export"  _type="link" _text="{tr}Upload{/tr}" href="tiki-upload_file.php" galleryId=$galleryId}
			{/if}
			{if $tiki_p_upload_files eq 'y' and $prefs.feature_draw eq 'y'}
				{button _keepall='y' _icon_name="post"  _type="link" _text="{tr}Draw{/tr}" href="tiki-edit_draw.php" galleryId=$galleryId}
			{/if}
			{if $prefs.feature_file_galleries_batch eq "y" and $tiki_p_batch_upload_file_dir eq 'y'}
				{button _keepall='y' _icon_name="file-archive" _text="{tr}Batch{/tr}" href="tiki-batch_upload_files.php" galleryId=$galleryId}
			{/if}
		{/if}
	{else}
		{if $treeRootId eq $prefs.fgal_root_id && ( $edit_mode eq 'y' or $dup_mode eq 'y')}
			{button _icon_name="list" _text="{tr}List{/tr}" href='?'}
		{/if}
		{if $tiki_p_create_file_galleries eq 'y' and $edit_mode ne 'y'}
			{button _icon_name="create" _keepall='y' _text="{tr}Create{/tr}" edit_mode="1" parentId="-1" galleryId="0"}
		{/if}
		{if $tiki_p_upload_files eq 'y'}
			{button _icon_name="export" _text="{tr}Upload{/tr}" href="tiki-upload_file.php"}
		{/if}
	{/if}
	{if $edit_mode neq 'y' and $prefs.fgal_show_slideshow eq 'y' and $gal_info.show_slideshow eq 'y'}
		{button _icon_name="chart" _text="{tr}SlideShow{/tr}" href="#" _onclick="javascript:window.open('tiki-list_file_gallery.php?galleryId=$galleryId&amp;slideshow','','menubar=no,width=600,height=500,resizable=yes');return false;"}
	{/if}
</div>

{if $edit_mode neq 'y' and $gal_info.description neq ''}
	<div class="description help-block">
		{$gal_info.description|escape|nl2br}
	</div>
{/if}

{if !empty($filegals_manager)}
	{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Be careful to set the right permissions on the files you link to{/tr}.{/remarksbox}
	<label for="keepOpenCbx">{tr}Keep gallery window open{/tr}</label>
	<input type="checkbox" id="keepOpenCbx" checked="checked">
{/if}

{if isset($fileChangedMessage) and $fileChangedMessage neq ''}
	{remarksbox type="note" title="{tr}Note{/tr}"}
		{$fileChangedMessage}
		<form method="post"
				action="{$smarty.server.PHP_SELF}{if !empty($filegals_manager) and $filegals_manager neq ''}?filegals_manager={$filegals_manager|escape}{/if}"
				class="form-inline">
			<input type="hidden" name="galleryId" value="{$galleryId|escape}">
			<input type="hidden" name="fileId" value="{$fileId|escape}">
			<div class="form-group">
				<label>
					{tr}Your comment{/tr} ({tr}optional{/tr}):
					<input type="text" name="comment" size="30" class="form-input">
				</label>
				<button type="submit" class="btn btn-default btn-sm">{icon name='ok'} {tr}Save{/tr}</button>
			</div>
		</form>
	{/remarksbox}
{/if}

{if $user and $prefs.feature_user_watches eq 'y'}
	<div class="categbar" align="right">
		{if isset($category_watched) && $category_watched eq 'y'}
			{tr}Watched by categories:{/tr}
			{section name=i loop=$watching_categories}
				{button _keepall='y' _text=$watching_categories[i].name|escape href="tiki-browse_categories.php" parentId=$watching_categories[i].categId}
			{/section}
		{/if}
	</div>
{/if}

{if !empty($fgal_diff)}
	{remarksbox type="note" title="{tr}Modifications{/tr}"}
		{foreach from=$fgal_diff item=fgp_prop key=fgp_name name=change}
			{tr}Property <b>{$fgp_name}</b> Changed{/tr}
		{/foreach}
	{/remarksbox}
{/if}

{if $edit_mode eq 'y'}
	<br>{include file='edit_file_gallery.tpl'}
{elseif $dup_mode eq 'y'}
	{include file='duplicate_file_gallery.tpl'}
{else}
	{if $prefs.fgal_elfinder_feature neq 'y' or $view neq 'finder'}
		<div class="row">
		{if $prefs.fgal_search eq 'y' and $view neq 'page'}
			<div class="col-sm-6">
				{include file='find.tpl' find_show_num_rows = 'y' find_show_categories_multi='y' find_durations=$find_durations find_show_sub='y' find_other="{tr}Gallery of this fileId{/tr}" find_in="<ul><li>{tr}Name{/tr}</li><li>{tr}Filename{/tr}</li><li>{tr}Description{/tr}</li></ul>"}
			</div>
		{/if}
		{if $prefs.fgal_search_in_content eq 'y' and $galleryId > 0}
			{if $view neq 'page'}
				<div class="col-sm-6">
					<form id="search-form" class="form" role="form" method="get" action="tiki-search{if $prefs.feature_forum_local_tiki_search eq 'y'}index{else}results{/if}.php">
						<input type="hidden" name="where" value="files">
						<input type="hidden" name="galleryId" value="{$galleryId}">
						<label for="highlight" class="find_content sr-only">{tr}Search in content{/tr}</label>
						<div class="input-group">
							<input name="highlight" size="30" type="text" placeholder="{tr}Search in content{/tr}..." class="form-control">
							<div class="input-group-btn">
								<input type="submit" class="wikiaction btn btn-default" name="search" value="{tr}Go{/tr}">
							</div>
						</div>
					</form>
				</div>
			{/if}
		{/if}
		</div>
	{/if}

	{if $view eq 'page'}
		<div class="pageview">
			<form id="size-form" class="form form-inline" role="form" action="tiki-list_file_gallery.php">
				<input type="hidden" name="view" value="page">
				<input type="hidden" name="galleryId" value="{$galleryId}">
				<input type="hidden" name="maxRecords" value=1>
				<input type="hidden" name="offset" value="{$offset}">
				<label for="maxWidth">
					{tr}Maximum width{/tr}&nbsp;<input id="maxWidth" class="form-control" type="text" name="maxWidth" value="{$maxWidth}">
				</label>
				<input type="submit" class="wikiaction btn btn-default" name="setSize" value="{tr}Submit{/tr}">
			</form>
		</div><br>
		{pagination_links cant=$cant step=$maxRecords offset=$offset}
			tiki-list_file_gallery.php?galleryId={$galleryId}&maxWidth={$maxWidth}&maxRecords={$maxRecords}&view={$view}
		{/pagination_links}
		<br>
	{/if}
	{if $prefs.fgal_quota_show neq 'n' and $gal_info.quota}
		<div style="float:right">
			{capture name='use'}
				{math equation="round((100*x)/(1024*1024*y),2)" x=$gal_info.usedSize y=$gal_info.quota}
			{/capture}
			{if $prefs.fgal_quota_show neq 'y'}
				<b>{$smarty.capture.use} %</b> {tr}space use on{/tr} <b>{$gal_info.quota} Mo</b>
				<br>
			{/if}

			{if $prefs.fgal_quota_show neq 'text_only'}
				{quotabar length='100' value=$smarty.capture.use}
			{/if}
		</div>
	{/if}
	{if $prefs.fgal_elfinder_feature eq 'y' and $view eq 'finder'}<br>
		<div class="elFinderDialog" style="height: 100%"></div>
		{jq}

var elfoptions = initElFinder({
		defaultGalleryId: {{$galleryId}},
		deepGallerySearch:1,
		getFileCallback: function(file,elfinder) { window.handleFinderFile(file,elfinder); },
		height: 600
	});

var elFinderInstnce = $(".elFinderDialog").elfinder(elfoptions).elfinder('instance');
// when changing folders update the buttons in the navebar above
elFinderInstnce.bind("open", function (data) {
	$.getJSON($.service('file_finder', 'finder'), {
		cmd: "tikiFileFromHash",
		hash: data.data.cwd.hash
	}).done(function (data) {
		var href = '';
		$(".t_navbar a").each(function () {
			href = $(this).attr("href");
			if (href) {	// avoid chosen select replacements
				href = href.replace(/(galleryId|objectId|parentId|watch_object)=\d+/, '$1=' + data.galleryId);
				$(this).attr("href", href);
			}
		});
	});
});

window.handleFinderFile = function (file, elfinder) {
		var hash = "";
		if (typeof file === "string") {
			var m = file.match(/target=([^&]*)/);
			if (!m || m.length < 2) {
				return false;	// error?
			}
			hash = m[1];
		} else {
			hash = file.hash;
		}
	$.ajax({
		type: 'GET',
		url: $.service('file_finder', 'finder'),
		dataType: 'json',
		data: {
			cmd: "tikiFileFromHash",
			{{if !empty($filegals_manager)}}
				filegals_manager: "{{$filegals_manager}}",
			{{/if}}
			hash: hash
		},
		success: function (data) {
			{{if !empty($filegals_manager)}}
				window.opener.insertAt('{{$filegals_manager}}', data.wiki_syntax);
				checkClose();
			{{/if}}
		}
	});
};
		{/jq}
	{else}
		{include file='list_file_gallery.tpl'}
	{/if}

	{if $galleryId gt 0
		&& $prefs.feature_file_galleries_comments == 'y'
		&& ($tiki_p_read_comments == 'y'
		|| $tiki_p_post_comments == 'y'
		|| $tiki_p_edit_comments == 'y')}

		<div id="page-bar">
			<a id="comment-toggle" href="{service controller=comment action=list type="file gallery" objectId=$galleryId}#comment-container" class="btn btn-default btn-sm">
				{icon name="comments"} {tr}Comments{/tr}
			</a>
			{jq}
				$('#comment-toggle').comment_toggle();
			{/jq}
		</div>

		<div id="comment-container"></div>
	{/if}
{/if}

{if $galleryId>0}
	{if $edited eq 'y'}
		{remarksbox type="tip" title="{tr}Information{/tr}"}
			{tr}You can access the file gallery using the following URL:{/tr} <a class="fgallink alert-link" href="{$url}?galleryId={$galleryId}">{$url}?galleryId={$galleryId}</a>
		{/remarksbox}
	{/if}
{/if}

