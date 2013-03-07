<input
	type="text"
	id="{$object_selector.id|escape}"
	{if $object_selector.name}name="{$object_selector.name|escape}"{/if}
	{if $object_selector.class}class="{$object_selector.class|escape}"{/if}
	{if $object_selector.value}value="{$object_selector.value|escape}"{/if}
	/>

{jq}
$('#{{$object_selector.id|escape}}')
	.object_selector({{$object_selector.filter}}, {{$prefs.maxRecords|escape}});
{/jq}
