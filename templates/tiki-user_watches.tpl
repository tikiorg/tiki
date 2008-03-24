{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-user_watches.tpl,v 1.24.2.3 2008-03-13 21:00:48 sylvieg Exp $ *}
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

{if $prefs.feature_articles eq 'y' and $tiki_p_read_article eq 'y'}
<br />
<h2>{tr}Add Watch{/tr}</h2>
<form action="tiki-user_watches.php" method="post">
<table class="normal">
<tr>
<td class="formcolor">{tr}Event{/tr}:</td>
<td class="formcolor">
<select name="event">
<option value="article_submitted">{tr}A user submits an article{/tr}</option>
</select>
</td>
</tr>
<tr><td class="formcolor">&nbsp;</td>
<td class="formcolor"><input type="submit" name="add" value="{tr}Add{/tr}" /></td>
</tr>
</table>
</form>
{/if}

<h2>{tr}Watches{/tr}</h2>
<form action="tiki-user_watches.php" method="post" id='formi'>
{tr}Event{/tr}:<select name="event" onchange="javascript:document.getElementById('formi').submit();">
<option value=""{if $smarty.request.event eq ''} selected="selected"{/if}>{tr}All{/tr}</option>
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
	{else}{$events[ix]}{/if}
</option>
{/section}
</select>
</form>

<form action="tiki-user_watches.php" method="post">
<table class="normal">
<tr>
<td style="text-align:center;"  class="heading"><input type="submit" name="delete" value="{tr}x{/tr}"></td>
<td class="heading">{tr}Event{/tr}</td>
<td class="heading">{tr}Object{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$watches}
<tr>
<td style="text-align:center;" class="{cycle advance=false}">
<input type="checkbox" name="watch[{$watches[ix].watchId}]" />
</td>
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
	{/if}
	({$watches[ix].event})
</td>
<td class="{cycle}"><a class="link" href="{$watches[ix].url}">{tr}{$watches[ix].type}{/tr}: {$watches[ix].title}</a></td>
</tr>
{/section}
</table>
</form>
