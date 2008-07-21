{* $Id$ *}
<h1><a class="pagetitle" href="tiki-user_watches.php">{tr}User Watches{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}User+Watches" target="tikihelp" class="tikihelp" title="{tr}User Watches{/tr}">
{icon _id='help'}</a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-user_watches.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}User Watches tpl{/tr}">
{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>
{if $prefs.feature_ajax ne 'y' && $prefs.feature_mootools ne 'y'}
{include file=tiki-mytiki_bar.tpl}
{/if}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}Use "watches" to monitor wiki pages or other objects.{/tr} {tr}Watch new items by clicking the {icon _id=eye} button on specific pages.{/tr}{/remarksbox}

{if $add_options|@count > 0}
<h2>{tr}Add Watch{/tr}</h2>
<form action="tiki-user_watches.php" method="post">
<table class="normal">
<tr>
<td class="formcolor">{tr}Event{/tr}:</td>
<td class="formcolor">
<select name="event" onchange="document.getElementById('lang_list').style.visibility = (this.value == 'wiki_page_in_lang_created') ? '' : 'hidden'">
	<option>{tr}Select event type{/tr}</option>
	{foreach key=event item=label from=$add_options}
		<option value="{$event|escape}">{$label|escape}</option>
	{/foreach}
</select>
</td>
</tr>
<tr id="lang_list" style="visibility: hidden">
	<td class="formcolor">{tr}Language{/tr}</td>
	<td class="formcolor">
		<select name="langwatch">
			{section name=ix loop=$languages}
				<option value="{$languages[ix].value|escape}">
				  {$languages[ix].name}
				</option>
			{/section}
		</select>
	</td>
</tr>
<tr><td class="formcolor">&nbsp;</td>
<td class="formcolor"><input type="submit" name="add" value="{tr}Add{/tr}" /></td>
</tr>
</table>
</form>
{/if}
<br />
<h2>{tr}Watches{/tr}</h2>
<form action="tiki-user_watches.php" method="post" id='formi'>
{tr}Show{/tr}:<select name="event" onchange="javascript:document.getElementById('formi').submit();">
<option value=""{if $smarty.request.event eq ''} selected="selected"{/if}>{tr}All{/tr} {tr}watched events{/tr}</option>
{section name=ix loop=$events}
<option value="{$events[ix]|escape}" {if $events[ix] eq $smarty.request.event}selected="selected"{/if}>
	{if $events[ix] eq 'article_submitted'}
		{tr}A user submits an article{/tr}
	{elseif $events[ix] eq 'blog_post'}
		{tr}A user submits a blog post{/tr}
	{elseif $events[ix] eq 'forum_post_thread'}
		{tr}A user posts a forum thread{/tr}
	{elseif $events[ix] eq 'forum_post_topic'}
		{tr}A user posts a forum topic{/tr}
	{elseif $events[ix] eq 'wiki_page_changed'}
		{tr}A user edited a wiki page{/tr}
	{elseif $events[ix] eq 'wiki_page_in_lang_created'}
		{tr}A user created a wiki page in a language{/tr}
	{else}{$events[ix]}{/if}
</option>
{/section}
</select>
</form>
<br />
<form action="tiki-user_watches.php" method="post">
<table class="normal">
<tr>
{if $watches}
<th style="text-align:center;" class="heading"></th>
{/if}
<th class="heading">{tr}Event{/tr}</th>
<th class="heading">{tr}Object{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$watches}
<tr>
{if $watches}
<td style="text-align:center;" class="{cycle advance=false}">
<input type="checkbox" name="watch[{$watches[ix].watchId}]" />
</td>
{/if}
<td class="{cycle advance=false}">
	{if $watches[ix].event eq 'article_submitted'}
		{tr}A user submits an article{/tr}
	{elseif $watches[ix].event eq 'blog_post'}
		{tr}A user submits a blog post{/tr}
	{elseif $watches[ix].event eq 'forum_post_thread'}
		{tr}A user posts a forum thread{/tr}
	{elseif $watches[ix].event eq 'forum_post_topic'}
		{tr}A user posts a forum topic{/tr}
	{elseif $watches[ix].event eq 'wiki_page_changed'}
		{tr}A user edited a wiki page{/tr}
	{elseif $watches[ix].event eq 'wiki_page_in_lang_created'}
		{tr}A user created a wiki page in a language{/tr}
	{/if}
	({$watches[ix].event})
</td>
<td class="{cycle}"><a class="link" href="{$watches[ix].url}">{tr}{$watches[ix].type}{/tr}: {$watches[ix].title}</a></td>
</tr>
{sectionelse}
<tr><td class="odd" colspan="2">{tr}No records found.{/tr}</td></tr>
{/section}
</table>
{if $watches}
{tr}Perform action with checked{/tr}: <input type="submit" name="delete" value=" {tr}Delete{/tr} ">
{/if}
</form>
