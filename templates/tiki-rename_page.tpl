<h2>{tr}Rename page{/tr}: {$page}</h2>
<form action="tiki-rename_page.php" method="post">
<input type="hidden"  name="oldpage" value="{$page}" />
<input type="hidden"  name="page" value="{$page}" />
<table class="normal">
<tr>
  <td class='formcolor'>{tr}New name{/tr}:</td>
  <td>
    <input type='text' name='newpage' />
    <input type="submit" name="rename" value='{tr}rename{/tr}' />
  </td>
</tr>
</table>
</form>
<br/>
<br/>
