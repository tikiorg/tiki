{*Smarty template*}
<a class="pagetitle" href="tiki-notepad_read.php?noteId={$noteId}">{tr}Reading note:{/tr} {$info.name}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
<a class="link" href="tiki-notepad_list.php">{tr}List notes{/tr}</a>
<a class="link" href="tiki-notepad_write.php">{tr}Write note{/tr}</a>
<br/><br/>
<form action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId}" />
<select name="mode">
<option value="raw" {if $mode eq 'raw'}seleced="selected"{/if}>{tr}Normal{/tr}</option>
<option value="wiki"{if $mode eq 'wiki'}seleced="selected"{/if}>{tr}Wiki{/tr}</option>
</select>
<input type="submit" name="mode" value="{tr}set{/tr}" />
</form>
<form action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId}" />
<input type="submit" name="remove" value="{tr}delete{/tr}" />
</form>
<form action="tiki-notepad_write.php" method="post">
<input type="hidden" name="noteId" value="{$noteId}" />
<input type="submit" name="write" value="{tr}edit{/tr}" />
</form>
<table class="normal">
<tr>
  <td class="formcolor">
  {$info.parsed}
  </td>
</tr>
</table>
