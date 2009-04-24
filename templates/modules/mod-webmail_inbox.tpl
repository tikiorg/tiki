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
		{if $prefs.feature_ajax}
			<a title="{tr}Refresh{/tr}" {ajax_href template="modules/mod-webmail_inbox.tpl" htmlelement="mod-webmail_inbox$module_position$module_ord"|escape function="doReloadWebmail"}tiki-webmail_ajax.php{/ajax_href}>
				{icon _id='arrow_refresh' class='webmail_refresh_icon'}
				{icon _id='img/spinner.gif' class='webmail_refresh_busy' style='display:none'}
			</a>
			<span class='webmail_refresh_message' style='display:none'></span>
		{else}
			<a title="{tr}Refresh (non-ajax){/tr}" href="{$request_uri}refresh_mail=1">{icon _id='arrow_refresh'}</a>
		{/if}
	</div>
	<div class="mod_webmail_list">
		{if isset($error)}<span class="error">{$error}</span>{/if}
		{if isset($module_params.date_format)}
			{assign var=date_format value=$module_params.date_format}
		{else}
			{assign var=date_format value="`$prefs.short_date_format` `$prefs.short_time_format`"}
		{/if}
		{section name=ix loop=$list}
			{assign var='date_value' value=$list[ix].timestamp|tiki_date_format:$date_format}
			{assign var='subject' value=$list[ix].subject}
			{assign var='sender' value=$list[ix].sender}
			{assign var='class' value="webmail_item"}
			{if $list[ix].isRead}{assign var=class value="$class webmail_read"}{/if}
			{if $list[ix].isFlagged}{assign var=class value="$class webmail_flagged"}{/if}
			{if $list[ix].isReplied}{assign var=class value="$class webmail_replied"}{/if}
			<div class="{$class} {cycle values="odd,even"}">
				{if $nonums != 'n'}
				<span class="mod_numbers">{$smarty.section.ix.index_next})</span>&nbsp;
				{/if}
				<a class="linkmodule tips300 webmail_subject" href="tiki-webmail.php?locSection=read&amp;msgid={$list[ix].msgid}"
						title="<span class='webmail_tip_title'><strong>{$subject}</strong><br />{tr}From{/tr}: <em>{$sender.name}</em> <tt>&amp;lt;{$sender.email}&amp;gt;</tt></span>|({$date_value})">
					{if $maxlen > 0}{* default value for maxlen param eq 26 *}
						{$subject|truncate:$maxlen:"...":true}
					{else}
						{$subject}
					{/if}
				</a>
				{if $module_params.showDescription eq 'y'}
					<div class="description"></div>
				{/if}
			</div>
		{sectionelse}
			<p>{tr}No mail found.{/tr}</p>
		{/section}
	</div>
</form>
{else}
	<span class="error">{$error}</span>
{/if}
{/tikimodule}
{/strip}