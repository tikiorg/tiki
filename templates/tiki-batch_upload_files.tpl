{* $Header$ *}
<h1><a href="tiki-batch_upload_files.php" class="pagetitle">{tr}Directory batch upload{/tr}</a></h1>
<span class="button2">
{if $galleryId ne ''}
<a href="tiki-list_file_gallery.php?galleryId={$galleryId}" class="linkbut">
{else}
<a href="tiki-file_galleries.php" class="linkbut">
{/if}
{tr}Browse file gallery{/tr}</a></span>
<span class="button2"><a href="tiki-upload_file.php?galleryId={$galleryId}" class="linkbut">{tr}Upload from disk{/tr}</a></span>
<br />
<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}Please do not use this feature to upload data into the database.{/tr}</div>
</div>
<br /><br />

{if count($feedback)}<div class="simplebox highlight">{section name=i loop=$feedback}{$feedback[i]}<br />{/section}</div>{/if}

<h2>{$totimg} {tr}available files{/tr} {$dirsize}</h2>
<form method="get" action="tiki-batch_upload_files.php" name="f">
<table border="0" class="normal" id="filelist" width="100%">
<tr>
<th width="42" class="heading" nowrap="nowrap">
<input type="checkbox" name="imgs[]" value="ALL" id="box_all" onclick="disableOthers(this);"/>
<script type="text/javascript">
{literal}
<!--
function disableOthers(all) {
    els = document.getElementsByTagName("input");
    for(i = 0; i < els.length; ++i) {
    if(els[i].type == "checkbox" && els[i].name == "imgs[]" && els[i].value != "ALL" && all.checked) {
      els[i].disabled = true;
    } else {
      els[i].disabled = false;
    }
  }
}
//-->
{/literal}
</script>
<label class="tableheading" for="box_all">{tr}all{/tr}</label></th>
<th class="heading"><a class="tableheading" href="javascript:void(0);">{tr}filename{/tr}</a></th>
<th class="heading" width="80"><a class="tableheading" href="javascript:void(0);">{tr}filesize{/tr}</th>
<th class="heading" width="80"><a class="tableheading" href="javascript:void(0);">{tr}filetype{/tr}</a></th></tr>
{cycle print=false values="even,odd"}
{foreach key=k item=it from=$filestring}
<tr class="{cycle}">
<td><input type="checkbox" name="files[]" value="{$it[0]}" id="box_{$k}" /></td>
<td><label for="box_{$k}">{$it[0]}</label></td>
<td>{$it[1]|kbsize}</td>
<td>{$it[2]}</td></tr>
{/foreach}
</table>
<br />
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="removeExt" value="true" id="removeExt" /> {tr}remove file extension from image name{/tr}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "digicam0001.jpg" then name digicam0001 will be used for the name field{/tr}<br />
<!--
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="subdirToSubgal" value="true" id="subdirToSubgal" /> {tr}convert sub directories to sub galleries{/tr}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "misc/screenshots/digicam0001.jpg" a gallery named screenshots will be created{/tr}<br />
-->
<br />
{if $permAddGallery eq "y" || $user ne "admin"}
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="subToDesc" value="true" id="subToDesc" /> {tr}use sub directory names as description{/tr}<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "misc/screenshots/digicam0001.jpg" a description misc/screenshots will be created{/tr}<br />
<br />
{/if}
&nbsp;&nbsp;&nbsp;&nbsp; {tr}Select a File Gallery{/tr}
<select name="galleryId">
{section name=idx loop=$galleries}
{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_batch_upload_file_dir eq 'y')}
<option  value="{$galleries[idx].galleryId}" {if $galleries[idx].galleryId eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
{/if}
{/section}
</select>
&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="batch_upload" value="{tr}Process files{/tr}" />
</form>
<br />
