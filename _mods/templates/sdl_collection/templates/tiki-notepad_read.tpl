{*Smarty template*}
<a class="pagetitle" href="tiki-notepad_read.php?noteId={$noteId}">{tr}Reading Note:{/tr} {$info.name}</a><br/><br/>
{include file=tiki-mytiki_bar.tpl}
<br/><br/>
[<a class="link" href="tiki-notepad_list.php">{tr}List notes{/tr}</a>]
[<a class="link" href="tiki-notepad_write.php">{tr}Write note{/tr}</a>]
<br/><br/>
<table>
<tr><td>
<form id='formread' action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<select name="parse_mode" onchange="javascript:document.getElementById('formread').submit();">
<option value="raw" {if $parse_mode eq 'raw'}selected="selected"{/if}>{tr}Normal{/tr}</option>
<option value="wiki"{if $parse_mode eq 'wiki'}selected="selected"{/if}>{tr}Wiki{/tr}</option>
<option value="template"{if $parse_mode eq 'template'}selected="selected"{/if}>{tr}Template{/tr}</option>
</select>
<!--<input type="submit" name="setpm" value="{tr}set{/tr}" />-->
</form>
</td>
<td>
<form action="tiki-notepad_read.php" method="post">
<input type="hidden" name="noteId" value="{$noteId|escape}" />
<input type="submit" name="remove" value="{tr}Delete{/tr}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this note?{/tr}')"/>
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
<input size="20" type="text" name="wiki_name" value="{$info.name|escape}" />
<input type="checkbox" name="over" />
</form>
{/if}
</td>
{/if}
</tr></table>
{if $smarty.request.parse_mode eq 'template'}
  <div class="wikitext">
  {eval var="$info.data"}
  </div>
{elseif $smarty.request.parse_mode eq 'wiki'}
  <div class="wikitext">
  {$info.parsed}
  </div>
{else}
  <div class="wikitext">
  {$info.data}
  </div>
{/if}