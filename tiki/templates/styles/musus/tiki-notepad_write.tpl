{*Smarty template*}
<a class="pagetitle" href="tiki-notepad_write.php">{tr}Write a note{/tr}</a><br /><br />
{include file=tiki-mytiki_bar.tpl}
<br /><br />
<a href="tiki-notepad_list.php">{tr}Notes{/tr}</a>
<br /><br />
<form action="tiki-notepad_write.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<table>
  <tr><td>{tr}Name{/tr}</td>
      <td><input type="text" name="name" value="{$info.name|escape}" /></td>
  </tr>
  <tr><td>{tr}Data{/tr}</td>
      <td>
        <textarea rows="20" cols="80" name="data">{$info.data|escape}</textarea>
      </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" value="{tr}save{/tr}" />
    </td>
  </tr>
</table>
</form>

