<h1><a class="pagetitle" href="tiki-notepad_read.php?noteId={$noteId}">{tr}Reading note:{/tr} {$info.name}</a></h1>
{include file=tiki-mytiki_bar.tpl}

<span class="button2"><a class="linkbut" href="tiki-notepad_list.php">{tr}List notes{/tr}</a></span>
<span class="button2"><a class="linkbut" href="tiki-notepad_write.php">{tr}Write note{/tr}</a></span>
<br /><br />

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
{if $wiki_exists eq 'n'}
<form action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<input type="submit" name="wikify" value="{tr}Wiki Create{/tr}"/>
<input size="20" type="text" name="wiki_name" value="{$info.name|escape}" />
</form>
{else}
<form action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<input type="submit" name="wikify" value="{tr}Wiki Overwrite{/tr}" />
<input size="40" type="text" name="wiki_name" value="{$info.name|escape}" />
</form>
{/if}
</td>
{/if}
</tr></table>
<div class="wikitext">
{$info.parsed}
</div>
