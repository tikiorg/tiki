{if $p.helpurl}
	<a href="{$p.helpurl|escape}" target="tikihelp" class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
		{icon _id=help alt=''}
	</a>
{elseif $p.description}
	<span class="tikihelp" title="{$p.name|escape}: {$p.description|escape}">
		{icon _id=information alt=''}
	</span>
{/if}
{if $p.warning}
	<a href="" target="tikihelp" class="tikihelp" title="{tr}Warning{/tr}: {$p.warning|escape}">
		{icon _id=error alt=''}
	</a>
{/if}

<input class="pref-reset system" type="checkbox" name="lm_reset[]" value="{$p.preference|escape}"/>

{jq}
$jq('.pref-reset')
	.change( function() {
		$jq(this).closest('.adminoptionbox').find(':input')
			.not('.system').attr( 'disabled', $jq(this).attr('checked') );
	} )
	.hide()
	.wrap('<span/>')
	.closest('span')
		.append('{{icon _id=shading alt="{tr}Reset to default{/tr}" href=#}}')
		.find('a')
			.click( function() {
				var box = $jq(this).closest('span').find(':checkbox');

				box.attr('checked', box.filter(':checked').length == 0).change();
				return false;
			} );
{/jq}

{$p.pages}
