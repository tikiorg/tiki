{title help="Articles"}{tr}Admin Topics{/tr}{/title}

<h2>{tr}Edit a topic{/tr}</h2>

{if !empty($errors)}
<div class="highlight simplebox">{section name=ix loop=$errors}{$errors[ix]}{/section}</div>
{/if}

<form enctype="multipart/form-data" action="tiki-edit_topic.php" method="post">
 <table class="formcolor">
<tr><td>{tr}Topic Name{/tr}</td>
    <td>
      <input type="hidden" name="topicid" value="{$topic_info.topicId}" />
      <input type="text" name="name" value="{$topic_info.name|escape}" />
    </td>
</tr>
<tr><td>{tr}Upload Image{/tr}</td>
    <td>
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
      <input name="userfile1" type="file" />
    </td>
</tr>
<tr><td>{tr}Add Notification Email{/tr}</td><td><input type="text" name="email" value="{$email|escape}" />&nbsp;<a href="tiki-admin_notifications.php" title="{tr}Admin notifications{/tr}">{icon _id='wrench' alt="{tr}Admin notifications{/tr}"}</a></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="edittopic" value="{tr}Edit{/tr}" /></td></tr>
</table>
</form>
