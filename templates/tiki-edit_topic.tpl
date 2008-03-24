{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_topic.tpl,v 1.12.2.1 2008-01-30 15:33:51 nyloth Exp $ *}

<h1><a  class="pagetitle" href="tiki-admin_topics.php">{tr}Admin Topics{/tr}</a>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Articles" target="tikihelp" class="tikihelp" title="{tr}Admin Topics{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' />
</a>{/if}
</h1>

<h2>{tr}Edit a topic{/tr}</h2>

{if !empty($errors)}
<div class="highlight simplebox">{section name=ix loop=$errors}{$errors[ix]}{/section}</div>
{/if}

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
<tr><td class="formcolor">{tr}Add Notification Email{/tr}</td><td class="formcolor"><input type="text" name="email" value="{$email|escape}" />&nbsp;<a href="tiki-admin_notifications.php" title="{tr}Admin notifications{/tr}">{icon _id='wrench' alt="{tr}Admin notifications{/tr}"}</a></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="edittopic" value="{tr}Edit{/tr}" /></td></tr>
</table>
</form>
