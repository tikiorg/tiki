<div class="adminoptionbox">
	{if $p.name}
		<label for="{$p.id|escape}">{$p.name|escape}:</label>
	{/if}

	{foreach from=$p.options key=value item=label name=loop}
		<div class="adminoptionlabel">
			 <input id="{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}" type="radio" name="{$p.preference|escape}" value="{$value}"{if $p.value eq $value} checked="checked"{/if} />
			 <label for="{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}">{$label|escape}</label>
		</div>
	{/foreach}
	{include file=prefs/shared-flags.tpl}
	{if $p.hint}
		<br/><em>{$p.hint|escape}</em>
	{/if}
	{include file=prefs/shared-dependencies.tpl}

{foreach from=$p.options key=value item=label name=loop}
	{jq}
if( ! $jq('#{{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}}').attr('checked') ) {
	$jq('#{{$p.preference|escape}}_childcontainer_{{$smarty.foreach.loop.index}}').hide();
}
$jq('#{{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}}').change( function() {
	if( $jq('#{{$p.id|cat:'_'|cat:$smarty.foreach.loop.index|escape}}').attr('checked') ) {
		show('#{{$p.preference|escape}}_childcontainer_{{$smarty.foreach.loop.index}}');
	}
} );
{/jq}
{/foreach}
</div>
