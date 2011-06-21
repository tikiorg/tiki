<ul>
	{foreach from=$field.relations item=identifier}
		<li>{object_link identifier=$identifier}</li>
	{/foreach}
</ul>
