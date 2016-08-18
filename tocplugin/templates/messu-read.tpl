<div class="row"><div class="col-xs-12">{title url="messu-read.php?msgId=$msgId" admpage="messages"}{tr}Read message{/tr}{/title}</div></div>
<div class="row"><div class="col-xs-12">{include file='tiki-mytiki_bar.tpl'}</div></div>
<div class="row"><div class="col-xs-12">{include file='messu-nav.tpl'}</div></div>
		<br>
{if $legend}
	{$legend}
{else}
<div class="row" style="padding-bottom:10px;">
	<div class="col-xs-4 col-sm-5">
		<div class="row">
			<div class="col-xs-4 col-sm-2">
				{if $prev}
					<a class="btn btn-link" title="Previous Message" href="messu-read.php?offset={$offset}&amp;msgId={$prev}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-left" aria-hidden="true"></i>{/tr}</a>
				{else}
					<a class="btn btn-link disabled" title="Next Message" href="messu-read.php?offset={$offset}&amp;msgId={$prev}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-left" aria-hidden="true"></i>{/tr}</a>
				{/if}
			</div>
			<div class="col-xs-4 col-sm-2">
				{if $next}
					<a class="btn btn-link" href="messu-read.php?offset={$offset}&amp;msgId={$next}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-right" aria-hidden="true"></i>{/tr}</a>
				{else}
					<a class="btn btn-link disabled" href="messu-read.php?offset={$offset}&amp;msgId={$next}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-right" aria-hidden="true"></i>{/tr}</a>
				{/if}
			</div>
			<div class="col-xs-4 col-sm-2">
				{if $msg.isFlagged eq 'y'}
					<a class="btn btn-link" href="messu-read.php?offset={$offset}&amp;action=isFlagged&amp;actionval=n&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-flag" aria-hidden="true"></i>{/tr}</a>
				{else}
					<a class="btn btn-link" href="messu-read.php?offset={$offset}&amp;action=isFlagged&amp;actionval=y&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-flag-o" aria-hidden="true"></i>{/tr}</a>
				{/if}
			</div>
		</div>
	</div>
	<div class="col-xs-8 col-sm-7" style="padding-top: 4px;">
		<div class="col-xs-4 col-sm-3 col-sm-offset-3 col-lg-2 col-lg-offset-6">
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
					<input type="submit" class="btn btn-default btn-sm pull-right" name="reply" value="{tr}Reply{/tr}">
				</form>
		</div>
		<div class="col-xs-4 col-sm-3 col-lg-2">
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
					<button type="submit" class="btn btn-default btn-sm pull-right" name="replyall" value="{tr}replyall{/tr}">Reply All</button>
				</form>
		</div>
		<div class="col-xs-4 col-sm-3 col-lg-2">
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
				<input type="submit" class="btn btn-warning btn-sm pull-right" name="delete" value="{tr}Delete{/tr}">
			</form>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
	<div class="messureadhead">
		<table>
			<tr><td style="font-weight:bold;">{tr}From:{/tr}</td><td>{$msg.user_from|username}</td></tr>
			<tr><td style="font-weight:bold;">{tr}To:{/tr}</td><td>{$msg.user_to|escape}</td></tr>
			<tr><td style="font-weight:bold;">{tr}Cc:{/tr}</td><td>{$msg.user_cc|escape}</td></tr>
			<tr><td style="font-weight:bold;">{tr}Subject:{/tr}</td><td>{$msg.subject|escape}</td></tr>
			<tr><td style="font-weight:bold;">{tr}Date:{/tr}</td><td>{$msg.date|tiki_short_datetime}</td></tr><!--date_format:"%a %b %Y [%H:%I]"-->
		</table>
	</div>
	</div>
	<div class="col-xs-12">
	<div class="messureadbody">
		{$msg.parsed}
	</div>
	</div>
</div>
{/if}
<br><br><br>