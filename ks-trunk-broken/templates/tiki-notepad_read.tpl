{title help="notepad"}{tr}Reading note:{/tr}&nbsp;{$info.name}{/title}

{include file='tiki-mytiki_bar.tpl'}

<div class="navbar">
	{button href="tiki-notepad_list.php" _text="{tr}List notes{/tr}"}
	{button href="tiki-notepad_write.php" _text="{tr}Write note{/tr}"}
</div>

<table>
<tr><td>
<form id='formread' action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<select name="parse_mode" onchange="javascript:document.getElementById('formread').submit();">
<option value="raw"{if $info.parse_mode eq 'raw'} selected="selected"{/if}>{tr}Text{/tr}</option>
<option value="wiki"{if $info.parse_mode eq 'wiki'} selected="selected"{/if}>{tr}Wiki{/tr}</option>
</select>
</form>
</td>
<td>
<form action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<input type="submit" name="remove" value="{tr}Delete{/tr}" />
</form>
</td>
<td>
<form action="tiki-notepad_write.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<input type="submit" name="write" value="{tr}Edit{/tr}" />
</form>
</td>
{if $tiki_p_edit eq 'y'}
  <td>
    <form action="tiki-notepad_read.php" method="post">
      <input type="hidden" name="noteId" value="{$noteId|escape}" />
      <input type="submit" name="{if $wiki_exists eq 'n'}wikify{else}over{/if}" value="{if $wiki_exists eq 'n'}{tr}Wiki Create{/tr}{else}{tr}Wiki Overwrite{/tr}{/if}"/>
      <input size="40" type="text" name="wiki_name" value="{$info.name|escape}" />
    </form>
  </td>
{/if}
</tr></table>
<div class="wikitext">
{$info.parsed}
</div>
