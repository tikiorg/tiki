{if $tiki_p_tracker_view_ratings eq 'y'}
	{if $context.list_mode eq 'csv'}
		{$field.value}/{$field.voteavg}
	{else}
		{strip}
			{capture name=stat}
				{if empty($field.numvotes)}
					{tr}Number of votes:{/tr} 0
				{else}
					{tr}Number of votes:{/tr} {$field.numvotes|default:"0"}, {tr}Average:{/tr} {$field.voteavg|default:"0"}
					{if $tiki_p_tracker_vote_ratings eq 'y'}
						, {if isset($field.my_rate) && $field.my_rate !== false}{tr}Your rating:{/tr} {$field.my_rate}{else}{tr}You did not vote yet{/tr}{/if}
					{/if}
				{/if}
			{/capture}
		{/strip}
		{capture name=myvote}{tr}My rating:{/tr} {$field.my_rate}{/capture}
		<span class="rating">
		<span style="white-space:nowrap">
		{section name=i loop=$field.rating_options}
			{if $field.mode eq 'radio'}{tr}{$field.labels[i]}{/tr}: {/if}
			{$star = 'display:none'}
			{$starselected = 'display:none'}
			{$starhalf = 'display:none'}
			{$starhalfselected = 'display:none'}
			{$starempty = 'display:none'}
			{$staremptyselected = 'display:none'}
			{if $field.numvotes && $field.voteavg >= $field.rating_options[i]}
				{if $field.my_rate !== false && $field.my_rate == $field.rating_options[i]}
					{$starselected = 'display:inline'}
				{else}
					{$star = 'display:inline'}
				{/if}
			{* showing half stars only works with the default iconset so far *}
			{elseif $field.numvotes && $field.rating_options[i] - $field.voteavg <= 0.5}
				{if $field.my_rate !== false && $field.my_rate == $field.rating_options[i]}
					{$starhalfselected = 'display:inline'}
				{else}
					{$starhalf = 'display:inline'}
				{/if}
			{else}
				{if $field.my_rate !== false && $field.my_rate == $field.rating_options[i]}
					{$staremptyselected = 'display:inline'}
				{else}
					{$starempty = 'display:inline'}
				{/if}
			{/if}
			{if ($tiki_p_tracker_vote_ratings eq 'y' && (!isset($field.my_rate) || $field.my_rate === false)) ||
			($tiki_p_tracker_revote_ratings eq 'y' && isset($field.my_rate) && $field.my_rate !== false)}
				{$endtag = '</a>'}
				{capture name=thisvote}{tr}Click to vote for this value:{/tr} {$field.rating_options[i]}{/capture}
				<a
				href="{$smarty.server.REQUEST_URI}"
				data-vote="{$field.rating_options[i]}"
				onclick="sendVote(this,{$item.itemId},{$field.fieldId},{$field.rating_options[i]});return false;"
				class="tips" title=":{$smarty.capture.thisvote}"
				>{/if}
					{icon name='star-selected' istyle="{$starselected}"}
					{icon name='star' istyle="{$star}"}
					{icon name='star-half-rating' istyle="{$starhalf}"}
					{icon name='star-half-selected' istyle="{$starhalfselected}"}
					{icon name='star-empty-selected' istyle="{$staremptyselected}"}
					{icon name='star-empty' istyle="{$starempty}"}{$endtag}
			{assign var='previousvote' value=$field.rating_options[i]}
		{/section}
		</span>
		{if $item.itemId}
			<small class="tips" title=":{tr}Votes{/tr}">
				({$field.numvotes})
			</small>
			{icon name='help' iclass='tips' ititle=":{$smarty.capture.stat}"}
		{/if}
		{if $tiki_p_tracker_revote_ratings eq 'y'}
			<a
				href="{$smarty.server.REQUEST_URI}"
				data-vote="0" onclick="sendVote(this,{$item.itemId},{$field.fieldId},'NULL');return false;"
				{if empty($field.my_rate) or not in_array($field.my_rate, $field.rating_options)} style="display:none;"{/if}
			>{icon name='delete' iclass='tips unvote' ititle=":{tr}Remove your rating{/tr}"}</a>
		{/if}
		</span>
	{/if}
{/if}
