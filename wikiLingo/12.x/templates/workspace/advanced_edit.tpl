<form method="post" action="{service controller=workspace action=advanced_edit id=$id}">
	{if $is_advanced != 'y'}
		{remarksbox type=warning title="{tr}No turning back{/tr}"}
			<p>{tr}Once you switch your template to advanced mode, there is no turning back. The simple interface will no longer be available.{/tr}</p>
			<a href="{service controller=workspace action=edit_template id=$id}">{tr}Return to simple interface{/tr}</a>
		{/remarksbox}
	{/if}
	<label>
		{tr}Name{/tr}
		<input type="text" name="name" value="{$name|escape}">
	</label>
	{textarea syntax='tiki' codemirror='true'}{$definition}{/textarea}
	<div class="submit">
		<input type="submit" class="btn btn-default" value="{tr}Save{/tr}">
	</div>
</form>
