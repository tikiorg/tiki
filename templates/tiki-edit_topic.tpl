{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_topic.tpl,v 1.4 2004-06-06 08:39:56 damosoft Exp $ *}

<a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a>

{if $feature_help eq 'y'}
<!-- the help link info -->
<a href="{$helpurl}Article" target="tikihelp" class="tikihelp" title="{tr}Admin Topics{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' />
</a>{/if}

<!-- beginning of next bit -->

<br /><br />
<h3>{tr}Edit a topic{/tr}</h3>

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
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
      <input name="userfile1" type="file">
    </td>
</tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="edittopic" value="{tr}edit{/tr}" /></td></tr>
</table>
</form>
