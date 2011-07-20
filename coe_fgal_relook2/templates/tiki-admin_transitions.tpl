{title help="Transitions"}{tr}Transitions{/tr}{/title}

{tabset}
	{tab name="{tr}Subset Selection{/tr}"}
		<form method="post" action="tiki-admin_transitions.php?action=subset" style="text-align: left;">
			<fieldset>
				<legend>{tr}Type{/tr}</legend>

				<p>
					{if $prefs.feature_categories eq 'y'}
					<input type="radio" name="transition_mode" value="category" id="transition-mode-category"{if $transition_mode eq 'category'} checked="checked"{/if}/>
					<label for="transition-mode-category">{tr}Category{/tr}</label>
					{/if}

					<input type="radio" name="transition_mode" value="group" id="transition-mode-group"{if $transition_mode eq 'group' or $prefs.feature_categories ne 'y'} checked="checked"{/if}/>
					<label for="transition-mode-group">{tr}Group{/tr}</label>
				</p>
			</fieldset>

			{if $prefs.feature_categories eq 'y'}
			<fieldset id="transition-category-selection">
				<legend>{tr}Category Selection{/tr}</legend>
				{$cat_tree}
			</fieldset>
			{/if}

			<fieldset id="transition-group-selection">
				<legend>{tr}Group Selection{/tr}</legend>
				<ul id="transition-group-list">
					{if $transition_mode eq 'group'}
						{foreach from=$available_states item=state}
							<li>
								<input type="hidden" name="groups[]" value="{$state|escape}"/>
								{$state|escape}
								{icon _id=cross class="removeitem"}
							</li>
						{/foreach}
					{/if}
				</ul>
				<p>
					<label for="transition-group-auto">{tr}Type in a group name and click "enter"{/tr}</label>
					<input type="text" id="transition-group-auto"/> 
				</p>
			</fieldset>

			<p>
				{tr}Once you have selected at least two, click:{/tr} <input type="submit" value="{tr}Select{/tr}"/> {tr}and then, click the "New/Edit" tab which will appear above.{/tr}
			</p>
		</form>
		{jq}
			var blocks = $('#transition-group-selection, #transition-category-selection');
			$(':radio[name=transition_mode]').change( function( e ) {
				if( $(this).attr('checked') ) {
					blocks.hide();
					blocks.filter( '#transition-' + $(this).val() + '-selection' ).show();
				}
			} ).change();

			$('#transition-group-auto')
				.tiki('autocomplete','groupname')
				.keypress( function( e ) {
					if( e.which !== 13 ) {
						return;
					}
					e.preventDefault();
					if( $(this).val() === '' ) {
						return;
					}
					$('#transition-group-list').append( 
						$('<li/>').text( $(this).val() )
							.append( $('<input type="hidden" name="groups[]"/>').val( $(this).val() ) )
							.append( $('{{icon _id=cross class="removeitem"}}') )
					);
					$(this).val('');
				} );
			$('#transition-group-list .removeitem').live( 'click', function( e ) {
				$(this).parent().remove();
			} );
		{/jq}
	{/tab}
	{if $available_states|@count > 0}
	{tab name="{tr}Transitions{/tr}"}
		{$headerlib->add_dracula()}
		<div id="graph-canvas" class="graph-canvas" data-graph-nodes="{$graph_nodes|escape}" data-graph-edges="{$graph_edges|escape}"></div>
		<a href="#" id="graph-draw" class="button">{tr}Draw Transition Diagram{/tr}</a>
		{jq}
		$('#graph-draw').click( function( e ) {
			$(this).hide();
			$('#graph-canvas').drawGraph();
			return false;
		} );
		{/jq}
		<table class="normal">
			<thead>
				<tr>
					<th>{tr}Label{/tr}</th>
					<th>{tr}From{/tr}</th>
					<th>{tr}To{/tr}</th>
					<th>{tr}Guards{/tr}</th>
					<th>{tr}Actions{/tr}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$transitions item=trans}
					<tr>
						<td class="text">{$trans.name|escape}</td>
						<td class="text">{$trans.from_label|escape} {if $trans.preserve} - <em>{tr}preserved{/tr}</em>{/if}</td>
						<td class="text">{$trans.to_label|escape}</td>
						<td class="integer">{self_link transitionId=$trans.transitionId action=edit cookietab=4}{$trans.guards|@count|escape}{/self_link}</td>
						<td class="action">
							{self_link transitionId=$trans.transitionId action=edit cookietab=3}{icon _id=page_edit alt="{tr}Edit{/tr}"}{/self_link}
							{self_link transitionId=$trans.transitionId action=remove}{icon _id=cross alt="{tr}Remove{/tr}"}{/self_link}
							<a class="link" href="tiki-objectpermissions.php?objectName={$trans.name|escape:url}&amp;objectType=transition&amp;permType=transition&amp;objectId={$trans.transitionId|escape:"url"}">
								{icon _id='key' class=titletips title="{tr}Assign permissions for transition{/tr}" alt="{tr}Permissions{/tr}"}
							</a>
						</td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="4">{tr}No transitions{/tr}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{/tab}
		
	{tab name="{tr}New / Edit{/tr}"}
		<form method="post" action="tiki-admin_transitions.php?action={if $selected_transition}edit{else}new{/if}&amp;cookietab=2" style="text-align: left;">
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

	{if $selected_transition}
		{tab name="{tr}Guards{/tr}"}
			<table class="normal">
				<thead>
					<tr>
						<th>{tr}Type{/tr}</th>
						<th>{tr}Count{/tr}</th>
						<th>{tr}Members{/tr}</th>
						<th>{tr}Actions{/tr}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$guards item=guard key=key}
						<tr>
							<td>{$guard.type|escape}</td>
							<td>{$guard.count|escape}</td>
							<td>
								<ul>
									{foreach from=$guard.members item=name}
										<li>{$name|escape}</li>
									{/foreach}
								</ul>
							</td>
							<td>
								{self_link transitionId=$selected_transition.transitionId action=removeguard guard=$key cookietab=4}{icon _id=cross}{/self_link}
							</td>
						</tr>
					{foreachelse}
						<tr>
							<td colspan="4">{tr}No guards on this transition.{/tr}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
			<form method="post" action="tiki-admin_transitions.php?action=addguard&amp;transitionId={$selected_transition.transitionId|escape}&amp;cookietab=4" style="text-align: left;">
				<h2>{tr}New Guard{/tr}</h2>
				<fieldset>
					<legend>{tr}General{/tr}</legend>
					<p>
						<label for="guard-type">{tr}Type{/tr}</label>
						<select id="guard-type" name="type">
							<option value="exactly">{tr}Exactly{/tr}</option>
							<option value="atLeast">{tr}At Least{/tr}</option>
							<option value="atMost">{tr}At Most{/tr}</option>
						</select>
					</p>
					<p>
						<label for="guard-count">{tr}Count{/tr}</label>
						<input type="text" name="count"/>
					</p>
				</fieldset>
				<fieldset>
					<legend>{tr}States{/tr}</legend>
					{foreach from=$available_states item=label key=value}
						<p>
							<input type="checkbox" name="states[]" value="{$value|escape}" id="guard-state-{$value|escape}"/>
							<label for="guard-state-{$value|escape}">{$label|escape}</label>
						</p>
					{/foreach}
				</fieldset>
				<p>
					<input type="submit" value="{tr}Add{/tr}"/>
				</p>
			</form>
		{/tab}
	{/if}

	{/if}
{/tabset}
