{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-edit_topic.tpl,v 1.3 2004-01-17 01:19:09 musus Exp $ *}

<a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a>

{if $feature_help eq 'y'}
<!-- the help link info -->
<a href="http://tikiwiki.org/tiki-index.php?page=Article" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Admin Topics{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' />
</a>{/if}

<!-- beginning of next bit -->

<br /><br />
<h3>{tr}Edit a topic{/tr}</h3>

<form enctype="multipart/form-data" action="tiki-edit_topic.php" method="post">
 <table>
<tr><td>{tr}Topic Name{/tr}</td>
    <td>
      <input type="hidden" name="topicid" value="{$topic_info.topicId}" />
      <input type="text" name="name" value="{$topic_info.name}" />
    </td>
</tr>
<tr><td>{tr}Upload Image{/tr}</td>
    <td>
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
      <input name="userfile1" type="file">
    </td>
</tr>
<tr><td>&nbsp;</td><td><input type="submit" name="edittopic" value="{tr}edit{/tr}" /></td></tr>
</table>
</form>
