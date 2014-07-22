<form method="post" action="">
	<input type="hidden" name="wp_transition_obj" value="{$wp_transition_obj|escape}">
	{foreach from=$wp_transitions item=trans}
		<div>
			<input id="transition-{$trans.transitionId|escape}" type="radio" name="wp_transition" value="{$trans.transitionId|escape}" {if ! $trans.enabled}disabled="disabled"{/if}>
			<label for="transition-{$trans.transitionId|escape}">{$trans.name|escape}</label>
			{if ! $trans.enabled}
				<a href="#trans{$trans.transitionId|escape}" class="mouseover">Why?</a>
				<div id="trans{$trans.transitionId|escape}">
					{foreach item=reason from=$trans.explain}
						{if $reason.class eq 'missing'}
							<p>{tr _0=$reason.count}Missing %0 of the following categories:{/tr}</p>
						{elseif $reason.class eq 'extra'}
							<p>{tr _0=$reason.count}%0 extra of the following categories:{/tr}</p>
						{elseif $reason.class eq 'unknown'}
							<p>{tr _0=$reason.count}Unknown comparison:{/tr}</p>
						{elseif $reason.class eq 'invalid'}
							<p>{tr _0=$reason.count}Impossible condition, %0 of:{/tr}</p>
						{/if}
						<ul>
							{foreach from=$reason.set item=state}
								<li>{categoryName id=$state}</li>
							{/foreach}
						</ul>
					{/foreach}
				</div>
			{/if}
		</div>
	{/foreach}
	{jq}{literal}
		$('.mouseover:not(.done)')
			.addClass('done')
			.each( function( k, e ) {
				$(e.href.substr(e.href.lastIndexOf('#'))).hide();
			} )
			.click( function( e ) {
				e.preventDefault();
				$(this.href.substr(this.href.lastIndexOf('#'))).toggle('fast');
			} );
	{/literal}{/jq}
	<div><input type="submit" class="btn btn-default btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}"></div>
</form>

