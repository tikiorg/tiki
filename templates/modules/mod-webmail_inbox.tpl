{* $Id $ *}
{strip}
{if !isset($tpl_module_title)}
	{if $nonums eq 'y'}
	{else}
		{eval var="{tr}Webmail inbox{/tr}" assign="tpl_module_title"}
	{/if}
{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="webmail_inbox" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $prefs.feature_webmail eq 'y'}
	<div class="mod_webmail_heading">
		{*if $prefs.feature_ajax*}
			<a title="{tr}Refresh{/tr}" {ajax_href template="modules/mod-webmail_inbox.tpl" htmlelement="tiki-center"}modules/mod-webmail_inbox.php{/ajax_href}>{icon _id='arrow_refresh'}</a>
		{*else*}
			<a title="{tr}Refresh (non-ajax){/tr}" href="{$request_uri}refresh_mail=1">{icon _id='arrow_refresh'}</a>
		{*/if*}
	</div>
	{if isset($error)}<span class="error">$error<span>{/if}
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
		<div class="$class">
			{if $nonums != 'y'}
			<span class="mod_numbers">{$smarty.section.ix.index_next})</span>&nbsp;
			{/if}
			<a class="linkmodule tips webmail_subject" href="tiki-webmail.php?locSection=read&amp;msgid={$list[ix].msgid}" title="{tr}From{/tr}: <strong>{$sender.name}</strong> &amp;lt;{$sender.email}&amp;gt;|({$date_value})">
				{if $maxlen > 0}{* default value for maxlen param eq 26 *}
					{$subject|truncate:$maxlen:"...":true|escape}
				{else}
					{$subject|escape}
				{/if}
			</a>
			{if $module_params.showDescription eq 'y'}
				<div class="description"></div>
			{/if}
		</div>
	{sectionelse}
		<p>{tr}No mail found.{/tr}</p>
	{/section}
{else}
	{$error}
{/if}
{/tikimodule}
{/strip}