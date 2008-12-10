{assign var=thispage value=$page|escape:url}
{$object_page_controls.header}

<form action="tiki-rename_page.php" method="post">
  <input type="hidden"  name="page" value="{$page|escape}" />
  <table class="normal">
    <tr>
      <td class='formcolor'>{tr}New name{/tr}:</td>
      <td class='formcolor'>
        <input type='text' name='newpage' size='40' value='{$page|escape}'/>
        <input type="submit" name="rename" value='{tr}Rename{/tr}' />
      </td>
    </tr>
  </table>
</form>

<br />

{include file=tiki-page_bar.tpl}

{$object_page_controls.footer}
