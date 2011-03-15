{remarksbox type="tip" title="{tr}Tip{/tr}"}
{tr}To add/remove image galleries, go to "Image Galleries" on the application menu, or{/tr} <a class="rbox-link" href="tiki-galleries.php">{tr}Click Here{/tr}</a>.
<hr />
{tr}You can upload images of a size of {/tr}{$max_img_upload_size|kbsize}. {tr}Alter the php.ini variables file_uploads, upload_max_filesize, post_max_size and database variables (max_allowed_packet for mysql) to change this value{/tr}.
{/remarksbox}

<fieldset class="admin">
	<legend>{tr}Home Gallery{/tr}</legend>
	<form action="tiki-admin.php?page=gal" method="post">
		{preference name=home_gallery}
		<input type="submit" name="galset" value="{tr}OK{/tr}" />
	</form>
</fieldset>

<fieldset class="admin">
	<legend>{tr}Galleries features{/tr}</legend>
	<form action="tiki-admin.php?page=gal" method="post">
		<table class="admin">
			<tr>
				<td><label>{tr}Rankings:{/tr}</label></td>
				<td>
					<input type="checkbox" name="feature_gal_rankings" {if $prefs.feature_gal_rankings eq 'y'}checked="checked"{/if}/>
				</td>
			</tr>
			<tr>
				<td><label>{tr}Comments:{/tr}</label></td>
				<td>
					<input type="checkbox" name="feature_image_galleries_comments" {if $prefs.feature_image_galleries_comments eq 'y'}checked="checked"{/if}/>
				</td>
			</tr>
			<tr>
				<td><label>{tr}Uses Slideshow:{/tr}</label></td>
					<td>
						<input type="checkbox" name="feature_gal_slideshow" {if $prefs.feature_gal_slideshow eq 'y'}checked="checked"{/if}/>
					</td>
				</tr>
				<tr>
					<td><label>{tr}Use database to store images:{/tr}</label></td>
					<td>
						<input type="radio" name="gal_use_db" value="y" {if $prefs.gal_use_db eq 'y'}checked="checked"{/if}/>
					</td>
				</tr>
				<tr>
					<td><label>{tr}Use a directory to store images:{/tr}</label></td>
					<td>
						<input type="radio" name="gal_use_db" value="n" {if $prefs.gal_use_db eq 'n'}checked="checked"{/if}/>
						<label>{tr}Directory path:{/tr}</label>
						<br />
						<input type="text" name="gal_use_dir" value="{$prefs.gal_use_dir|escape}" size="50" />
						<br />
						({tr}Note: if you change this directory, you have to move the contents to the new directory. You can also use the 'Mover' below.{/tr})
					</td>
				</tr>
				<tr>
					<td><label>{tr}Library to use for processing images:{/tr}</label></td>
					<td>
						<input type="radio" name="gal_use_lib" value="gd" {if $prefs.gal_use_lib ne 'imagick'}checked="checked"{/if}/>GD: {$gdlib}
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="radio" name="gal_use_lib" value="imagick" {if $prefs.gal_use_lib eq 'imagick'}checked="checked"{/if}/>Imagick 0: {$imagicklib}
					</td>
				</tr>
				<tr>
					<td><label>{tr}Uploaded image names must match regex:{/tr}</label></td>
					<td>
						<input type="text" name="gal_match_regex" value="{$prefs.gal_match_regex|escape}"/>
					</td>
				</tr>
				<tr>
					<td><label>{tr}Uploaded image names cannot match regex:{/tr}</label></td>
					<td>
						<input type="text" name="gal_nmatch_regex" value="{$prefs.gal_nmatch_regex|escape}"/>
					</td>
				</tr>
				<tr>
					<td colspan="2"><b>{tr}Directory Batch Loading{/tr}</b>
					<br />
					{tr}If you enable Directory Batch Loading, you need to setup a web-readable directory (outside of your web space is better). Then setup a way to upload images in that dir, either by scp, ftp, or other protocols{/tr}
				</td>
			</tr>
			<tr>
				<td><label>{tr}Enable directory batch loading:{/tr}</label></td>
				<td>
					<input type="checkbox" name="feature_gal_batch" {if $prefs.feature_gal_batch eq 'y'}checked="checked"{/if}/>
				</td>
			</tr>
			<tr>
				<td>
					<label>{tr}Batch loading directory:{/tr}</label>
				</td>
				<td>
					<input type="text" name="gal_batch_dir" value="{$prefs.gal_batch_dir|escape}" size="50" />
				</td>
			</tr>
			{if $prefs.feature_categories eq 'y'}
				<tr>
					<td>{tr}Force and limit categorization to within subtree of:{/tr}</td>
					<td>
						<select name="feature_image_gallery_mandatory_category">
							<option value="-1" {if $prefs.feature_image_gallery_mandatory_category eq -1 or $prefs.feature_image_gallery_mandatory_category eq ''}selected="selected"{/if}>{tr}None{/tr}</option>
							<option value="0" {if $prefs.feature_image_gallery_mandatory_category eq 0}selected="selected"{/if}>{tr}All{/tr}</option>
							{section name=ix loop=$catree}
								<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $prefs.feature_image_gallery_mandatory_category}selected="selected"{/if}>{$catree[ix].categpath}</option>
							{/section}
						</select>
					</td>
				</tr>
			{/if}
			<tr>
				<td><label>{tr}Display image information in a mouseover box:{/tr}</label></td>
				<td>
					<input type="radio" name="gal_image_mouseover" value="n" {if $prefs.gal_image_mouseover eq 'n'}checked="checked"{/if}/>{tr}No{/tr}
					<input type="radio" name="gal_image_mouseover" value="y" {if $prefs.gal_image_mouseover eq 'y'}checked="checked"{/if}/>{tr}Yes{/tr}
					<input type="radio" name="gal_image_mouseover" value="only" {if $prefs.gal_image_mouseover eq 'only'}checked="checked"{/if}/>{tr}yes, and don't display that information under the image{/tr}
				</td>
			</tr>
				<tr>
				<td>
					<label>{tr}Use default max rows, images per row, thumbnails size and scale size for all galleries (set values below){/tr}</label>
				</td>
				<td>
					<input type="checkbox" name="preset_galleries_info" {if $prefs.preset_galleries_info eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Max Rows per page:{/tr}</td>
				<td>
					<input type="text" name="maxRows" value="{$maxRows|escape}" /><i>{tr}Default:{/tr} {if !empty($prefs.maxRowsGalleries)}{$prefs.maxRowsGalleries}{else}10{/if}</i>
				</td>
			</tr>
			<tr>
				<td>{tr}Images per row:{/tr}</td>
				<td>
					<input type="text" name="rowImages" value="{$rowImages|escape}" /><i>{tr}Default:{/tr} {if !empty($prefs.rowImagesGalleries)}{$prefs.rowImagesGalleries}{else}6{/if}</i>
				</td>
			</tr>
			<tr>
				<td>
					{tr}Thumbnails size X:{/tr}
				</td>
				<td>
					<input type="text" name="thumbSizeX" value="{$thumbSizeX|escape}" /><i>{tr}Pixels Default:{/tr} {if !empty($prefs.thumbSizeXGalleries)}{$prefs.thumbSizeXGalleries}{else}80{/if}</i>
				</td>
			</tr>
			<tr>
				<td>{tr}Thumbnails size Y:{/tr}</td>
				<td>
					<input type="text" name="thumbSizeY" value="{$thumbSizeY|escape}" /><i>{tr}Pixels Default:{/tr} {if !empty($prefs.thumbSizeYGalleries)}{$prefs.thumbSizeYGalleries}{else}80{/if}</i>
				</td>
			</tr>
			<tr>
				<td>{tr}Default scale size:{/tr}</td>
				<td>
					<input type="text" name="scaleSize" size="4" value="{$scaleSize|escape}" />{tr}pixels{/tr}
				</td>
			</tr>
			<tr>
				<td colspan="2" class="input_submit_container">
					<input type="submit" name="galfeatures" value="{tr}Set features{/tr}" />
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<fieldset class="admin">
	<legend>{tr}Exterminator{/tr}</legend>
	<form action="tiki-admin.php?page=gal" method="post">
		<table class="admin">
			<tr>
				<td>
					<label>{tr}Remove images in the system gallery not being used in Wiki pages, articles or blog posts{/tr}</label>
					<input type="hidden" name="rmvorphimg" value="1" />
				</td>
				<td>
					<input type="submit" name="button" value="{tr}Remove{/tr}" />
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<fieldset class="admin">
	<legend>{tr}Mover{/tr}</legend>
		<form action="tiki-admin.php?page=gal" method="post">
		<table class="admin">
			<tr>
				<td>
					{if $prefs.gal_use_db eq 'n'}
						<label>{tr}Move images from database storage to filesystem storage{/tr}</label>
						<input type="hidden" name="mvimg" value="to_fs" />
					{else}
						<label>{tr}Move images from filesystem storage to database storage{/tr}</label>
						<input type="hidden" name="mvimg" value="to_db" />
					{/if}
				</td>
				<td>
					<select name="move_gallery">
						<option value="-1">{tr}All galleries{/tr}</option>
						<option value="0">{tr}System Gallery{/tr}</option>
						{section name=ix loop=$galleries}
							<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:20:"...":true}</option>
						{/section}
					</select>
				</td>
				<td>
					<input type="submit" name="button" value="{tr}Move{/tr}" />
				</td>
			</tr>
			{if $prefs.gal_use_db eq 'n'}
				<tr>
					<td>
						<label>{tr}Move images from old filesystem store to new directory{/tr}<input type="hidden" name="newdir" value="to_newdir"></label>
					<td>
					<input type="text" name="gal_use_dir" value="{$prefs.gal_use_dir|escape}" size="50" />
				<td>
				<input type="submit" name="button" value="{tr}Move{/tr}" /></td></tr>
			{/if}
			{if $movedimgs}
				<tr>
					<td colspan="3">{tr}Moved{/tr} {$movedimgs} {tr}Images{/tr}</td>
				</tr>
			{/if}
		</table>
	</form>
</fieldset>

<fieldset class="admin">
	<legend>{tr}Gallery listing configuration{/tr}</legend>
	<form method="post" action="tiki-admin.php?page=gal">
		<table class="admin">
			<tr>
				<td><label>{tr}Name{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_name" {if $prefs.gal_list_name eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><label>{tr}Parent{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_parent" {if $prefs.gal_list_parent eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><label>{tr}Description{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_description" {if $prefs.gal_list_description eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><label>{tr}Created{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_created" {if $prefs.gal_list_created eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><label>{tr}Last modified{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_lastmodif" {if $prefs.gal_list_lastmodif eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><label>{tr}User{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_user" {if $prefs.gal_list_user eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><label>{tr}Images{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_imgs" {if $prefs.gal_list_imgs eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td><label>{tr}Visits{/tr}</label></td>
				<td><input type="checkbox" name="gal_list_visits" {if $prefs.gal_list_visits eq 'y'}checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td colspan="2" class="input_submit_container"><input type="submit" name="imagegallistprefs" value="{tr}Change configuration{/tr}" /></td>
			</tr>
		</table>	
	</form>	
</fieldset>

<fieldset class="admin">
	<legend>{tr}Image galleries comments settings{/tr}</legend>
	<form method="post" action="tiki-admin.php?page=gal">
		<table class="admin">
			<tr>
				<td><label>{tr}Default number of comments per page:{/tr} </label></td>
				<td>
					<input size="5" type="text" name="image_galleries_comments_per_page" value="{$prefs.image_galleries_comments_per_page|escape}" />
				</td>
			</tr>
			<tr>
				<td><label>{tr}Comments default ordering{/tr}</label></td>
				<td>
					<select name="image_galleries_comments_default_order">
						<option value="commentDate_desc" {if $prefs.image_galleries_comments_default_order eq 'commentDate_desc'}selected="selected"{/if}>{tr}Newest first{/tr}</option>
						<option value="commentDate_asc" {if $prefs.image_galleries_comments_default_order eq 'commentDate_asc'}selected="selected"{/if}>{tr}Oldest first{/tr}</option>
						<option value="points_desc" {if $prefs.image_galleries_comments_default_order eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="input_submit_container"><input type="submit" name="imagegalcomprefs" value="{tr}Change settings{/tr}" /></td>
			</tr>
		</table>
	</form>
</fieldset>
