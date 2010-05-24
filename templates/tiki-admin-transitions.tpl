{title help="Category Transitions"}{tr}Transitions{/tr}{/title}

{tabset}
	{tab name="{tr}Subset Selection{/tr}"}
		<form method="post" action="tiki-admin_transitions.php?action=subset" style="text-align: left;">
			<fieldset>
				<legend>{tr}Type{/tr}</legend>

				<p>
					<input type="radio" name="transition_mode" value="category" id="transition-mode-category"{if $transition_mode eq 'category'} checked="checked"{/if}/>
					<label for="transition-mode-category">{tr}Category{/tr}</label>

					<input type="radio" name="transition_mode" value="group" id="transition-mode-group"{if $transition_mode eq 'group'} checked="checked"{/if}/>
					<label for="transition-mode-group">{tr}Group{/tr}</label>
				</p>
			</fieldset>

			<fieldset id="transition-category-selection">
				<legend>{tr}Category Selection{/tr}</legend>
				{$cat_tree}
			</fieldset>

			<fieldset id="transition-group-selection">
				<legend>{tr}Group Selection{/tr}</legend>
				TODO
			</fieldset>

			<p>
				<input type="submit" value="{tr}Select{/tr}"/>
			</p>
		</form>
		{jq}
			var blocks = $jq('#transition-group-selection, #transition-category-selection');
			$jq(':radio[name=transition_mode]').change( function( e ) {
				if( $jq(this).attr('checked') ) {
					blocks.hide();
					blocks.filter( '#transition-' + $jq(this).val() + '-selection' ).show();
				}
			} ).change();
		{/jq}
	{/tab}
	{if $available_states|@count > 0}
	{tab name="{tr}Transitions{/tr}"}
		<table class="data">
			<thead>
				<tr>
					<th>{tr}Label{/tr}</th>
					<th>{tr}From{/tr}</th>
					<th>{tr}To{/tr}</th>
					<th>{tr}Actions{/tr}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$transitions item=trans}
					<tr>
						<td>{$trans.name|escape}</td>
						<td>{$trans.from_label|escape} {if $trans.preserve} - <em>{tr}preserved{/tr}</em>{/if}</td>
						<td>{$trans.to_label|escape}</td>
						<td>
							{self_link transitionId=$trans.transitionId action=edit}{icon _id=page_edit alt="{tr}Edit{/tr}"}{/self_link}
							{self_link transitionId=$trans.transitionId action=remove}{icon _id=cross alt="{tr}Remove{/tr}"}{/self_link}
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="4">{tr}No transitions{/tr}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		
		<form method="post" action="tiki-admin_transitions.php?action={if $selected_transition}edit{else}new{/if}" style="text-align: left;">
			{if $selected_transition}
				<h2>
					{tr 0=$selected_transition.name}Edit <em>%0</em>{/tr}
					<input type="hidden" name="transitionId" value="{$selected_transition.transitionId|escape}"/>
					(<a href="tiki-admin_transitions.php">{tr}Create new{/tr}</a>)
				</h2>
			{else}
				<h2>{tr}New transition{/tr}</h2>
			{/if}
			<fieldset>
				<legend>{tr}General{/tr}</legend>
				<p>
					<label for="new-transition-name">{tr}Label{/tr}</label>
					<br/>
					<input type="text" name="label" {if $selected_transition}value="{$selected_transition.name|escape}"{/if}/>
				</p>
				<p>
					<input type="checkbox" name="preserve" value="y" id="new-transition-preserve" {if $selected_transition && $selected_transition.preserve}checked="checked"{/if}/>
					<label for="new-transition-preserve">{tr}Upon trigger, preserve the initial state{/tr}</label>
				</p>
			</fieldset>
			<fieldset style="float: left; width: 48%;">
				<legend>{tr}From{/tr}</legend>
				{foreach from=$available_states item=label key=value}
					<p>
						<input type="radio" name="from" value="{$value|escape}" id="state-from-{$value|escape}"{if $selected_transition && $selected_transition.from eq $value} checked="checked"{/if}/>
						<label for="state-from-{$value|escape}">{$label|escape}</label>
					</p>
				{/foreach}
			</fieldset>
			<fieldset style="float: left; width: 48%;">
				<legend>{tr}To{/tr}</legend>
				{foreach from=$available_states item=label key=value}
					<p>
						<input type="radio" name="to" value="{$value|escape}" id="state-to-{$value|escape}"{if $selected_transition && $selected_transition.to eq $value} checked="checked"{/if}/>
						<label for="state-to-{$value|escape}">{$label|escape}</label>
					</p>
				{/foreach}
			</fieldset>
			<p>
				<input type="submit" value="{tr}Save{/tr}"/>
			</p>
		</form>
	{/tab}
	{/if}
{/tabset}
