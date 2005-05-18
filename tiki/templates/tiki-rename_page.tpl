<h1><a class="pagetitle" href="tiki-pagehistory?page={$page|escape:"url"}">{tr}Rename page{/tr}</a>: <a class="pagetitle" href="tiki-index.php?page={$page|escape:"url"}">{$page}</a></h1>
<form action="tiki-rename_page.php" method="post">
<input type="hidden"  name="page" value="{$page|escape}" />
<table class="normal">
<tr>
  <td class='formcolor'>{tr}New name{/tr}:</td>
  <td class='formcolor'>
    <input type='text' name='newpage' value='{$page|escape}'/>
    <input type="submit" name="rename" value='{tr}rename{/tr}' />
  </td>
</tr>
</table>
</form>
<br />
<br />
