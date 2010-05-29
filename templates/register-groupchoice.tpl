	
	{* Custom fields *}
	{section name=ir loop=$customfields}
		{if $customfields[ir].show}
				<tr>
					<td class="form"><label for="{$customfields[ir].prefName}">{tr}{$customfields[ir].label}:{/tr}</label></td>
					<td class="form"><input type="{$customfields[ir].type}" name="{$customfields[ir].prefName}" value="{$customfields[ir].value}" size="{$customfields[ir].size}" id="{$customfields[ir].prefName}" /></td>
				</tr>
		{/if}
	{/section}
      
    {* Groups *}
	{if isset($theChoiceGroup)}
				<input type="hidden" name="chosenGroup" value="{$theChoiceGroup|escape}" />
	{elseif isset($listgroups)}
				<tr>
					<td class="formcolor">{tr}Group{/tr}</td>
					<td class="formcolor">
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
