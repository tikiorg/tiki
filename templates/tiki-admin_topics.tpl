{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_topics.tpl,v 1.8 2003-08-01 10:31:09 redflo Exp $ *}

<a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a><br /><br />
<h3>{tr}Create a new topic{/tr}</h3>

<form enctype="multipart/form-data" action="tiki-admin_topics.php" method="post">
 <table class="normal">
<tr><td class="formcolor">{tr}Topic Name{/tr}</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">{tr}Upload Image{/tr}</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input name="userfile1" type="file"></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="addtopic" value="{tr}add{/tr}" /></td></tr>
</table>
</form>

<h3>{tr}List of topics{/tr}</h3>
<table class="normal">
<tr>
<td class="heading">{tr}name{/tr}</td>
<td class="heading">{tr}Image{/tr}</td>
<td class="heading">{tr}Active?{/tr}</td>
<td class="heading">{tr}Articles (subs){/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=user loop=$topics}
<tr>
<td class="{cycle advance=false}">{$topics[user].name}</td>
<td class="{cycle advance=false}"><img alt="topic image" border="0" src="topic_image.php?id={$topics[user].topicId}" />
</td>
<td class="{cycle advance=false}">{$topics[user].active}</td>
<td class="{cycle advance=false}">{$topics[user].arts} ({$topics[user].subs})</td>
<td class="{cycle}">
<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}">{tr}Remove{/tr}</a>
{if $topics[user].active eq 'n'}
<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}">{tr}Activate{/tr}</a>
{else}
<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">{tr}Deactivate{/tr}</a>
{/if}
{if $topics[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName={tr}Topic{/tr}%20{$topics[user].name}&amp;objectType=topic&amp;permType=topics&amp;objectId={$topics[user].topicId}">{tr}perms{/tr}</a>{if $topics[user].individual eq 'y'}){/if}
</td>
</tr>
{sectionelse}
<tr>
<td colspan="5" class="odd">{tr}No records found{/tr}</td>
</tr>
{/section}
</table>
