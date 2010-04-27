{* $Id$ *}

{title}{tr}Edit Section:{/tr}&nbsp;{$title}{/title}

<div class="navbar">
  <a href="{$referer}">{tr}View object{/tr}</a>
</div>

{if $preview}
  {include file='tiki-preview.tpl'}
  <br />
{/if}
<form method="post" action="tiki-edit_wiki_section.php" id='editwikiform'>
<table class="normal">
<tr class="formcolor">
  <td>{tr}Edit{/tr}:
  </td>
  <td>
    {toolbars area_name='editwiki'}
    <textarea class="wikiedit" id="editwiki" name="data" rows="{$rows}" cols="{$cols}">{$data|escape}</textarea>
    <input type="hidden" name="rows" value="{$rows}"/>
    <input type="hidden" name="cols" value="{$cols}"/>
  </td>
</tr>
<tr><td class="formcolor" colspan="3">
<input type="hidden" name="referer" value="{$referer}" />
<input type="hidden" name="title" value="{$title}" />
<input type="hidden" name="object" value="{$object}" />
<input type="hidden" name="type" value="{$type}" />
{if isset($pos)}<input type="hidden" name="pos" value="{$pos}" />{/if}
{if isset($cell)}<input type="hidden" name="cell" value="{$cell}" />{/if}
{if isset($hdr)}<input type="hidden" name="hdr" value="{$hdr}" />{/if}
{if $prefs.feature_ajax_autosave eq "y"}
	{button _title="{tr}Preview your changes in a separate window.{/tr}" _class="wikiaction tips" _text="{tr}AJAX Preview{/tr}" _ajax="n" _onclick="ajax_preview(); return false;"}&nbsp;&nbsp;
{/if}
<input type="submit" title="{tr}Preview your changes.{/tr}" class="wikiaction tips" name="preview" value="{tr}Preview{/tr}" />&nbsp;&nbsp;
<input type="submit"  title="{tr}Save the page.{/tr}" class="wikiaction tips" name="save" value="{tr}Save{/tr}" />&nbsp;&nbsp;
<input type="submit" title="{tr}Cancel the edit, you will lose your changes.{/tr}" class="wikiaction tips" name="cancel_edit" value="{tr}Cancel Edit{/tr}" />
</td></tr>
</table>
</form>
