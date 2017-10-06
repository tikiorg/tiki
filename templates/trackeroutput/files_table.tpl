{$showdescriptions = false}
{foreach from=$data.files item=file}
	{if not empty($file.description|escape)}
		{$showdescriptions = true}
	{/if}
{/foreach}
<div id="display_f{$field.fieldId|escape}" class="files-field display_f{$field.fieldId|escape}">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th>{tr}File{/tr}</th>
			<th>{tr}Date{/tr}</th>
			{if $showdescriptions}<th>{tr}Description{/tr}</th>{/if}
		</tr>
		</thead>
		<tbody>
		{foreach from=$data.files item=file}
			<tr>
				<td>
					<img src="tiki-download_file.php?fileId={$file.fileId|escape}&amp;thumbnail" width="32" height="32">
					{object_link type="file" id=$file.fileId title=$file.name}
				</td>
				<td>{$file.lastModif|tiki_short_datetime}</td>
				{if $showdescriptions}<td>{$file.description|escape}</td>{/if}
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
