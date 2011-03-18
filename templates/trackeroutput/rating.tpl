{if $tiki_p_tracker_view_ratings eq 'y'}
	{if $context.list_mode eq 'csv'}
		{$field.value}/{$field.voteavg}
	{else}
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
		{capture name=myvote}
			{tr}My rating:{/tr} {$field.my_rate}
		{/capture}
		<span class="rating">
		<span style="white-space:nowrap">
		{section name=i loop=$field.options_array}
			{if $tiki_p_tracker_vote_ratings eq 'y' and isset($field.my_rate) and $field.options_array[i] === $field.my_rate}
				<b class="highlight">
					{if $field.numvotes && $field.voteavg >= $field.options_array[i]}
				   		{icon _id='star' alt=$field.options_array[i] title=$smarty.capture.myvote}
					{else}
						{icon _id='star_grey' alt=$field.options_array[i] title=$smarty.capture.myvote}
					{/if}
				</b>
			{else}
				{if ($tiki_p_tracker_vote_ratings eq 'y' && (!isset($field.my_rate) || $field.my_rate === false)) ||
					($tiki_p_tracker_revote_ratings eq 'y' && isset($field.my_rate) && $field.my_rate !== false)}
					{capture name=thisvote}{tr}Click to vote for this value:{/tr} {$field.options_array[i]}{/capture}
					<a href="{$smarty.server.REQUEST_URI}{if empty($smarty.server.QUERY_STRING)}?{else}&amp;{/if}itemId={$item.itemId}&amp;ins_{$field.fieldId}={$field.options_array[i]}&amp;vote=y">
				{/if}
				{if $field.numvotes && $field.voteavg >= $field.options_array[i]}
					{icon _id='star' alt=$field.options_array[i] title=$smarty.capture.thisvote}
				{else}
					{icon _id='star_grey' alt=$field.options_array[i] title=$smarty.capture.thisvote}
				{/if}
				{if ($tiki_p_tracker_vote_ratings eq 'y' && (!isset($field.my_rate) || $field.my_rate === false)) ||
					($tiki_p_tracker_revote_ratings eq 'y' && isset($field.my_rate) && $field.my_rate !== false)}
					</a>
				{/if}	
			{/if}
			{assign var='previousvote' value=$field.options_array[i]}
		{/section}
		</span>
		{if $item.itemId}
			<small title="{tr}Votes{/tr}">
				({$field.numvotes})
			</small>
			{icon _id='help' title=$smarty.capture.stat}
		{/if}
		{if $tiki_p_tracker_revote_ratings eq 'y' and  isset($field.my_rate) and in_array($field.my_rate, $field.options_array)}
			<a href="{$smarty.server.REQUEST_URI}{if empty($smarty.server.QUERY_STRING)}?{else}&amp;{/if}itemId={$item.itemId}&amp;ins_{$field.fieldId}=NULL&amp;vote=y" title="{tr}Clik to delete your vote{/tr}">x</a>
		{/if}
		<span>
	{/if}
{/if}
