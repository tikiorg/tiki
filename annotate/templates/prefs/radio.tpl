<div class="adminoptionbox preference clearfix {$p.tagstring|escape}{if isset($smarty.request.highlight) and $smarty.request.highlight eq $p.preference} highlight{/if}">
	{if $p.name}
		<label for="{$p.id|escape}">{$p.name|escape}:</label>
	{/if}

	{foreach from=$p.options key=value item=label name=loop}
		<div class="adminoptionlabel">
			 <input id="{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}" type="radio" name="{$p.preference|escape}" 
			 	value="{$value}"{if $p.value eq $value} checked="checked"{/if} {$p.params}/>
			 <label for="{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}">{$label|escape}</label>
		</div>
	{/foreach}
	{include file="prefs/shared-flags.tpl"}
	{if $p.hint}
		<br/><em>{$p.hint|simplewiki}</em>
	{/if}
	{include file="prefs/shared-dependencies.tpl"}

{foreach from=$p.options key=value item=label name=loop}
	{jq}
if( ! $('#{{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}}').attr('checked') ) {
	$('#{{$p.preference|escape}}_childcontainer_{{$smarty.foreach.loop.index}}').hide();
}
$('#{{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}}').change( function() {
	if( $('#{{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}}').attr('checked') ) {
		show('#{{$p.preference|escape}}_childcontainer_{{$smarty.foreach.loop.index}}');
	}
} );
{/jq}
{/foreach}
</div>
