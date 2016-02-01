{title}{tr}Edit Image{/tr}{/title}

<div class="t_navbar">
	{button href="tiki-browse_gallery.php" _auto_args='galleryId' class="btn btn-default" _icon_name="previous" _text="{tr}Return to Gallery{/tr}"}
	{button href="tiki-browse_image.php?imageId=$imageId" class="btn btn-default" _icon_name="view" _text="{tr}Browse Images{/tr}"}
</div>

<div align="center">
	{if $show eq 'y'}
		<br>
		<hr>
		<h2>{tr}Edit successful!{/tr}</h2>
		<h3>{tr}The following image was successfully edited:{/tr}</h3>
		<hr>
		<br>
	{/if}
	<img src="show_image.php?id={$imageId}" alt="{tr}Image{/tr}"><br><br>
	<form enctype="multipart/form-data" action="tiki-edit_image.php" method="post" class="form-horizontal">
		<input type="hidden" name="edit" value="{$imageId|escape}">
		<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
		<input type="hidden" name="galleryId" value="{$galleryId|escape}">
		<div class="form-group">
			<label class="col-sm-3 control-label">{tr}Image Name{/tr}</label>
			<div class="col-sm-7 margin-bottom-sm">
		      	<input type="text" name="name" value="{$name|escape}" class="form-control">
		    </div>
	    </div>
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Image Description{/tr}</label>
			<div class="col-sm-7 margin-bottom-sm">
		      	<textarea rows="5" cols="40" name="description" class="form-control">{$description|escape}</textarea>
		    </div>
	    </div>
	    {include file='categorize.tpl'}
	    <div class="form-group">
			<label class="col-sm-3 control-label">{tr}Upload from disk to change the image:{/tr}</label>
			<div class="col-sm-7 margin-bottom-sm">
				{$filename}
		      	<input name="userfile" type="file">
		    </div>
	    </div>
    	<div class="form-group">
			<label class="col-sm-3 control-label"></label>
			<div class="col-sm-7 margin-bottom-sm">
		      	<input type="submit" class="btn btn-default btn-sm" name="editimage" value="{tr}Save{/tr}">
		      	<input type="submit" class="btn btn-default btn-sm" name="editimage_andgonext" value="{tr}Save and Go Next{/tr}">
		      	<a class="link btn btn-default btn-sm" href="tiki-browse_image.php?imageId={$imageId}">{tr}Cancel Edit{/tr}</a>
		    </div>
	    </div>
	</form>

	<br>
	<br><br>

	<!--this table is a duplicate of the one in tiki-browse_image.tpl-->
	<div class="table-responsive">
		<table class="table noslideshow">
			<tr><td class="odd">{tr}Image Name:{/tr}</td><td class="odd">{$name}</td></tr>
			<tr><td class="even">{tr}Created:{/tr}</td><td class="even">{$created|tiki_long_datetime}</td></tr>
			<tr><td class="odd">{tr}Image size:{/tr}</td><td class="odd">{$xsize}x{$ysize}</td></tr>
			<tr><td class="even">{tr}Image Scale:{/tr}</td><td class="even">{if $resultscale}{$xsize_scaled}x{$ysize_scaled}{else}{tr}Original Size{/tr}{/if}</td></tr>
			<tr><td class="odd">{tr}Hits:{/tr}</td><td class="odd">{$hits}</td></tr>
			<tr><td class="even">{tr}Description:{/tr}</td><td class="even">{$description}</td></tr>
			<tr><td class="odd">{tr}Author:{/tr}</td><td class="odd">{$image_user|userlink}</td></tr>
			{if $tiki_p_admin_galleries eq 'y' or ($user and $user eq $owner)}
				<tr>
					<td class="even">
						{tr}Move image:{/tr}
					</td>
					<td class="odd">
						<form action="tiki-browse_image.php" method="post">
							<input type="hidden" name="scalesize" value="{$scalesize|escape}">
							<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
							<input type="hidden" name="imageId" value="{$imageId|escape}">
							<input type="hidden" name="galleryId" value="{$galleryId|escape}">
							<input type="text" name="newname" value="{$name}">
							<select name="newgalleryId">
								{section name=idx loop=$galleries}
									<option value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
								{/section}
							</select>
							<input type="submit" class="btn btn-default btn-sm" name="move_image" value="{tr}Move{/tr}">
						</form>
					</td>
				</tr>
			{/if}
		</table>
	</div>
	<br><br>

	<div class="table-responsive">
		<table class="table noslideshow" style="font-size:small">
			<tr>
				<td class="even" style="border-bottom:0px" colspan="2">
					{tr}Include the image in a tiki page using the following syntax:{/tr}
				</td>
			</tr>
			<tr>
				<td width="6px" style="border:0px">
				</td>
				<td style="border:0px">
					<code>
						{if $resultscale == $defaultscale}
							{literal}{{/literal}img id={$imageId}{literal}}{/literal}
						{elseif !$resultscale}
							{literal}{{/literal}img id={$imageId}&amp;scalesize=0){literal}}{/literal}
						{else}
							{literal}{{/literal}img id={$imageId}&amp;scaled&amp;scalesize={$resultscale}{literal}}{/literal}
						{/if}
					</code>
				</td>
			</tr>
			<tr>
				<td class="even" style="border-bottom:0px" colspan="2">
					{tr}To include the image in an HTML page:{/tr}
				</td>
			</tr>
			<tr>
				<td width="10px" style="border:0px">
				</td>
				<td style="border:0px">
					<code>
						{if $resultscale == $defaultscale}
							&lt;img src="{$url_show}?id={$imageId}" /&gt;
						{elseif !$resultscale}
							&lt;img src="{$url_show}?id={$imageId}&amp;scalesize=0" /&gt;
						{else}
							&lt;img src="{$url_show}?id={$imageId}&amp;scalesize={$resultscale}" /&gt;
						{/if}
					</code>
				</td>
			</tr>
			<tr>
				<td class="even" style="border-bottom:0px" colspan="2">
					{tr}To link to this page from another tiki page:{/tr}
				</td>
			</tr>
			<tr>
				<td width="6px" style="border:0px">
				</td>
				<td style="border:0px">
					<code>
						{literal}[{/literal}tiki-browse_image.php?imageId={$imageId}{literal}]{/literal}
					</code>
				</td>
			</tr>
		</table>
	</div>
</div>
