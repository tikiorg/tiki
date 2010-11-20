{* $Id$ *}
{* Next line moved from bottom of file to top.  *}
{include file=tiki-page_bar.tpl}
{title}{tr}Rename page:{/tr}&nbsp;{$page}{/title}

<div class="navbar">
	{assign var=thispage value=$page|escape:url}
	{button href="tiki-index.php?page=$thispage" _text="{tr}View page{/tr}"}
</div>

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

