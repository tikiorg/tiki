<ul id="display_f{$field.fieldId|escape}">
	{foreach from=$field.relations item=identifier}
		<li>{object_link identifier=$identifier}</li>
	{/foreach}
	{foreach from=$field.inverts item=identifier}
		<li>{object_link identifier=$identifier}</li>
	{/foreach}
</ul>
{jq}
	$('#display_f{{$field.fieldId|escape}}').sortList();
{/jq}
