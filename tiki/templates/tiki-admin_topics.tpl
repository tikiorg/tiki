<h1><a  class="wiki" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a></h1>

<h3>Create a new topic</h3>
<form enctype="multipart/form-data" action="tiki-admin_topics.php" method="post">
<table>
<tr><td>Name</td><td><input type="text" name="name" /></td></tr>
<tr><td>Upload Image</td><td><input type="hidden" name="MAX_FILE_SIZE" value="1000000">
<input name="userfile1" type="file"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="addtopic" value="add" /></td></tr>
</table>
</form>

<h3>List of topics</h3>
<table border="1" cellpadding="0" cellspacing="0" width="97%">
<tr>
<td class="heading">{tr}name{/tr}</td>
<td class="heading">{tr}Image{/tr}</td>
<td class="heading">{tr}Active?{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=user loop=$topics}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$topics[user].name}</td>
<td class="odd"><img alt="topic image" border="0" src="topic_image.php?id={$topics[user].topicId}" />
</td>
<td class="odd">{$topics[user].active}</td>
<td class="odd">
<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}">{tr}Remove{/tr}</a>
{if $topics[user].active eq 'n'}
<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}">{tr}Activate{/tr}</a>
{else}
<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">{tr}Deactivate{/tr}</a>
{/if}
</td>
</tr>
{else}
<tr>
<td class="even">{$topics[user].name}</td>
<td class="even"><img alt="topic image" border="0" src="topic_image.php?id={$topics[user].topicId}" /></td>
<td class="even">{$topics[user].active}</td>
<td class="even">
<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}">{tr}Remove{/tr}</a>
{if $topics[user].active eq 'n'}
<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}">{tr}Activate{/tr}</a>
{else}
<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}">{tr}Deactivate{/tr}</a>
{/if}
</td>
</tr>
{/if}
{sectionelse}
<tr>
<td colspan="3" class="odd">{tr}No records found{/tr}</td>
</tr>
{/section}
</table>
