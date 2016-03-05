{* $Id$ *}

{title}{tr}Browsing Gallery:{/tr} {$name}{/title}
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
			<li class="dropdown-title">
				{tr}Gallery actions{/tr}
			</li>
			<li class="divider"></li>
			{if $user and $prefs.feature_user_watches eq 'y'}
				<li>
					{if $user_watching_gal eq 'n'}
						<a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;watch_event=image_gallery_changed&amp;watch_object={$galleryId}&amp;watch_action=add">
							{icon name='watch'} {tr}Monitor{/tr}
						</a>
					{else}
						<a href="tiki-browse_gallery.php?galleryId={$galleryId}&amp;watch_event=image_gallery_changed&amp;watch_object={$galleryId}&amp;watch_action=remove">
							{icon name='stop-watching'} {tr}Stop monitoring{/tr}
						</a>
					{/if}
				</li>
			{/if}
			{if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
				<li>
					<a href="tiki-object_watches.php?objectId={$galleryId|escape:"url"}&amp;watch_event=image_gallery_changed&amp;objectType=image+gallery&amp;objectName={$name|escape:"url"}&amp;objectHref={'tiki-browse_gallery.php?galleryId='|cat:$galleryId|escape:"url"}">
						{icon name='group'} {tr}Group Monitor{/tr}
					</a>
				</li>
			{/if}
		</ul>
		{if $js == 'n'}</li></ul>{/if}
	</div>
	{if $tiki_p_list_image_galleries eq 'y'}
		{button href="tiki-galleries.php" class="btn btn-default" _icon_name='list' _text="{tr}List Galleries{/tr}"}
	{/if}
	{if $system eq 'n'}
		{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
			{button href="tiki-galleries.php?edit_mode=1&amp;galleryId=$galleryId" class="btn btn-default" _icon_name='edit' _text="{tr}Edit Gallery{/tr}"}
			{button href="tiki-browse_gallery.php?galleryId=$galleryId&amp;rebuild=$galleryId" class="btn btn-default" _icon_name='cog' _text="{tr}Rebuild Thumbnails{/tr}"}
		{/if}

		{if $tiki_p_upload_images eq 'y'}
			{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
				{button href="tiki-upload_image.php?galleryId=$galleryId" class="btn btn-default" _icon_name='upload' _text="{tr}Upload Image{/tr}"}
			{/if}
		{/if}

		{if $prefs.feature_gal_batch eq "y" and $tiki_p_batch_upload_image_dir eq 'y'}
			{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
				{button href="tiki-batch_upload.php?galleryId=$galleryId" class="btn btn-default" _icon_name='file-archive' _text="{tr}Directory Batch{/tr}"}
			{/if}
		{/if}

		{if $tiki_p_assign_perm_image_gallery eq 'y'}
			{assign var=thisname value=$name|escape:"url"}
			{permission_link mode=text type="image gallery" permType="image galleries" addclass="btn btn-default" id=$galleryId title=$thisname}
		{/if}
	{/if}

	{if $tiki_p_admin_galleries eq 'y'}
		{button href="tiki-list_gallery.php?galleryId=$galleryId" class="btn btn-default" _icon_name='list' _text="{tr}List Gallery{/tr}"}
		{button href="tiki-show_all_images.php?id=$galleryId" class="btn btn-default" _icon_name='image' _text="{tr}All Images{/tr}"}
	{/if}

	{if $prefs.feed_image_gallery eq 'y'}
		{button href="tiki-image_gallery_rss.php?galleryId=$galleryId" class="btn btn-default" _icon_name='rss' _text="{tr}RSS{/tr}"}
	{/if}
</div>

<div class="categbar" align="right">
	{if $user and $prefs.feature_user_watches eq 'y'}
		{if $category_watched eq 'y'}
			{tr}Watched by categories:{/tr}
			{section name=i loop=$watching_categories}
				<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}">{$watching_categories[i].name|escape}</a>&nbsp;
			{/section}
		{/if}
	{/if}
</div>

{if $advice}
	<div class="highlight simplebox">{tr}{$advice}{/tr}</div>
{/if}

{if strlen($description) > 0}
	<div class="description help-block">
		{$description|escape}
	</div>
{/if}

<span class="sorttitle">{tr}Sort Images by{/tr}</span>
[ <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:gallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></span>
| <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:gallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Date{/tr}</a></span>
| <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:gallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></span>
| <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:gallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></span>
| <span class="sortoption"><a class="gallink" href="{$galleryId|sefurl:gallery:with_next}offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Size{/tr}</a></span> ]
{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}

<div class="thumbnails">
	<table class="galtable" cellpadding="0" cellspacing="0">
		<tr>
			{if $num_objects > 0}
				{foreach from=$subgals key=key item=item}
					<td align="center" {if (($key / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
						&nbsp;&nbsp;<br>
						<a href="{$item.galleryId|sefurl:gallery}"><img alt="{tr}subgallery{/tr} {$item.name}" class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1"></a>
						<br>
						<small class="caption">
							{tr}Subgallery:{/tr}
							{if $showname=='y' || $showfilename=='y'}{$item.name}<br>{/if}
							{if $showimageid=='y'}{tr}ID:{/tr} {$item.galleryId}<br>{/if}
							{if $showcategories=='y'}
								{tr}Categories:{/tr}
									<ul>
									{section name=categ loop=$item.categories}
										<li>{$item.categories[categ]}</li>
									{/section}
									</ul><br>
							{/if}
							{if $showdescription=='y'}{$item.description}<br>{/if}
							{if $showcreated=='y'}{tr}Created:{/tr} {$item.created|tiki_short_date}<br>{/if}
							{if $showuser=='y'}{tr}User:{/tr} {$item.user|userlink}<br>{/if}
							{if $showxysize=='y' || $showfilesize=='y'}({$item.images} Images){/if}
							{if $showhits=='y'}[{$item.hits} {if $item.hits == 1}{tr}Hit{/tr}{else}{tr}Hits{/tr}{/if}]<br>{/if}
						</small>
					</td>
					{if $key%$rowImages eq $rowImages2}
						</tr><tr>
					{/if}
				{/foreach}
				{foreach from=$images key=key item=item}
					<td align="center" {if ((($key +$num_subgals) / $rowImages) % 2)}class="oddthumb"{else}class="eventhumb"{/if}>
						&nbsp;&nbsp;<br>
						{if $prefs.feature_shadowbox eq 'y'}
							<a href="show_image.php?id={$item.imageId}&amp;scalesize={$defaultscale}" data-box="lightbox[gallery];type=img" title="{if $item.description neq ''}{$item.description}{elseif $item.name neq ''}{$item.name}{else}{$item.filename}{/if}" {if $prefs.gal_image_mouseover neq 'n'}{popup fullhtml="1" text=$over_info.$key|escape:"javascript"|escape:"html"}{/if} class="linkmenu tips">
								<img class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1">
							</a>
						{else}
							<a href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;imageId={$item.imageId}&amp;scalesize={$defaultscale}" {if $prefs.gal_image_mouseover neq 'n'}{popup fullhtml="1" text=$over_info.$key|escape:"javascript"|escape:"html"}{/if} class="linkmenu tips">
								<img class="athumb" src="show_image.php?id={$item.imageId}&amp;thumb=1">
							</a>
						{/if}
						<br>
						<small class="caption">
							{if $prefs.gal_image_mouseover neq 'only'}
								{if $showname=='y'}{$item.name}<br>{/if}
								{if $showfilename=='y'}{tr}Filename:{/tr} {$item.filename}<br>{/if}
								{if $showimageid=='y'}{tr}ID:{/tr} {$item.imageId}<br>{/if}
								{if $showcategories=='y'}
									{tr}Categories:{/tr}
									<ul class='categories'>
										{section name=categ loop=$item.categories}
											<li>{$item.categories[categ]}</li>
										{/section}
									</ul><br>
								{/if}
								{if $showdescription=='y'}{$item.description}<br>{/if}
								{if $showcreated=='y'}{tr}Created:{/tr} {$item.created|tiki_short_date}<br>{/if}
								{if $showuser=='y'}{tr}User:{/tr} {$item.user|userlink}<br>{/if}
								{if $showxysize=='y'}({$item.xsize}x{$item.ysize}){/if}
								{if $showfilesize=='y'}({$item.filesize} Bytes){/if}
								{if $showhits=='y'}[{$item.hits} {if $item.hits == 1}{tr}Hit{/tr}{else}{tr}Hits{/tr}{/if}]{/if}
							{else}
								{if $showname=='y' and $item.name neq ''}{$item.name}{else}{$item.filename}{/if}
							{/if}
							<br>
							{capture name=image_actions}
								{strip}
									{$libeg}<a {jspopup href="tiki-browse_image.php?galleryId=$galleryId&amp;sort_mode=$sort_mode&amp;imageId="|cat:$item.imageId|cat:"&amp;scalesize=$defaultscale&amp;popup=1"} class="gallink">
										{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}Popup{/tr}"}
									</a>{$liend}
									{$libeg}<a class="gallink tips" href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;imageId={$item.imageId}&amp;scalesize={$defaultscale}" {if $prefs.gal_image_mouseover neq 'n'}{popup fullhtml="1" text=$over_info.$key|escape:"javascript"|escape:"html"}{/if}>
										{icon name='list' _menu_text='y' _menu_icon='y' alt="{tr}Details{/tr}"}
									</a>{$liend}
									{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
										{if $nextx!=0}
											{$libeg}<a class="gallink" href="tiki-browse_image.php?galleryId={$galleryId}&amp;sort_mode={$sort_mode}&amp;imageId={$item.imageId}&amp;scalesize=0">
												{icon name='image' _menu_text='y' _menu_icon='y' alt="{tr}Original size{/tr}"}
											</a>{$liend}
										{/if}
										{if $imagerotate}
											{$libeg}<a class="gallink" href="{$galleryId|sefurl:gallery:with_next}&amp;rotateright={$item.imageId}" title="{tr}rotate right{/tr}">
												{icon name='repeat' _menu_text='y' _menu_icon='y' alt="{tr}Rotate{/tr}"}
											</a>{$liend}
										{/if}
										{$libeg}<a class="gallink" href="tiki-edit_image.php?galleryId={$galleryId}&amp;edit={$item.imageId}">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
										{$libeg}<a class="gallink" href="{$galleryId|sefurl:gallery:with_next}&amp;remove={$item.imageId}">
											{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
										</a>{$liend}
									{/if}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.image_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.image_actions}</ul></li></ul>
							{/if}
							<br>
						</small>
					</td>
					{if ($key + $num_subgals) % $rowImages eq $rowImages2}
						</tr><tr>
					{/if}
				{/foreach}
			{else}
				{norecords _colspan=6}
			{/if}
		</tr>
	</table>
</div>

{pagination_links cant=$cant step=$maxImages offset=$offset}{/pagination_links}

{include file='find.tpl'}

{if $prefs.feature_image_galleries_comments == 'y'
	&& (($tiki_p_read_comments == 'y'
	&& $comments_cant != 0)
	|| $tiki_p_post_comments == 'y'
	|| $tiki_p_edit_comments == 'y')}
	<div id="page-bar" class="btn-group">
		<span class="button btn-default"><a id="comment-toggle" href="{service controller=comment action=list type="image gallery" objectId=$galleryId}#comment-container">{tr}Comments{/tr}</a></span>
		{jq}
			$('#comment-toggle').comment_toggle();
		{/jq}
	</div>
	<div id="comment-container"></div>
{/if}

<div class="table-responsive">
	<table class="table noslideshow">
		<tr>
			<td class="even" colspan="2" style="border:0px; font-size:x-small">
				{tr}You can view this gallery's configured image (first, random, etc.) in your browser using:{/tr}
			</td>
		<tr>
			<td width="6px" style="border:0px">
			</td>
			<td style="border:0px; font-size:x-small">
				<a class="gallink" href="{$base_url}show_image.php?galleryId={$galleryId}">
					{$base_url}show_image.php?galleryId={$galleryId}
				</a>
			</td>
		</tr>
		<tr>
			<td class="even" style="border-bottom:0px; font-size:x-small" colspan="2">
				{tr}You can include the gallery's image in an HTML page using:{/tr}
			</td>
		</tr>
		<tr>
			<td style="border:0px" width="6px"></td>
			<td style="border:0px; font-size:x-small">
				<code>&lt;img src="{$base_url}show_image.php?galleryId={$galleryId}" /&gt;</code>
			</td>
		</tr>
		<tr>
			<td class="even" style="border-bottom:0px; font-size:x-small" colspan="2">
				{tr}You can include the image in a tiki page using:{/tr}
			</td>
		<tr>
			<td width="6px" style="border:0px">
			</td>
			<td style="border:0px; font-size:x-small">
				<code>
					{if $resultscale == $defaultscale or !$resultscale}
						{literal}{{/literal}img src=show_image.php?galleryId={$galleryId} {literal}}{/literal}<br>
					{else}
						{literal}{{/literal}img src={$base_url}show_image.php?galleryId={$galleryId}{literal}}{/literal}<br>
					{/if}
				</code>
			</td>
		</tr>
	</table>
</div>
