{* $Id$ *}
<h1><a href="tiki-batch_upload.php" class="pagetitle">{tr}Directory batch upload{/tr}</a></h1>
<span class="button2">
{if $galleryId ne ''}
<a href="tiki-browse_gallery.php?galleryId={$galleryId}" class="linkbut">
{else}
<a href="tiki-galleries.php" class="linkbut">
{/if}
{tr}Browse gallery{/tr}</a></span>
<span class="button2"><a href="tiki-upload_image.php" class="linkbut">{tr}Upload from disk{/tr}</a></span>
<br /><br />

{if count($feedback)}<div class="simplebox highlight">{section name=i loop=$feedback}{$feedback[i]}<br />{/section}</div>{/if}

{$totimg} {tr}available images{/tr} {$dirsize} <br /><br />
<form method="post" action="tiki-batch_upload.php" name="f">
<table border="0" class="normal" id="imagelist" width="100%">
<tr>
<th width="42" class="heading"></th>
<th class="heading"><a class="tableheading" href="javascript:void(0);">{tr}Filename{/tr}</a></th>
<th class="heading" width="80"><a class="tableheading" href="javascript:void(0);">{tr}width{/tr}</a></th>
<th class="heading" width="80"><a class="tableheading" href="javascript:void(0);">{tr}height{/tr}</a></th>
<th class="heading" width="80"><a class="tableheading" href="javascript:void(0);">{tr}Filesize{/tr}</th>
<th class="heading" width="80"><a class="tableheading" href="javascript:void(0);">{tr}Filetype{/tr}</a></th></tr>
{cycle print=false values="even,odd"}
{foreach key=k item=it from=$imgstring}
<tr class="{cycle}">
<td><input type="checkbox" name="imgs[]" value="{$it[0]}" id="box_{$k}" /></td>
<td><label for="box_{$k}">{$it[0]}</label></td>
<td>{$it[1]}</td>
<td>{$it[2]}</td>
<td>{$it[3]|kbsize}</td>
<td>{$it[4]}</td></tr>
{/foreach}
<tr><td colspan='4'><input name="switcher" id="clickall" type="checkbox" onclick="switchCheckboxes(this.form,'imgs[]',this.checked)"/>
<label for="clickall">{tr}Select All{/tr}</label></td></tr>
</table>
<br />
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="removeExt" value="true" id="removeExt" /> {tr}Remove File Extension from Image Name{/tr}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "digicam0001.jpg" then name digicam0001 will be used for the name field{/tr}<br />
<br />
{if $permAddGallery eq "y"}
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="subdirToSubgal" value="true" id="subdirToSubgal" /> {tr}convert the last sub directory to a sub gallery{/tr}<br />
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


