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
					<a class="btn btn-link" href="messu-read_sent.php?offset={$offset}&amp;msgId={$prev}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-left" aria-hidden="true"></i>{/tr}</a>
				{else}
					<a class="btn btn-link disabled" href="messu-read_sent.php?offset={$offset}&amp;msgId={$prev}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-left" aria-hidden="true"></i>{/tr}</a>
				{/if}
			</div>
			<div class="col-xs-4 col-sm-2">
				{if $next}
					<a class="btn btn-link" href="messu-read_sent.php?offset={$offset}&amp;msgId={$next}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-right" aria-hidden="true"></i>{/tr}</a>
				{else}
					<a class="btn btn-link disabled" href="messu-read_sent.php?offset={$offset}&amp;msgId={$next}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-arrow-right" aria-hidden="true"></i>{/tr}</a>
				{/if}
			</div>
			<div class="col-xs-4 col-sm-2">
				{if $msg.isFlagged eq 'y'}
					<a class="btn btn-link" href="messu-read_sent.php?offset={$offset}&amp;action=isFlagged&amp;actionval=n&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-flag" aria-hidden="true"></i>{/tr}</a>
				{else}
					<a class="btn btn-link" href="messu-read_sent.php?offset={$offset}&amp;action=isFlagged&amp;actionval=y&amp;msgId={$msgId}&amp;sort_mode={$sort_mode}&amp;find={$find|escape:"url"}&amp;flag={$flag}&amp;priority={$priority}&amp;flagval={$flagval}">{tr}<i class="fa fa-flag-o" aria-hidden="true"></i>{/tr}</a>
				{/if}
			</div>
		</div>
	</div>
    <div class="col-xs-3 col-xs-offset-4 col-md-2 col-md-offset-5" style="padding-top: 4px;">
				<form method="post" action="messu-read_sent.php">
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
					<input type="submit" class="btn btn-default btn-sm pull-right" name="delete" value="{tr}Delete{/tr}">
				</form>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="messureadhead">
            <table>
                <tr><td style="font-weight:bold;">{tr}From:{/tr}</td><td>{$msg.user_from|escape}</td></tr>
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
