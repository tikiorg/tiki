{title help="ImportingPagesAdmin"}{tr}Import pages from a Structured Text Dump{/tr}{/title}

<form method="post" action="tiki-import_structuredtext.php">
<table class="formcolor">
<tr>
  <td>{tr}Name of the dump file (it has to be in dump/):{/tr}</td>
  <td><input type="text" name="path" /></td>
</tr>
<tr>
  <td>{tr}Overwrite existing pages if the name is the same:{/tr}</td>
  <td>{tr}Yes{/tr}<input type="radio" name="crunch" value='y' /><input checked="checked" type="radio" name="crunch" value='n' />{tr}No{/tr}</td>
</tr>
<tr>
  <td>{tr}Previously remove existing page versions:{/tr}</td>
  <td>{tr}Yes{/tr}<input type="radio" name="remo" value='y' /><input checked="checked" type="radio" name="remo" value='n' />{tr}No{/tr}</td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td><input type="submit" name="import" value="{tr}import{/tr}" /></td>
</tr>
</table>
</form>
<br /><br />
{if $result eq 'y'}
<table class="normal">
<tr>
  <th>{tr}page{/tr}</th>
  <th>{tr}excerpt{/tr}</th>
  <th>{tr}Result{/tr}</th>
  <th>{tr}body{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$lines}
<tr class="{cycle}">
  <td class="text">{$lines[ix].page}</td>
  <td class="text">{$lines[ix].ex}</td>
  <td class="text">{$lines[ix].msg}</td>
  <td class="text">{$lines[ix].body}</td>
</tr>
{/section}
</table>
{/if}
