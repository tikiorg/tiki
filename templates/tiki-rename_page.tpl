<h1><a class="pagetitle" href="tiki-rename_page.php?page={$page|escape:"url"}">{tr}Rename page{/tr}: {$page}</h1>
<div class="navbar">
<span class="button2"><a class="linkbut" href="tiki-index.php?page={$page|escape:"url"}">{tr}View{/tr}</a></span>
</div>
<form action="tiki-rename_page.php" method="post">
<input type="hidden"  name="page" value="{$page|escape}" />
<table class="normal">
<tr>
  <td class='formcolor'>{tr}New name{/tr}:</td>
  <td class='formcolor'>
    <input type='text' name='newpage' value='{$page|escape}'/>
    <input type="submit" name="rename" value='{tr}Rename{/tr}' />
  </td>
</tr>
</table>
</form>
<br />

{include file=tiki-page_bar.tpl}

