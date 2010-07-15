<div class="adminoptionbox" style="text-align: left;">
	<label for="{$p.id|escape}">{$p.name|escape}:</label>
	<select name="{$p.preference|escape}" id="{$p.id|escape}">
		{foreach from=$p.options key=value item=label}
			<option value="{$value|escape}"{if $value eq $p.value} selected="selected"{/if}>{$label|escape}</option>
		{/foreach}
	</select>
	{include file=prefs/shared-flags.tpl}
	{if $p.shorthint}
		<em>{$p.shorthint|simplewiki}</em>
	{/if}
	{if $p.hint}
		<br/><em>{$p.hint|simplewiki}</em>
	{/if}
	{include file=prefs/shared-dependencies.tpl}
	{jq}
if ($jq('.{{$p.preference|escape}}_childcontainer').length) {
	$jq('#{{$p.id|escape}}').change( function( e ) {
		$jq('.{{$p.preference|escape}}_childcontainer').hide();
		if( $jq(this).val().length ) {
			$jq('.{{$p.preference|escape}}_childcontainer.' + $jq(this).val()).show();
		}
	} ).change();
}
	{/jq}
</div>
