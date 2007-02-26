{*
@author: Javier Reyes Gomez (jreyes@escire.com)
@date: 27/01/2006
@copyright (C) 2006 Javier Reyes Gomez (eScire.com)
@license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*}
{tikimodule title="{tr}Clipboard{/tr}" name="workspaces_clipboard" flip=$module_params.flip decorations=$module_params.decorations}
{if $error}
Error: {$error}
{/if}
<form name="pasteForm" id="pasteForm" method="post" action="{$clipboardCurrentUrl}">
<input name="pasteIdCateg" type="hidden" id="pasteIdCateg" value=""> 
      <table  border="0" cellpadding="0" cellspacing="0">
       {foreach from=$clipboard item=clipEntry}
       <tr>
          <td><input  type="checkbox" name ="pasteObjects[]" value="{$clipEntry.type}{$clipEntry.id}" />
</td>
          <td><a class="linkmodule" href="{$clipEntry.href}" alt="{$clipEntry.desc}" title="{$clipEntry.desc}">
	({$clipEntry.type}) {$clipEntry.name}
         </a>
        </td>
       </tr>
       {/foreach}
       </table>
<input class="edubutton" type="submit" name="ClipboardDeleteAll" value="{tr}Empty clipboard{/tr}">
</form>
{/tikimodule}
