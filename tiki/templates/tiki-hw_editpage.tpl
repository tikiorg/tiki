{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_editpage.tpl,v 1.2 2004-02-05 19:10:00 ggeller Exp $ *}

{* {if $preview}
  <h2>{tr}Preview{/tr}: {$homeworkTitle}</h2>
  <div  class="wikitext">{$parsed}</div>
{/if} *}

<h1>
  {$homeworkTitle}
</h1>

{* {assign var=area_name value="body"} *}

<div style="margin-bottom:1px;">
  <span class="tabbut"><a href="tiki-hw_pagehistory.php?id={$pageId}" class="tablink">{tr}history{/tr}</a></span>
  <span class="tabbut"><a href="javascript:flip('edithelpzone');" class="linkbut">{tr}Wiki quick help{/tr}</a></span>
</div>

{* {assign var=area_name value="editwiki"} *}
<form  enctype="multipart/form-data" method="post" action="tiki-hw_editpage.php?id={$pageId}" id='editpageform'>
  <table class="normal">
    {if $feature_smileys eq 'y'}
      <tr class="formcolor"><td class="formcolor">{tr}Smileys{/tr}:</td>
        <td class="formcolor">
          {include file="tiki-smileys.tpl" area_name='editwiki'}
        </td>
      </tr>
    {/if}

    <tr class="formcolor">
      <td class="formcolor">{tr}Edit{/tr}:<br /><br />
        {include file="textareasize.tpl" area_name='editwiki' formId='editpageform'}<br /><br />
        {tr}Quicklinks{/tr}:<br /><br />
        {include file=tiki-edit_help_tool.tpl}
      </td>
      <td class="formcolor">
        <textarea id='editwiki' class="wikiedit" name="edit" rows="{$rows}" wrap="virtual" cols="{$cols}">{$pagedata|escape}</textarea>
        <input type="hidden" name="rows" value="{$rows}"/>
        <input type="hidden" name="cols" value="{$cols}"/>
      </td>
    </tr>

    <tr class="formcolor">
      <td class="formcolor">{tr}Comment{/tr}:</td>
      <td class="formcolor"><input size="80" class="wikitext" type="text" name="comment" value="{$commentdata|escape}" /></td>
    </tr>

    <input type="hidden" name="page" value="{$page|escape}" />

    {* GGG No preview for the prototype
      <tr class="formcolor">
        <td class="formcolor">&nbsp;</td>
        <td class="formcolor"><input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" /></td>
      </tr>
    *}

    <tr class="formcolor">
      <td class="formcolor">
        &nbsp;
      </td>
      <td class="formcolor">
        <input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" />
        <a class="link" href="tiki-hw_page.php?assignmentId={$assignmentId|escape:"url"}">{tr}cancel edit{/tr}</a>
      </td class="formcolor">
    </tr>
  </table>
</form>
{* <br />
<!--<a href="javascript:replaceSome('editwiki','foo','bar');">foo2bar</a>--> *}
{include file=tiki-edit_help.tpl}
