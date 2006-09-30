{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_topics.tpl,v 1.29 2006-09-30 20:20:19 ohertel Exp $ *}
<h1><a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}Articles" target="tikihelp" class="tikihelp" title="{tr}Admin Topics{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_topics.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin topics template{/tr}">
<img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}edit{/tr}' /></a>{/if}</h1>

<h2>{tr}Create a new topic{/tr}</h2>

<form enctype="multipart/form-data" action="tiki-admin_topics.php" method="post">
 <table class="normal">
<tr><td class="formcolor">{tr}Topic Name{/tr}</td><td class="formcolor"><input type="text" name="name" /></td></tr>
<tr><td class="formcolor">{tr}Upload Image{/tr}</td><td class="formcolor"><input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
<input name="userfile1" type="file" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="addtopic" value="{tr}add{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}List of topics{/tr}</h2>
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
<td class="{cycle advance=false}"><a class="link" href="tiki-view_articles.php?topic={$topics[user].topicId}">{$topics[user].name}</a></td>
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
<a class="link" href="tiki-admin_topics.php?remove={$topics[user].topicId}"><img border='0' title='{tr}remove{/tr}' alt='{tr}remove{/tr}' src='pics/icons/cross.png' width='16' height='16' /></a>
<a class="link" href="tiki-admin_topics.php?removeall={$topics[user].topicId}">{tr}Remove with articles{/tr}</a>
{if $topics[user].active eq 'n'}
<a class="link" href="tiki-admin_topics.php?activate={$topics[user].topicId}"><img border="0" alt="{tr}activate{/tr}" title='{tr}active - click to deactivate{/tr}' src="pics/icons/accept.png" width='16' height='16' /></a>
{else}
<a class="link" href="tiki-admin_topics.php?deactivate={$topics[user].topicId}"><img border="0" alt="{tr}deactivate{/tr}" title='{tr}inactive - click to activate{/tr}' src="pics/icons/delete.png" width='16' height='16' /></a>
{/if}
{if $topics[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName={$topics[user].name|escape:"url"}&amp;objectType=topic&amp;permType=topics&amp;objectId={$topics[user].topicId}"><img border="0" alt="{tr}permissions{/tr}" src="pics/icons/key.png" width='16' height='16' /></a>{if $topics[user].individual eq 'y'}){/if}
 <a class="link" href="tiki-edit_topic.php?topicid={$topics[user].topicId}"><img border='0' title='{tr}edit{/tr}' alt='{tr}edit{/tr}' src='pics/icons/page_edit.png' width='16' height='16' /></a>
</td>
</tr>
{sectionelse}
<tr>
<td colspan="5" class="odd">{tr}No records found{/tr}</td>
</tr>
{/section}
</table>
