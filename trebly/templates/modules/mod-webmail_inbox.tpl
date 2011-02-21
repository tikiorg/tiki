{* $Id $ *}
{* params:
 *   autoloaddelay in seconds (default = 1, -1 = off)
 *
 *}
{strip}
{if !isset($tpl_module_title)}
	{if $nonums eq 'y'}
	{else}
		{eval var="{tr}Webmail inbox{/tr}" assign="tpl_module_title"}
	{/if}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="webmail_inbox" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $prefs.feature_webmail eq 'y'}
<form action="javascript:void(null);" onsubmit="return submitWebmail();" name="webmail_form">
	<div class="mod_webmail_heading">
		<div class="floatright">
			{*icon _id='resultset_first' class=''*}
			{if isset($prevstart) and $prevstart}
				<a href="#" onclick="doRefreshWebmail({$prevstart});return false;">{icon _id='resultset_previous' class=''}</a>
			{else}
				{icon _id='pics/trans.png' class=''}
			{/if}
			{if isset($nextstart) and $nextstart}
				<a href="#" onclick="doRefreshWebmail({$nextstart});return false;">{icon _id='resultset_next' class=''}</a>
			{else}
				{icon _id='pics/trans.png' class=''}
			{/if}
			{*icon _id='resultset_last' class=''*}
		</div>
		{if 0 and $prefs.feature_ajax}{* AJAX_TODO *}
			<a title="{tr}Refresh{/tr}" onclick="doRefreshWebmail(0, true);return false;">
				{icon _id='arrow_refresh' class='webmail_refresh_icon icon'}
				{icon _id='img/spinner.gif' class='webmail_refresh_busy icon' style='display:none'}
			</a>
			&nbsp;<span class='webmail_refresh_message' style='display:none'></span>
		{else}
			<a title="{tr}Refresh (non-ajax){/tr}" href="{$request_uri}refresh_mail=1">{icon _id='arrow_refresh'}</a>
		{/if}
	</div>
	<div class="webmail_list">
		{if isset($error)}<span class="error">{$error}</span>{/if}
		{if isset($module_params.date_format)}
			{assign var=date_format value=$module_params.date_format}
		{else}
			{assign var=date_format value="`$prefs.short_date_format` `$prefs.short_time_format`"}
		{/if}
		{section name=ix loop=$webmail_list}
			{assign var='date_value' value=$webmail_list[ix].timestamp|tiki_date_format:$date_format}
			{assign var='date_short' value=$webmail_list[ix].timestamp|tiki_date_format:"%H:%M"}
			{assign var='subject' value=$webmail_list[ix].subject}
			{assign var='sender' value=$webmail_list[ix].sender}
			{assign var='class' value="webmail_item"}
			{if $webmail_list[ix].isRead eq 'y'}{assign var=class value="$class webmail_read"}{/if}
			{if $webmail_list[ix].isFlagged eq 'y'}{assign var=class value="$class webmail_flagged"}{/if}
			{if $webmail_list[ix].isReplied eq 'y'}{assign var=class value="$class webmail_replied"}{/if}
			<div class="{cycle values='odd,even'} {$class}">
				{if $module_params.mode eq 'webmail' or $module_params.mode eq ''}
					{if $nonums != 'n'}
						<span class="mod_numbers">{$smarty.section.ix.index_next})</span>&nbsp;
					{/if}
					<a class="linkmodule tips300 webmail_subject" href="tiki-webmail.php?locSection=read&amp;msgid={$webmail_list[ix].msgid}"
							title="<span class='webmail_tip_title'><strong>{$subject}</strong><br />{tr}From:{/tr} <em>{$sender.name}</em> <tt>&amp;lt;{$sender.email}&amp;gt;</tt></span>|({$date_value})">
						{if $maxlen > 0}{* default value for maxlen param eq 26 *}
							{$subject|truncate:$maxlen:"...":true}
						{else}
							{$subject}
						{/if}
					</a>
				{elseif $module_params.mode eq 'groupmail'}
					<span class="mod_webmail_date">{$date_short}</span>&nbsp;
					{if !empty($webmail_list[ix].operator)}
						{if $webmail_list[ix].operator eq $user}
							<a class="button mod_webmail_action webmail_taken" onclick="doPutBackWebmail({$webmail_list[ix].msgid})" href="#">{$webmail_list[ix].operator}</a>&nbsp;
						{else}
							<span class="button mod_webmail_action webmail_taken">{$webmail_list[ix].operator}</span>&nbsp;
						{/if}
					{else}
						<a class="button mod_webmail_action" onclick="doTakeWebmail({$webmail_list[ix].msgid})" href="#">{tr}TAKE{/tr}</a>&nbsp;
					{/if}
					{if $sender.contactId gt 0}
						{if !empty($sender.wikiPage)}
							{self_link _script=$sender.wikiPage|sefurl _class="mod_webmail_from"}{$sender.email|truncate:17:"...":true}{/self_link}
						{else}
							{self_link _script='tiki-contacts.php' contactId=$sender.contactId _class="mod_webmail_from"}{$sender.email|truncate:17:"...":true}{/self_link}
						{/if}
						<div style="float: right;">{self_link _script='tiki-contacts.php' contactId=$sender.contactId _icon='user_gray' _width=12 _height=12}{tr}View contact{/tr}{/self_link}</div>
					{else}
						<span class="mod_webmail_from">{$sender.email|truncate:20:"...":true}</span>
					{/if}
					{assign var=tit value="<span class='webmail_tip_title'><strong>$subject</strong><br /></span>|{tr}From:{/tr} <em>`$sender.name`</em> &nbsp; <tt>&amp;lt;`$sender.email`&amp;gt;</tt><br /><small>[$date_value]</small>"}
					{self_link _script='tiki-webmail.php' msgid=$webmail_list[ix].msgid locSection='read' _noauto='y' _class='clearfix linkmodule tips300 webmail_subject' _title=$tit}
						{if $maxlen > 0}{* default value for maxlen param eq 26 *}
							{$subject|truncate:$maxlen:"...":true}
						{else}
							{$subject}
						{/if}
					{/self_link}
					{* self_link fails here because PHP_SELF is tiki-webmail_ajax.php, self_link needs to know the "real" page URL somehow *}
				{/if}
			</div>
		{sectionelse}
			{section name=foo loop=10}{* dummy loop to keep module height (approx) *}
				<div class="webmail_item_empty">
					&nbsp;<br />
					&nbsp;<br />
				</div>
			{/section}
		{/section}
	</div>
</form>
{else}
	<span class="error">{$error}</span>
{/if}
{/tikimodule}
{/strip}
