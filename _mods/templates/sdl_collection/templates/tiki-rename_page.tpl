<h2>{tr}Rename Page{/tr}: {$page}</h2>
<form action="tiki-rename_page.php" method="post">
<input type="hidden"  name="oldpage" value="{$page|escape}" />
<input type="hidden"  name="page" value="{$page|escape}" />
<table class="normal">
<tr>
  <td class='formcolor'>{tr}New Name{/tr}:</td>
  <td class='formcolor'>
    <input type='text' name='newpage' value='{$page|escape}'/>
    <input type="submit" name="rename" value='{tr}Rename{/tr}' />
  </td>
</tr>
</table>
</form>
<br />
<br />
