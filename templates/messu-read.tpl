{title url="messu-read.php?msgId=$msgId" admpage="messages"}{tr}Read message{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
{include file='messu-nav.tpl'}
<br>
{if $prev}<a class="readlink" href="messu-read.php?offset={$offset}&amp;msgId={$prev}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Prev{/tr}</a>{/if}
{if $next}<a class="readlink" href="messu-read.php?offset={$offset}&amp;msgId={$next}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Next{/tr}</a>{/if}
<a class="readlink" href="messu-mailbox.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Return to mailbox{/tr}</a>
<br><br>
{if $legend}
	{$legend}
{else}
	<table>
		<tr>
			<td>
				<form method="post" action="messu-read.php">
					<input type="hidden" name="offset" value="{$offset|escape}">
					<input type="hidden" name="find" value="{$find|escape}">
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
					<input type="hidden" name="flag" value="{$flag|escape}">
					<input type="hidden" name="flagval" value="{$flagval|escape}">
					<input type="hidden" name="priority" value="{$priority|escape}">
					<input type="hidden" name="msgdel" value="{$msgId|escape}">
					{if $next}
						<input type="hidden" name="msgId" value="{$next|escape}">
					{elseif $prev}
						<input type="hidden" name="msgId" value="{$prev|escape}">
					{else}
						<input type="hidden" name="msgId" value="">
					{/if}
					<input type="submit" class="btn btn-warning btn-sm" name="delete" value="{tr}Delete{/tr}">
				</form>
			</td>
			<td>
				<form method="post" action="messu-compose.php">
					<input type="hidden" name="offset" value="{$offset|escape}">
					<input type="hidden" name="msgId" value="{$msgId|escape}">
					<input type="hidden" name="find" value="{$find|escape}">
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
					<input type="hidden" name="flag" value="{$flag|escape}">
					<input type="hidden" name="priority" value="{$priority|escape}">
					<input type="hidden" name="flagval" value="{$flagval|escape}">
					<input type="hidden" name="to" value="{$msg.user_from|escape}">
					<input type="hidden" name="subject" value="{tr}Re:{/tr} {$msg.subject|escape}">
					<input type="hidden" name="body" value="{$msg.body|quoted:$quote_format:$msg.user_from|escape}">
					<input type="hidden" name="replyto_hash" value="{$msg.hash}">
					<input type="submit" class="btn btn-default btn-sm" name="reply" value="{tr}Reply{/tr}">
				</form>
			</td>
			<td>
				<form method="post" action="messu-compose.php">
					<input type="hidden" name="offset" value="{$offset|escape}">
					<input type="hidden" name="find" value="{$find|escape}">
					<input type="hidden" name="msgId" value="{$msgId|escape}">
					<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
					<input type="hidden" name="flag" value="{$flag|escape}">
					<input type="hidden" name="priority" value="{$priority|escape}">
					<input type="hidden" name="flagval" value="{$flagval|escape}">
					{if $msg.user_reply_to eq ''}
						<input type="hidden" name="to" value="{$msg.user_from|escape};{$msg.user_to|escape}" />
						{else}
						<input type="hidden" name="to" value="{$msg.user_reply_to|escape};{$msg.user_to|escape}" />
					{/if}
					<input type="hidden" name="cc" value="{$msg.user_cc|escape}" />
					<input type="hidden" name="subject" value="{tr}Re:{/tr} {$msg.subject|escape}">
					<input type="hidden" name="body" value="{$msg.body|quoted:$quote_format:$msg.user_from|escape}">
					<input type="hidden" name="replyto_hash" value="{$msg.hash}">
					<input type="submit" class="btn btn-default btn-sm" name="replyall" value="{tr}replyall{/tr}">
				</form>
			</td>
		</tr>
	</table>
	<div class="messureadflag">
		{if $msg.isFlagged eq 'y'}
			{icon name='flag' alt="flag"}<a class="link" href="messu-read.php?offset={$offset}&amp;action=isFlagged&amp;actionval=n&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Unflag{/tr}</a>
		{else}
			<a class="link" href="messu-read.php?offset={$offset}&amp;action=isFlagged&amp;actionval=y&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}Flag this message{/tr}</a>
		{/if}
	</div>
	<div class="messureadhead">
		<table>
			<tr><td style="font-weight:bold;">{tr}From:{/tr}</td><td>{$msg.user_from|username}</td></tr>
			<tr><td style="font-weight:bold;">{tr}To:{/tr}</td><td>{$msg.user_to|escape}</td></tr>
			<tr><td style="font-weight:bold;">{tr}Cc:{/tr}</td><td>{$msg.user_cc|escape}</td></tr>
			<tr><td style="font-weight:bold;">{tr}Subject:{/tr}</td><td>{$msg.subject|escape}</td></tr>
			<tr><td style="font-weight:bold;">{tr}Date:{/tr}</td><td>{$msg.date|tiki_short_datetime}</td></tr><!--date_format:"%a %b %Y [%H:%I]"-->
		</table>
	</div>
	<div class="messureadbody">
		{$msg.parsed}
	</div>
{/if}
