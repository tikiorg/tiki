<div class="adminoptionbox">
	<div class="adminoption">
		<input id="{$p.id|escape}" type="checkbox" name="{$p.preference|escape}" {if $p.value eq 'y'}checked="checked"{/if}/>
	</div>
	<div class="adminoptionlabel" >
		<label for="{$p.id|escape}">{$p.name|escape}</label>
		{include file=prefs/shared-flags.tpl}
	</div>
	{include file=prefs/shared-dependencies.tpl}
	{jq}
	{literal}
	(function(){
	{/literal}
	var id = '#{$p.id|escape}';
	var block = '{$p.preference|escape}_childcontainer';
	{literal}
	if( ! $jq(id).attr('checked') ) {
		$jq('#' + block).hide();
	}
	$jq(id).change( function() {
		if( $jq(id).attr('checked') ) {
			show(block);
		} else {
			hide(block);
		}
	} );
	})();
	{/literal}{/jq}
</div>
