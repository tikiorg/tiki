{if $prefs.user_register_prettytracker eq 'y' and $prefs.user_register_prettytracker_tpl}
	{if isset($theChoiceGroup)}
		<input type="hidden" name="chosenGroup" value="{$theChoiceGroup|escape}" />
	{elseif isset($listgroups)}
		{foreach item=gr from=$listgroups}
			{if $gr.registrationChoice eq 'y'}
				<div class="registergroup">
					<input type="radio" name="chosenGroup" id="gr_{$gr.groupName}" value="{$gr.groupName|escape}" /> 
					<label for="gr_{$gr.groupName}">
						{if $gr.groupDesc}
							{tr}{$gr.groupDesc|escape}{/tr}
						{else}
							{$gr.groupName|escape}
						{/if}
					</label>
				</div>
			{/if}
		{/foreach}
	{/if}
{else}
	{* Groups *}
	{if isset($theChoiceGroup)}
		<input type="hidden" name="chosenGroup" value="{$theChoiceGroup|escape}" />
	{elseif isset($listgroups)}
		<tr>
			<td>{tr}Group{/tr}</td>
			<td>
				{foreach item=gr from=$listgroups}
					{if $gr.registrationChoice eq 'y'}
						<div class="registergroup">
							<input type="radio" name="chosenGroup" id="gr_{$gr.groupName}" value="{$gr.groupName|escape}" />
							<label for="gr_{$gr.groupName}">
								{if $gr.groupDesc}
									{tr}{$gr.groupDesc|escape}{/tr}
								{else}
									{$gr.groupName|escape}
								{/if}
							</label>
						</div>
					{/if}
				{/foreach}
			</td>
		</tr>
	{/if}
{/if}
