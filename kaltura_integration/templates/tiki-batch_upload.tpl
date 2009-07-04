{* $Id$ *}
{title}{tr}Directory batch upload{/tr}{/title}

<div class="navbar">
	{if $galleryId ne ''}
		{button href="tiki-browse_gallery.php?galleryId=$galleryId" _text="{tr}Browse Gallery{/tr}"}
	{else}
		{button href="tiki-galleries.php" _text="{tr}Browse Gallery{/tr}"}
	{/if}
	{button href="tiki-upload_image.php" _text="{tr}Upload From Disk{/tr}"}
</div>

{if count($feedback)}<div class="simplebox highlight">{section name=i loop=$feedback}{$feedback[i]}<br />{/section}</div>{/if}

{$totimg} {tr}available images{/tr} {$dirsize} <br /><br />
<form method="post" action="tiki-batch_upload.php" name="f">
	<table border="0" class="normal" id="imagelist" width="100%">
		<tr>
			<th style="width:42px">{select_all checkbox_names='imgs[]'}</th>
			<th><a href="javascript:void(0);">{tr}Filename{/tr}</a></th>
			<th style="width:80px"><a href="javascript:void(0);">{tr}Width{/tr}</a></th>
			<th style="width:80px"><a href="javascript:void(0);">{tr}Height{/tr}</a></th>
			<th style="width:80px"><a href="javascript:void(0);">{tr}Filesize{/tr}</a></th>
			<th style="width:80px"><a href="javascript:void(0);">{tr}Filetype{/tr}</a></th>
		</tr>
		{cycle print=false values="even,odd"}
		{foreach key=k item=it from=$imgstring}
			<tr class="{cycle}">
				<td><input type="checkbox" name="imgs[]" value="{$it[0]}" id="box_{$k}" /></td>
				<td><label for="box_{$k}">{$it[0]}</label></td>
				<td>{$it[1]}</td>
				<td>{$it[2]}</td>
				<td>{$it[3]|kbsize}</td>
				<td>{$it[4]}</td>
			</tr>
		{/foreach}
	</table>
<br />
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="removeExt" value="true" id="removeExt" /> {tr}Remove File Extension from Image Name{/tr}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "digicam0001.jpg" then name digicam0001 will be used for the name field{/tr}<br />
<br />
{if $permAddGallery eq "y"}
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="subdirToSubgal" value="true" id="subdirToSubgal" /> {tr}Convert the last sub directory to a sub gallery{/tr}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "misc/screenshots/digicam0001.jpg" a gallery named "screenshots" will be created{/tr}<br />
<br />
{/if}
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="subToDesc" value="true" id="subToDesc" /> {tr}Use the last sub directory name as description{/tr}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "misc/screenshots/digicam0001.jpg" a description "screenshots" will be created{/tr}<br />
<br />
&nbsp;&nbsp;&nbsp;&nbsp; {tr}Select a Gallery{/tr}
<select name="galleryId">
{section name=idx loop=$galleries}
{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_batch_upload_image_dir eq 'y')}
<option  value="{$galleries[idx].galleryId}" {if $galleries[idx].galleryId eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
{/if}
{/section}
</select>
&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="batch_upload" value="{tr}Process{/tr}" />
</form>
<br />


