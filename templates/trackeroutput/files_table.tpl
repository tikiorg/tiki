<div id="display_f{$field.fieldId|escape}" class="files display_f{$field.fieldId|escape}">
	<table class="table table-striped table-hover">
		<thead>
		<tr>
			<th>{tr}File{/tr}</th>
			<th>{tr}Date{/tr}</th>
			<th>{tr}Description{/tr}</th>
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
				<td>{$file.description|escape}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
