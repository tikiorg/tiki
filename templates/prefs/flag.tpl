<div class="adminoptionbox">
	<div class="adminoption">
		<input id="{$p.id|escape}" type="checkbox" name="{$p.preference|escape}" {if $p.value eq 'y'}checked="checked" {/if} {if ! $p.available}disabled="disabled"{/if}/>
	</div>
	<div class="adminoptionlabel" >
		<label for="{$p.id|escape}">{$p.name|escape}</label>
		{include file=prefs/shared-flags.tpl}
		{if $p.hint}
			<br/><em>{$p.hint|escape}</em>
		{/if}
	</div>
	{include file=prefs/shared-dependencies.tpl}
	{jq}
$jq('#{{$p.id|escape}}').change( function() {
	if( $jq('#{{$p.id|escape}}').attr('checked') || $jq('#{{$p.id|escape}}').attr('disabled') ) {
		show('{{$p.preference|escape}}_childcontainer');
	} else {
		hide('{{$p.preference|escape}}_childcontainer');
	}
} ).change();
{/jq}
</div>
