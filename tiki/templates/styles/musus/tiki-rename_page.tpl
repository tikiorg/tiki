<h2>{tr}Rename page{/tr}: {$page}</h2>
<form action="tiki-rename_page.php" method="post">
<input type="hidden"  name="oldpage" value="{$page|escape}" />
<input type="hidden"  name="page" value="{$page|escape}" />
<table>
<tr>
  <td>{tr}New name{/tr}:</td>
  <td>
    <input type='text' name='newpage' value='{$page|escape}'/>
    <input type="submit" name="rename" value='{tr}rename{/tr}' />
  </td>
</tr>
</table>
</form>
<br />
<br />
