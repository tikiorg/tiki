{title url=''}{tr}Directory batch upload{/tr}{/title}

<div class="t_navbar btn-group form-group">
	{if $galleryId ne ''}
		{button href="tiki-list_file_gallery.php?galleryId=$galleryId" class="btn btn-default" _text="{tr}Browse File Gallery{/tr}"}
	{else}
		{button href="tiki-list_file_gallery.php" class="btn btn-default" _text="{tr}Browse File Gallery{/tr}"}
	{/if}
	{button href="tiki-upload_file.php?galleryId=$galleryId" class="btn btn-default" _text="{tr}Upload From Disk{/tr}"}
</div>

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Please do not use this feature to upload data into the database.{/tr}{/remarksbox}

{if count($feedback)}<div class="alert alert-warning">{section name=i loop=$feedback}{$feedback[i]}<br>{/section}</div>{/if}

<h2>{$totfile} {tr}Available Files{/tr} {$totalsize|kbsize}</h2>
<form method="post" action="tiki-batch_upload_files.php" name="f" class="form-horizontal">
	<table class="table table-stripped" id="filelist">
		<tr>
			<th>{select_all checkbox_names='files[]'}</th>
			<th>{tr}Filename{/tr}</th>
			<th width="80">{tr}Filesize{/tr}</th>
			<th width="80">{tr}Filetype{/tr}</th>
			<th width="80">{tr}Permissions{/tr}</th>
		</tr>

		{foreach key=k item=it from=$filestring}
			<tr>
				<td class="checkbox-cell"><input type="checkbox" name="files[]" value="{$it[0]}" id="box_{$k}"></td>
				<td><label for="box_{$k}">{$it[0]}</label></td>
				<td>{$it[1]|kbsize}</td>
				<td>{$it[2]}</td>
				<td>{if $it[3]}{icon name='success' title="{tr}File is writable{/tr}"}{else}{icon name='ban' title="{tr}File is not writable{/tr}"}{/if}</td>
			</tr>
		{/foreach}
	</table>
	<hr>
<!--
{if $permAddGallery eq "y"}
&nbsp;&nbsp;&nbsp;&nbsp; <input type="checkbox" name="subdirToSubgal" value="true" id="subdirToSubgal"> {tr}Convert the last sub directory to a sub gallery{/tr}<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {tr}eg. from "misc/screenshots/digicam0001.jpg" a gallery named "screenshots" will be created{/tr}<br>
{/if}
-->
    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}Use the last sub directory name as description{/tr}</label>
        <div class="col-sm-7">
            <input type="checkbox" name="subToDesc" value="true" id="subToDesc">
            <div class="help-block">
                {tr}eg. from "misc/screenshots/digicam0001.jpg" a description "screenshots" will be created{/tr}r}
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}Select a File Gallery{/tr}</label>
        <div class="col-sm-7">
            <select name="galleryId" class="form-control">
                <option value="{$treeRootId}" {if $treeRootId eq $galleryId}selected="selected"{/if} style="font-style:italic; border-bottom:1px dashed #666;">{tr}Root{/tr}</option>
                {section name=idx loop=$galleries}
                    {if ($galleries[idx].individual eq 'n') or ($galleries[idx].individual_tiki_p_batch_upload_file_dir eq 'y')}
                        <option value="{$galleries[idx].id}" {if $galleries[idx].id eq $galleryId}selected="selected"{/if}>{$galleries[idx].name}</option>
                    {/if}
                {/section}
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-7">
            <input type="submit" class="btn btn-default btn-sm" name="batch_upload" value="{tr}Process files{/tr}">
        </div>
    </div>
</form>
