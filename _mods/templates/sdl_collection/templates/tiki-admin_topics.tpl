{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-admin_topics.tpl,v 1.1 2004-05-09 23:07:57 damosoft Exp $ *}

<a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a>

<!-- the help link info -->
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ArticleDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Admin Topics{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

<!-- link to tpl -->
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_topics.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin topics tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}

<!-- beginning of next bit -->
<br /><br />
<h3>{tr}Create a new topic{/tr}</h3>

<form enctype="multipart/form-data" action="tiki-admin_topics.php" method="post">
 <table class="normal">
<tr><td class="formcolor">{tr}Topic Name{/tr}</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">{tr}Upload Image{/tr}</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input name="userfile1" type="file"></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="addtopic" value="{tr}Add{/tr}" /></td></tr>
</table>
</form>

<h3>{tr}List of topics{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}Name{/tr}</td>
<td class="heading">{tr}Image{/tr}</td>
<td class="heading">{tr}Active?{/tr}</td>
<td class="heading">{tr}Articles (subs){/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$topics}
<tr>
<td class="{cycle advance=false}">{$topics[user].name}</td>
<td class="{cycle advance=false}">
{if $topics[user].image_size}
<img alt="{tr}topic image{/tr}" border="0" src="topic_image.php?id={$topics[user].topicId}&amp;reload=1" />
{else}
&nbsp;
{/if}
</td>
<td class="{cycle advance=false}">{$topics[user].active}</td>
<td class="{cycle advance=false}">{$topics[user].arts} ({$topics[user].subs})</td>
<td class="{cycle}">
<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this topic?{/tr}')">{tr}Remove{/tr}</a><br />
<a class="link" href="tiki-admin_topics.php?removeall={$topics[user].topicId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this topic and all articles within this topic?{/tr}')">{tr}Remove with articles{/tr}</a><br />
{if $topics[user].active eq 'n'}
<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}">{tr}Activate{/tr}</a><br />
{else}
<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">{tr}Deactivate{/tr}</a><br />
{/if}
{if $topics[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName={tr}Topic{/tr}%20{$topics[user].name}&amp;objectType=topic&amp;permType=topics&amp;objectId={$topics[user].topicId}">{tr}Permissions{/tr}</a><br />{if $topics[user].individual eq 'y'}){/if}
 <a class="link" href="tiki-edit_topic.php?topicid={$topics[user].topicId}">{tr}Edit{/tr}</a><br />
</td>
</tr>
{sectionelse}
<tr>
<td colspan="5" class="odd">{tr}No records found{/tr}</td>
</tr>
{/section}

</table>
