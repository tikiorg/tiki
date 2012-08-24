<form method="post" action="{service controller=workspace action=edit_template id=$id}">
	{remarksbox type=info title="{tr}Not enough options?{/tr}"}
		<p>{tr}This is the simple edition interface offering a subset of the available features. You can switch to the advanced mode and get more power.{/tr}</p>
		<a class="ajax" href="{service controller=workspace action=advanced_edit id=$id}">{tr}Advanced Mode{/tr}</a>
	{/remarksbox}
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
