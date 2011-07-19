<ol>
	{foreach from=$field.files key=fileId item=info}
		<li>{object_link type=file id=$fileId title=$info.name}</li>
	{/foreach}
</ol>
