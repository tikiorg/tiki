{*Smarty template*}
<a class="pagetitle" href="tiki-notepad_read.php?noteId={$noteId}">{tr}Reading note:{/tr} {$info.name}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
<a class="link" href="tiki-notepad_list.php">{tr}List notes{/tr}</a>
<a class="link" href="tiki-notepad_write.php">{tr}Write note{/tr}</a>
<br/><br/>
<table>
<tr><td>
<form id='formread' action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId}" />
<select name="parse_mode" onChange="javascript:document.getElementById('formread').submit();">
<option value="raw" {if $parse_mode eq 'raw'}selected="selected"{/if}>{tr}Normal{/tr}</option>
<option value="wiki"{if $parse_mode eq 'wiki'}selected="selected"{/if}>{tr}Wiki{/tr}</option>
</select>
<!--<input type="submit" name="setpm" value="{tr}set{/tr}" />-->
</form>
</td>
<td>
<form action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId}" />
<input type="submit" name="remove" value="{tr}delete{/tr}" />
</form>
</td>
<td>
<form action="tiki-notepad_write.php" method="post">
<input type="hidden" name="noteId" value="{$noteId}" />
<input type="submit" name="write" value="{tr}edit{/tr}" />
</form>
</td></tr></table>
  <div class="wikitext">
  {$info.parsed}
  </div>
