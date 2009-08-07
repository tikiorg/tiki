<form method="post" action="">
	{foreach from=$datachannel_fields key=name item=label}
		<div>
			{$label|escape}: <input type="text" name="{$name|escape}"/>
		</div>
	{/foreach}
	<div>
		<input type="hidden" name="datachannel_execution" value="{$datachannel_execution|escape}"/>
		<input type="submit" value="{tr}Go{/tr}"/>
	</div>
</form>
