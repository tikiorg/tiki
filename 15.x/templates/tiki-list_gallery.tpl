{title help="Image+Galleries"}{tr}Browsing Gallery:{/tr} {$name}{/title}

<div class="galdesc">
	{$description}
</div>

<div class="t_navbar margin-bottom-md">
	{if $system eq 'n'}
		{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
			{button href="tiki-galleries.php?edit_mode=1&galleryId=$galleryId" class="btn btn-default" _icon_name="edit" _text="{tr}Edit Gallery{/tr}"}
			{button href="tiki-list_gallery.php?galleryId=$galleryId&rebuild=$galleryId" class="btn btn-default" _icon_name="cog" _text="{tr}Rebuild Thumbnails{/tr}"}
		{/if}
		{if $tiki_p_upload_images eq 'y'}
			{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner) or $public eq 'y'}
					{button href="tiki-upload_image.php?galleryId=$galleryId" class="btn btn-default" _icon_name="import" _text="{tr}Upload Image{/tr}"}
			{/if}
		{/if}
	{/if}
	{button href="tiki-browse_gallery.php?galleryId=$galleryId" class="btn btn-default" _icon_name="view" _text="{tr}Browse Gallery{/tr}"}
</div>

<h2>{tr}Gallery Images{/tr}</h2>
<div class="text-center">
	<div class="table-responsive">
		<table class="table table-striped normal table-hover">
			<tr>
				<th><a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'imageId_desc'}imageId_asc{else}imageId_desc{/if}">{tr}ID{/tr}</a></th>
				<th><a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
				<th><a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
				<th><a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'hits_desc'}hits_asc{else}hits_desc{/if}">{tr}Hits{/tr}</a></th>
				<th><a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'user_desc'}user_asc{else}user_desc{/if}">{tr}User{/tr}</a></th>
				<th><a href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'filesize_desc'}filesize_asc{else}filesize_desc{/if}">{tr}Filesize{/tr}</a></th>
			</tr>

			{section name=changes loop=$images}
				<tr>
					<td>{$images[changes].imageId}&nbsp;</td>
					<td><a class="imagename" href="tiki-browse_image.php?{if $images[changes].galleryId}galleryId={$images[changes].galleryId}&amp;{/if}imageId={$images[changes].imageId}">{$images[changes].name|truncate:22:"..."}</a>&nbsp;
						{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
							<a class="tips" title=":{tr}Delete{/tr}" href="tiki-list_gallery.php?galleryId={$galleryId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$images[changes].imageId}">
								{icon name='remove'}
							</a>
						{/if}
					</td>
					<td>{$images[changes].created|tiki_short_datetime}&nbsp;</td>
					<td><span class="badge">{$images[changes].hits}</span>&nbsp;</td>
					<td>{$images[changes].user|userlink}&nbsp;</td>
					<td>{$images[changes].filesize|kbsize}&nbsp;</td>
				</tr>
			{sectionelse}
				{norecords _colspan=6}
			{/section}
		</table>
	</div>

	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}

</div>
