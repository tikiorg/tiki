{title}{tr}Rename page:{/tr}&nbsp;{$page|escape}{/title}

<div class="navbar">
	{assign var=thispage value=$page|escape:url}
	{button href="tiki-index.php?page=$thispage" _text="{tr}View page{/tr}"}
</div>

<form action="tiki-rename_page.php" method="post">
  <input type="hidden"  name="page" value="{$page|escape}" />
  <table class="normal">
    <tr>
      <td class='formcolor'><label for='newpage'>{tr}New name:{/tr}</label></td>
      <td class='formcolor'>
        <input type='text' id='newpage' name='newpage' size='40' value='{$page|escape}'/>
        <input type="submit" name="rename" value='{tr}Rename{/tr}' />
      </td>
    </tr>
  </table>
</form>

<br />

{include file='tiki-page_bar.tpl'}

