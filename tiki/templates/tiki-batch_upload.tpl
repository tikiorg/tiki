<a href="tiki-batch_upload.php" class="pagetitle">{tr}Directory batch upload{/tr}</a><br /><br />
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
<form class="box" method="get" action="tiki-batch_upload.php" name="f">
<table border="0" class="normal">
<tr>
<td width="42" class="heading" nowrap="nowrap"><input name="tikiswitch" id="tikiswitch" type="checkbox" onclick="switchCheckboxes(this.form,'imgs[]',this.checked);" /><label class="tableheading" for="tikiswitch">{tr}all{/tr}</label></td>
<td class="heading"><a class="tableheading" href="javascript:void(0);">{tr}filename{/tr}</a></td>
<td class="heading"><a class="tableheading" href="javascript:void(0);">{tr}width{/tr}</a></td>
<td class="heading"><a class="tableheading" href="javascript:void(0);">{tr}height{/tr}</a></td>
<td class="heading"><a class="tableheading" href="javascript:void(0);">{tr}filesize{/tr}</td>
<td class="heading"><a class="tableheading" href="javascript:void(0);">{tr}filetype{/tr}</a></td></tr>
{cycle print=false values="even,odd"}
{foreach key=k item=it from=$imgstring}
<tr class="{cycle}">
<td><input type="checkbox" name="imgs[]" value="{$it[0]}" id="box_{$k}"></td>
<td><label for="box_{$k}">{$it[0]}</label></td>
<td>{$it[1]}</td>
<td>{$it[2]}</td>
<td>{$it[3]|kbsize}</td>
<td>{$it[4]}</td></tr>
{/foreach}
</table>
<br />
&nbsp;&nbsp;&nbsp;&nbsp; {tr}Select a Gallery{/tr}  
<select name="galleryId">
{section name=idx loop=$galleries}
{if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_batch_upload_image_dir eq 'y')}
<option  value="{$galleries[idx].id|escape}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
{/if}
{/section}
</select>
&nbsp;&nbsp;&nbsp;&nbsp; <input type="submit" name="batch_upload" value="{tr}Process{/tr}" />
</form>
<br />


