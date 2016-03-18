{* $Id$ *}
{title help="Image Galleries" admpage="gal"}{tr}Galleries{/tr}{/title}
{if $tiki_p_create_galleries eq 'y'}
	{if $edit_mode ne 'y' or $galleryId ne 0}
		<div class="t_navbar">
			{button href="?edit_mode=1&amp;galleryId=0" class="btn btn-default" _icon_name="create" _text="{tr}Create New Gallery{/tr}"}
			{if $galleryId ne 0}
				{button href="tiki-browse_gallery.php?galleryId=$galleryId" class="btn btn-default" _icon_name="view" _text="{tr}Browse Gallery{/tr}"}
			{/if}
		</div>
	{/if}

	{if $edit_mode eq 'y'}
		{if $galleryId eq 0}
			<h2>{tr}Create a gallery{/tr}</h2>
		{else}
			<h2>{tr}Edit this gallery:{/tr} {$name}</h2>
		{/if}
		{if $category_needed eq 'y'}
			{remarksbox type='Warning' title="{tr}Warning{/tr}"}
				<div class="highlight"><em class='mandatory_note'>{tr}A category is mandatory{/tr}</em></div>
			{/remarksbox}
		{/if}

		<div{* style="text-align: center"*}>
			{if $individual eq 'y'}
				{permission_link mode=link type="image gallery" permType="image galleries" id=$galleryId title=$name label="{tr}There are individual permissions set for this gallery{/tr}"}
			{/if}
			<form action="tiki-galleries.php" method="post" id="gal-edit-form" class="form-horizontal">
				<input type="hidden" name="galleryId" value="{$galleryId|escape}">
				<table class="formcolor">
					<tr><td>{tr}Name:{/tr}</td><td><input type="text" name="name" value="{$name|escape}"></td></tr>
					<tr><td>{tr}Description:{/tr}</td><td><textarea rows="20" cols="80" name="description" id="gal-desc">{$description|escape}</textarea></td></tr>
					{if $tiki_p_admin_galleries eq 'y'}
						<tr><td>{tr}Gallery is visible to non-admin users?{/tr}</td><td><input type="checkbox" name="visible" {if $visible eq 'y'}checked="checked"{/if}></td></tr>
						{* If a user can create a gallery, but doesn't have tiki_p_admin_galleries the new gallery needs to be visible. *}
					{else}
						<input type="hidden" name="visible" value="on">
					{/if}
					{if $prefs.preset_galleries_info ne 'y'}
						<tr>
							<td>{tr}Max Rows per page:{/tr}</td>
							<td>
								<input type="text" name="maxRows"{if !empty($maxRows)} value="{$maxRows|escape}"{/if}>
								<i>{tr}Default:{/tr} {if !empty($prefs.maxRowsGalleries)}{$prefs.maxRowsGalleries}{else}10{/if}</i>
							</td>
						</tr>
						<tr>
							<td>{tr}Images per row:{/tr}</td>
							<td>
								<input type="text" name="rowImages"{if !empty($rowImages)} value="{$rowImages|escape}"{/if}>
								<i>{tr}Default:{/tr} {if !empty($prefs.rowImagesGalleries)}{$prefs.rowImagesGalleries}{else}6{/if}</i>
							</td>
						</tr>
						<tr>
							<td>{tr}Thumbnails size X:{/tr}</td>
							<td>
								<input type="text" name="thumbSizeX"{if !empty($thumbSizeX)} value="{$thumbSizeX|escape}"{/if}>
								<i>{tr}Default:{/tr} {if !empty($prefs.thumbSizeXGalleries)}{$prefs.thumbSizeXGalleries}{else}80{/if}</i>
							</td>
						</tr>
						<tr>
							<td>{tr}Thumbnails size Y:{/tr}</td>
							<td>
								<input type="text" name="thumbSizeY"{if !empty($thumbSizeY)} value="{$thumbSizeY|escape}"{/if}>
								<i>{tr}Default:{/tr} {if !empty($prefs.thumbSizeYGalleries)}{$prefs.thumbSizeYGalleries}{else}80{/if}</i>
							</td>
						</tr>
					{/if}
					<tr>
						<td>{tr}Default sort order:{/tr}</td>
						<td>
							<select name="sortorder">
								{foreach from=$options_sortorder key=key item=item}
									<option value="{$item}" {if $sortorder == $item} selected="selected"{/if}>{$key}</option>
								{/foreach}
							</select>
							<input type="radio" name="sortdirection" value="desc" {if $sortdirection == 'desc'}checked="checked"{/if}>{tr}descending{/tr}
							<input type="radio" name="sortdirection" value="asc" {if $sortdirection == 'asc'}checked="checked"{/if}>{tr}ascending{/tr}
						</td>
					</tr>
					<tr>
						<td>{tr}Fields to show during browsing the gallery:{/tr}</td>
						<td>
							<input type="checkbox" name="showname" value="y" {if $showname=='y'}checked="checked"{/if}>{tr}Name{/tr}<br>
							<input type="checkbox" name="showimageid" value="y" {if $showimageid=='y'}checked="checked"{/if}>{tr}Image ID{/tr}<br>
							<input type="checkbox" name="showdescription" value="y" {if $showdescription=='y'}checked="checked"{/if}>{tr}Description{/tr}<br>
							<input type="checkbox" name="showcreated" value="y" {if $showcreated=='y'}checked="checked"{/if}>{tr}Creation Date{/tr}<br>
							<input type="checkbox" name="showuser" value="y" {if $showuser=='y'}checked="checked"{/if}>{tr}User{/tr}<br>
							<input type="checkbox" name="showhits" value="y" {if $showhits=='y'}checked="checked"{/if}>{tr}Hits{/tr}<br>
							<input type="checkbox" name="showxysize" value="y" {if $showxysize=='y'}checked="checked"{/if}>{tr}XY-Size{/tr}<br>
							<input type="checkbox" name="showfilesize" value="y" {if $showfilesize=='y'}checked="checked"{/if}>{tr}Filesize{/tr}<br>
							<input type="checkbox" name="showfilename" value="y" {if $showfilename=='y'}checked="checked"{/if}>{tr}Filename{/tr}<br>
							<input type="checkbox" name="showcategories" value="y" {if $showcategories=='y'}checked="checked"{/if}>{tr}Categories{/tr}<br>
						</td>
					</tr>
					<tr>
						<td>{tr}Gallery Image:{/tr}</td>
						<td>
							<select name="galleryimage">
								{foreach from=$options_galleryimage key=key item=item}
									<option value="{$item}" {if $galleryimage == $item} selected="selected"{/if}>{$key}</option>
								{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td>{tr}Parent gallery:{/tr}</td>
						<td>
							<select name="parentgallery">
								<option value="-1" {if $parentgallery == -1} selected="selected"{/if}>{tr}none{/tr}</option>
								{foreach from=$galleries_list key=key item=item}
									<option value="{$item.galleryId}" {if $parentgallery == $item.galleryId} selected="selected"{/if}>{$item.name}</option>
								{/foreach}
							</select>
						</td>
					</tr>
					{if $prefs.preset_galleries_info ne 'y'}
						<tr>
							<td>{tr}Available scales:{/tr}</td>
							<td>
								{tr}Global default{/tr} {$prefs.scaleSizeGalleries}x{$prefs.scaleSizeGalleries} ({tr}Bounding box{/tr})
								<input type="radio" name="defaultscale" value="{$prefs.scaleSizeGalleries}" {if $defaultscale==$prefs.scaleSizeGalleries}checked="checked"{/if}>
								{tr}default scale{/tr}<br>
								{section name=scales loop=$scaleinfo}
									{if $scaleinfo[scales].scale ne $prefs.scaleSizeGalleries}
										{tr}Remove:{/tr}<input type="checkbox" name="removescale_{$scaleinfo[scales].scale|escape}">
										{$scaleinfo[scales].scale}x{$scaleinfo[scales].scale} ({tr}Bounding box{/tr})
										<input type="radio" name="defaultscale" value="{$scaleinfo[scales].scale}" {if $defaultscale==$scaleinfo[scales].scale}checked="checked"{/if}>
										{tr}default scale{/tr}<br>
									{/if}
								{sectionelse}
									{tr}No scales available{/tr}
								{/section}<br>
								{tr}Original image is default scale{/tr}<input type="radio" name="defaultscale" value="o" {if $defaultscale=='o'}checked="checked"{/if}>
							</td>
						</tr>
						<tr><td>{tr}Add scaled images with bounding box of square size:{/tr}</td><td><input type="text" name="scaleSize">{tr}pixels{/tr}</td></tr>
					{/if}
					<tr><td>{tr}Owner of the gallery:{/tr}</td><td><input type="text" name="owner" value="{$owner|escape}"></td></tr>
					{include file='categorize.tpl'}
					{include file='freetag.tpl'}
					<tr><td>{tr}Other users can upload images to this gallery:{/tr}</td><td><input type="checkbox" name="public" {if $public eq 'y'}checked="checked"{/if}></td></tr>
					<tr><td>&nbsp;</td><td><input type="submit" class="btn btn-default btn-sm" value="{tr}Save{/tr}" name="edit"></td></tr>
				</table>
			</form>
		</div>
		<br>
	{/if}
{/if}
{if $galleryId > 0}
	{if $edited eq 'y'}
		<div class="wikitext">
			{tr}You can access the gallery using the following URL:{/tr} <a class="gallink" href="{$url}?galleryId={$galleryId}">{$url}?galleryId={$galleryId}</a>
		</div>
	{/if}
{/if}
{if $tiki_p_create_galleries eq 'y' && $galleryId ne 0}
	<div class="t_navbar"><a href="tiki-galleries.php?edit_mode=1&amp;galleryId=0" class="btn btn-default">{tr}Create New Gallery{/tr}</a></div>
{/if}
<h2>{tr}Available Galleries{/tr}</h2>
<div align="center">
	{if $galleries or ($find ne '')}
		{include file='find.tpl'}
		<div class="form-group">
			<form action="tiki-galleries.php" method="get">
				<div class="input-group col-sm-4">
					<select name="filter" class="form-control">
						<option value="">{tr}Choose a filter{/tr}</option>
						<option value="topgal"{if $filter eq 'topgal'} selected="selected"{/if}>{tr}Top{/tr}</option>
						<option value="parentgal"{if $filter eq 'parentgal'} selected="selected"{/if}>{tr}Parent gallery{/tr}</option>
						{*foreach key=fid item=field from=$listfields}
							{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
								<option value="{$fid}"{if $fid eq $filterfield} selected="selected"{/if}>{$field.name|truncate:65:"..."}</option>
							{/if}
						{/foreach*}
					</select>
					<span class="input-group-btn">
					<input type="submit" class="btn btn-default" value="{tr}Filter{/tr}">
					</span>
				</div>
			</form>
		</div>
	{/if}

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

	<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
	<table class="table table-hover table-striped">
		<tr>
			{if $prefs.gal_list_name eq 'y'}
				<th><a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
			{/if}
			{if $prefs.gal_list_parent eq 'y'}
				<th>
					<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'parentgallery_desc'}parentgallery_asc{else}parentgallery_desc{/if}">
						{tr}Parent{/tr}
					</a>
				</th>
			{/if}
			{if $prefs.gal_list_description eq 'y'}
				<th>
					<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">
						{tr}Description{/tr}
					</a>
				</th>
			{/if}
			{if $prefs.gal_list_created eq 'y'}
				<th><a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
			{/if}
			{if $prefs.gal_list_lastmodif eq 'y'}
				<th>
					<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">
						{tr}Last modified{/tr}
					</a>
				</th>
			{/if}
			{if $prefs.gal_list_user eq 'y'}
				<th><a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></th>
			{/if}
			{if $prefs.gal_list_imgs eq 'y'}
				<th style="text-align:right">
					<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'images_desc'}images_asc{else}images_desc{/if}">{tr}Imgs{/tr}</a>
				</th>
			{/if}
			{if $prefs.gal_list_visits eq 'y'}
				<th style="text-align:right">
					<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Visits{/tr}</a>
				</th>
			{/if}
			<th></th>
		</tr>

		{section name=changes loop=$galleries}
			{if ($filter eq 'topgal' and $galleries[changes].parentgallery eq -1) or ($filter eq 'parentgal' and $galleries[changes].parentgal eq 'y') or ($filter eq '')}
				{if $galleries[changes].visible eq 'y' or $tiki_p_admin_galleries eq 'y'}
					<tr>
						{if $prefs.gal_list_name eq 'y'}
							<td><a class="galname" href="{$galleries[changes].galleryId|sefurl:gallery}">{$galleries[changes].name}</a></td>
						{/if}
						{if $prefs.gal_list_parent eq 'y'}
							<td>
							{if $galleries[changes].parentgallery ne -1}
								<a class="galname" href="{$galleries[changes].parentgallery|sefurl:gallery}">{$galleries[changes].parentgalleryName}</a>
							{/if}
							{if $galleries[changes].parentgal eq 'y'}<i>{tr}Parent{/tr}</i>{/if}
							</td>
						{/if}
						{if $prefs.gal_list_description eq 'y'}
							<td>{$galleries[changes].description}</td>
						{/if}
						{if $prefs.gal_list_created eq 'y'}
							<td>{$galleries[changes].created|tiki_short_datetime}</td>
						{/if}
						{if $prefs.gal_list_lastmodif eq 'y'}
							<td>{$galleries[changes].lastModif|tiki_short_datetime}</td>
						{/if}
						{if $prefs.gal_list_user eq 'y'}
							<td>{$galleries[changes].user|userlink}</td>
						{/if}
						{if $prefs.gal_list_imgs eq 'y'}
							<td style="text-align:right"><span class="badge">{$galleries[changes].images}</span></td>
						{/if}
						{if $prefs.gal_list_visits eq 'y'}
							<td style="text-align:right;"><span class="badge">{$galleries[changes].hits}</span></td>
						{/if}
						<td nowrap="nowrap">
							{capture name=gallery_actions}
								{strip}
									{if $tiki_p_admin eq 'y' or $galleries[changes].perms.tiki_p_view_image_gallery eq 'y'}
										{$libeg}<a href="tiki-list_gallery.php?galleryId={$galleries[changes].galleryId}">
											{icon name='list' _menu_text='y' _menu_icon='y' alt="{tr}List{/tr}"}
										</a>{$liend}
									{/if}
									{if ($tiki_p_admin eq 'y') or ($galleries[changes].perms.tiki_p_assign_perm_image_gallery eq 'y' )}
										{$libeg}{permission_link mode=text type="image gallery" permType="image galleries" id=$galleries[changes].galleryId title=$galleries[changes].name}{$liend}
									{/if}
									{if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
										{if $tiki_p_admin eq 'y' or $galleries[changes].perms.tiki_p_create_galleries eq 'y'}
											{$libeg}<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edit_mode=1&amp;galleryId={$galleries[changes].galleryId}">
												{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
											</a>{$liend}
										{/if}
									{/if}
									{if $galleries[changes].perms.tiki_p_upload_images eq 'y'}
										{if $tiki_p_admin eq 'y' or $galleries[changes].perms.tiki_p_upload_images eq 'y'}
											{if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user) or $galleries[changes].public eq 'y'}
												{$libeg}<a href="tiki-upload_image.php?galleryId={$galleries[changes].galleryId}">
													{icon name='export' _menu_text='y' _menu_icon='y' alt="{tr}Upload{/tr}"}
												</a>{$liend}
												{if ($galleries[changes].geographic eq 'y')}
													{$libeg}<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;make_map=1&amp;galleryId={$galleries[changes].galleryId}">
														{icon name='wrench' alt="{tr}Make map{/tr}" _menu_text='y' _menu_icon='y' }
													</a>{$liend}
												{/if}
											{/if}
										{/if}
									{/if}
									{if $tiki_p_admin_galleries eq 'y' or ($user and $galleries[changes].user eq $user)}
										{if ($tiki_p_admin eq 'y') or ($galleries[changes].perms.has_special_perms eq 'n') or ($galleries[changes].perms.tiki_p_create_galleries eq 'y' )}
											{$libeg}<a href="tiki-galleries.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;removegal={$galleries[changes].galleryId}">
												{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
											</a>{$liend}
										{/if}
									{/if}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.gallery_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.gallery_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{/if}
			{/if}
		{sectionelse}
			{norecords _colspan=9}
		{/section}
	</table>
	</div>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
</div>
