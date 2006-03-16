{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_topic.tpl,v 1.8 2006-03-16 13:43:12 sylvieg Exp $ *}

<h1><a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}Articles" target="tikihelp" class="tikihelp" title="{tr}Admin Topics{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' />
</a>{/if}
</h1>

<h2>{tr}Edit a topic{/tr}</h2>

<form enctype="multipart/form-data" action="tiki-edit_topic.php" method="post">
 <table class="normal">
<tr><td class="formcolor">{tr}Topic Name{/tr}</td>
    <td class="formcolor">
      <input type="hidden" name="topicid" value="{$topic_info.topicId}" />
      <input type="text" name="name" value="{$topic_info.name}" />
    </td>
</tr>
<tr><td class="formcolor">{tr}Upload Image{/tr}</td>
    <td class="formcolor">
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
      <input name="userfile1" type="file" />
    </td>
</tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="edittopic" value="{tr}edit{/tr}" /></td></tr>
</table>
</form>
