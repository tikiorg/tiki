{* $Id$ *}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}To add/remove image galleries, go to "Image Galleries" on the application menu, or{/tr} <a class="rbox-link" href="tiki-galleries.php">{tr}Click Here{/tr}</a>.
<hr>
{tr}You can upload images of a size of {/tr}{$max_img_upload_size|kbsize}. {tr}Alter the php.ini variables file_uploads, upload_max_filesize, post_max_size and database variables (max_allowed_packet for mysql) to change this value{/tr}.
{/remarksbox}

{tabset name="admin_gal"}
	{tab name="{tr}Features{/tr}"}
		<form id="galfeatures" class="form-horizontal" action="tiki-admin.php?page=gal" method="post">
			{include file='access/include_ticket.tpl'}
			<br>
			<div class="form-group col-lg-12 clearfix">
				<div class="pull-right">
					<input type="submit" class="btn btn-primary btn-sm" form="galfeatures" name="galfeatures" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
				</div>
			</div>
			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_galleries visible="always"}
			</fieldset>
			<fieldset>
				<legend>{tr}Galleries features{/tr}</legend>
				{preference name=home_gallery}
				{preference name=feature_gal_rankings}
				{preference name=feature_image_galleries_comments}
				{preference name=feature_gal_slideshow}
				<div class="adminoptionbox">
					{preference name=gal_use_db}
					<div class="adminoptionboxchild" id="gal_use_db_childcontainer">
						{preference name=gal_use_dir}
					</div>
				</div>
				{preference name=gal_use_lib}
				{preference name=gal_match_regex}
				{preference name=gal_nmatch_regex}
				<div class="adminoptionbox">
					{preference name=feature_gal_batch}
					<div class="adminoptionboxchild" id="feature_gal_batch_childcontainer">
						{preference name=gal_batch_dir}
					</div>
				</div>
				{if $prefs.feature_categories eq 'y'}
					{preference name=feature_image_gallery_mandatory_category}
				{/if}
				{preference name=gal_image_mouseover}
				<div class="adminoptionbox">
					{preference name=preset_galleries_info}
					<div class="adminoptionboxchild" id="preset_galleries_info_childcontainer">
						{preference name=maxRowsGalleries}
						{preference name=rowImagesGalleries}
						{preference name=thumbSizeXGalleries}
						{preference name=thumbSizeYGalleries}
						{preference name=scaleSizeGalleries}
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>{tr}Gallery listing configuration{/tr}</legend>
				{preference name=gal_list_name}
				{preference name=gal_list_parent}
				{preference name=gal_list_description}
				{preference name=gal_list_created}
				{preference name=gal_list_lastmodif}
				{preference name=gal_list_user}
				{preference name=gal_list_imgs}
				{preference name=gal_list_visits}
			</fieldset>
			<fieldset>
				<legend>{tr}Comments settings{/tr}</legend>
				{preference name=image_galleries_comments_per_page}
				{preference name=image_galleries_comments_default_order}
			</fieldset>
			<br>
			<div class="form-group col-lg-12 text-center">
				<input type="submit" class="btn btn-primary btn-sm" form="galfeatures" name="galfeatures" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</form>
	{/tab}
	{tab name="{tr}Manage images{/tr}"}
		<form id="galremove" class="form-horizontal" action="tiki-admin.php?page=gal" method="post">
			{include file='access/include_ticket.tpl'}
			<br>
			<fieldset>
				<legend>{tr}Remove unused images{/tr}</legend>
				<div class="form-group">
					<label class="control-label col-sm-4">
						{tr}Remove images in the system gallery not being used in Wiki pages, articles or blog posts{/tr}
					</label>
					<div class="col-sm-8">
						<button type="submit" class="btn btn-default btn-sm" form="galremove" name="rmvorphimg" value="1">
							{tr}Remove{/tr}
						</button>
					</div>
				</div>
			</fieldset>
		</form>

			{if $prefs.gal_use_db eq 'n'}
				{$label = "{tr}Move images from database storage to filesystem storage{/tr}"}
				{$value = 'to_fs'}
			{else}
				{$label = "{tr}Move images from filesystem storage to database storage{/tr}"}
				{$value = 'to_db'}
			{/if}
		<form id="galmove" class="form-horizontal" action="tiki-admin.php?page=gal" method="post">
			{include file='access/include_ticket.tpl'}
			<input type="hidden" name="mvimg" value="{$value}">
			<fieldset>
				<legend>{tr}Move images{/tr}</legend>
				<div class="form-group">
					<label class="control-label col-sm-4">{$label}</label>
					<div class="col-sm-8">
						<select name="move_gallery" class="form-control">
							<option value="-1">{tr}All galleries{/tr}</option>
							<option value="0">{tr}System Gallery{/tr}</option>
							{section name=ix loop=$galleries}
								<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:20:"...":true}</option>
							{/section}
						</select><br>
						<button type="submit" class="btn btn-default btn-sm" form="galmove" name="mvimg" value="{$value}">
							{tr}Move{/tr}
						</button>
					</div>
				</div>
			</fieldset>
		</form>
			{* Don't see a function that does this
						{if $prefs.gal_use_db eq 'n'}
							<div class="form-group">
								<label class="control-label col-sm-4">
									{tr}Move images from old filesystem store to new directory{/tr}
								</label>
								<div class="col-sm-8">
									<input type="text" name="gal_use_dir" value="{$prefs.gal_use_dir|escape}" size="50" disabled="disabled">
									<br>
									<button type="submit" class="btn btn-default btn-sm" name="newdir" value="to_newdir">
										{tr}Move{/tr}
									</button>
								</div>
							</div>
						{/if}
						{if isset($movedimgs) and $movedimgs}
							<div class="form-group">
								<div class="col-sm-offset-4 col-am-8">
									<td colspan="3">{tr}Moved{/tr} {$movedimgs} {tr}Images{/tr}</td>
								</div>
							</div>
						{/if}
			*}
	{/tab}
{/tabset}






