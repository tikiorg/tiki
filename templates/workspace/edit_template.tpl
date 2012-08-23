<form method="post" action="{service controller=workspace action=edit_template id=$id}">
	<label>
		{tr}Name{/tr}
		<input type="text" name="name" value="{$name|escape}"/>
	</label>
	{if $area}
		<label>
			<input type="checkbox" name="area" value="1" {if $area eq 'y'}checked="checked"{/if} />
			{tr}Bind area{/tr}
		</label>
	{/if}
	
	<div class="submit">
		<input type="submit" value="{tr}Save{/tr}"/>
	</div>
</form>
