<a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a><br/><br/>
<h3>Create a new topic</h3>
<form enctype="multipart/form-data" action="tiki-admin_topics.php" method="post">
<table class="normal">
<tr><td class="formcolor">Name</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">Upload Image</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input name="userfile1" type="file"></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="addtopic" value="add" /></td></tr>
</table>
</form>

<h3>List of topics</h3>
<table class="normal">
<tr>
<td class="heading">{tr}name{/tr}</td>
<td class="heading">{tr}Image{/tr}</td>
<td class="heading">{tr}Active?{/tr}</td>
<td class="heading">{tr}Articles (subs){/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=user loop=$topics}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$topics[user].name}</td>
<td class="odd"><img alt="topic image" border="0" src="topic_image.php?id={$topics[user].topicId}" />
</td>
<td class="odd">{$topics[user].active}</td>
<td class="odd">{$topics[user].arts} ({$topics[user].subs})</td>
<td class="odd">
<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}">{tr}Remove{/tr}</a>
{if $topics[user].active eq 'n'}
<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}">{tr}Activate{/tr}</a>
{else}
<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">{tr}Deactivate{/tr}</a>
{/if}
{if $topics[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Topic%20{$topics[user].name}&amp;objectType=topic&amp;permType=topics&amp;objectId={$topics[user].topicId}">{tr}perms{/tr}</a>{if $topics[user].individual eq 'y'}){/if}
</td>
</tr>
{else}
<tr>
<td class="even">{$topics[user].name}</td>
<td class="even"><img alt="topic image" border="0" src="topic_image.php?id={$topics[user].topicId}" /></td>
<td class="even">{$topics[user].active}</td>
<td class="even">{$topics[user].arts} o({$topics[user].subs})</td>
<td class="even">
<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}">{tr}Remove{/tr}</a>
{if $topics[user].active eq 'n'}
<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}">{tr}Activate{/tr}</a>
{else}
<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">{tr}Deactivate{/tr}</a>
{/if}
{if $topics[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Topic%20{$topics[user].name}&amp;objectType=topic&amp;permType=topics&amp;objectId={$topics[user].topicId}">{tr}perms{/tr}</a>{if $topics[user].individual eq 'y'}){/if}
</td>
</tr>
{/if}
{sectionelse}
<tr>
<td colspan="3" class="odd">{tr}No records found{/tr}</td>
</tr>
{/section}
</table>
