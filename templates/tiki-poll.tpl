{if !empty($msg_poll)}
	{remarksbox type="errors"}
		{$msg_poll}
	{/remarksbox}
{/if}

{if $showtitle ne 'n'}{$menu_info.title|escape}<br>{/if}
<form method="post" action="{$ownurl}">
	<input type="hidden" name="polls_pollId" value="{$menu_info.pollId|escape}">
	{if !empty($showresult) && $showresult ne 'link'}<input type="hidden" name="showresult" value="{$showresult|escape}">{/if}
	{if $tiki_p_vote_poll ne 'n' && ($user || $prefs.feature_poll_anonymous == 'y' || $prefs.feature_antibot eq 'y')}
		{section name=ix loop=$channels}
			<label><input type="radio" name="polls_optionId" value="{$channels[ix].optionId|escape}"{if $polls_optionId == $channels[ix].optionId} checked="checked"{/if}>{tr}{$channels[ix].title|escape}{/tr}</label><br>
		{/section}
	{else}
		<ul>
			{section name=ix loop=$channels}
				<li>{tr}{$channels[ix].title|escape}{/tr}</li>
			{/section}
		</ul>
	{/if}
	<div align="center">
		{if $prefs.feature_antibot eq 'y' && $user eq ''}
			{include file='antibot.tpl' antibot_table='n'}
		{/if}
		{if $tiki_p_vote_poll ne 'n' && ($user || $prefs.feature_poll_anonymous == 'y' || $prefs.feature_antibot eq 'y')}
			<input type="submit" class="btn btn-default btn-sm" name="pollVote" value="{tr}vote{/tr}"><br>
		{/if}
		{if $tiki_p_view_poll_results == 'y' and $showresult ne 'always' and $showresult ne 'voted'}
			<a class="linkmodule" href="tiki-poll_results.php?pollId={$menu_info.pollId}">{tr}View Results{/tr}</a><br>
			({tr}Votes:{/tr} {$menu_info.votes})
		{/if}
	</div>
	{if $prefs.feature_poll_comments and $comments_cant and !isset($module_params)}
		<br>
		{include file='comments_button.tpl'}
	{/if}
</form>
