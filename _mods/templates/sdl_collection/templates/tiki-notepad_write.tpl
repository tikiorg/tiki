{*Smarty template*}
<a class="pagetitle" href="tiki-notepad_write.php">{tr}Write a Note{/tr}</a><br /><br />
{include file=tiki-mytiki_bar.tpl}
<br /><br />
<a class="link" href="tiki-notepad_list.php">{tr}Notes{/tr}</a>
<br /><br />
<form action="tiki-notepad_write.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<table class="normal">
  <tr><td class="formcolor">{tr}Name{/tr}</td>
      <td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td>
  </tr>
  <tr><td class="formcolor">{tr}Data{/tr}</td>
      <td class="formcolor">
        <textarea rows="20" cols="80" name="data">{$info.data|escape}</textarea>
      </td>
  </tr>
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" />
    </td>
  </tr>
</table>
</form>

