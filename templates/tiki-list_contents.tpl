<h1>
<a class="pagetitle" href="tiki-list_contents.php">{tr}Dynamic content system{/tr}</a>
{if $prefs.feature_help eq 'y'}
  <a href="{$prefs.helpurl}Dynamic+Content" target="tikihelp" class="tikihelp" title="{tr}Help on Dynamic Content{/tr}">
    <img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' />
  </a>
{/if}
{if $prefs.feature_view_tpl eq 'y'}
  <a href="tiki-edit_templates.php?template=tiki-list_contents.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin DynamicContent tpl{/tr}">
    <img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' />
  </a>
{/if}
</h1>

<div class="rbox" name="tip">
  <div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
  <div class="rbox-data" name="tip">{tr}To use content blocks in a text area (Wiki page, etc), a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{content id=x}{/literal}, where x is the ID of the content block.{/tr} {tr}You can also use {literal}{content label=x}{/literal}, where x is the label of the content block.{/tr}</div>
</div>
<br />

<h2>
{if $contentId}
  {tr}Edit content block{/tr}
{else}
  {tr}Create content block{/tr}
{/if}
</h2>

{if $contentId ne ''}<a class="linkbut" href="tiki-list_contents.php">{tr}Create New Block{/tr}</a>{/if}

<form action="tiki-list_contents.php" method="post">
  {query _type='form_input'}
  <input type="hidden" name="contentId" value="{$contentId|escape}" />
  <table class="normal">
    <tr>
      <td class="formcolor">{tr}Label{/tr}:</td>
      <td class="formcolor">
        <input type="text" name="contentLabel" style="width:40%" value="{$contentLabel|escape}" />
      </td>
    </tr>
    <tr>
      <td class="formcolor">{tr}Description{/tr}:</td>
      <td class="formcolor">
        <textarea rows="5" cols="40" name="description" style="width:95%">{$description|escape}</textarea>
      </td>
    </tr>
    <tr>
      <td class="formcolor">&nbsp;</td>
      <td class="formcolor">
        <input type="submit" name="save" value="{tr}Save{/tr}" />
      </td>
    </tr>
  </table>
</form>
<br />

<h2>{tr}Available content blocks{/tr}</h2>

{if $listpages or $find neq ''}
  {include file='find.tpl'}
{/if}

<table class="normal">
  <tr>
    <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='contentId'}{tr}Id{/tr}{/self_link}</td>
    <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='contentLabel'}{tr}Label{/tr}{/self_link}</td>
    <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='data'}{tr}Current Value{/tr}{/self_link}</td>
    <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='actual'}{tr}Current ver{/tr}{/self_link}</td>
    <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='next'}{tr}Next ver{/tr}{/self_link}</td>
    <td class="heading">{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='future'}{tr}Future vers{/tr}{/self_link}</td>
    <td class="heading">{tr}Action{/tr}</td>
  </tr>
{cycle values="odd,even" print=false}
{section name=changes loop=$listpages}
  <tr>
    <td class="{cycle advance=false}">{$listpages[changes].contentId}</td>
    <td class="{cycle advance=false}">
      {if $listpages[changes].contentLabel neq ''}
         <b>{$listpages[changes].contentLabel}</b>
      {/if}
      {if $listpages[changes].description neq ''}
        <div class="subcomment">{$listpages[changes].description}</div>
      {/if}
    </td>
    <td class="{cycle advance=false}">{$listpages[changes].data|escape:'html'|nl2br}</td>
    <td class="{cycle advance=false}">{$listpages[changes].actual|tiki_short_datetime}</td>
    <td class="{cycle advance=false}">{$listpages[changes].next|tiki_short_datetime}</td>
    <td class="{cycle advance=false}">{$listpages[changes].future}</td>
    <td class="{cycle advance=true}">
      {self_link _class='link' _icon='page_edit' edit=$listpages[changes].contentId}{tr}Edit{/tr}{/self_link}
      <a class="link" href="tiki-edit_programmed_content.php?contentId={$listpages[changes].contentId}" title="{tr}Program{/tr}">{icon _id=wrench.png alt="{tr}Program{/tr}"}</a>
      {self_link _class='link' _icon='cross' _template='confirm.tpl' remove=$listpages[changes].contentId}{tr}Remove{/tr}{/self_link}
    </td>
  </tr>
{sectionelse}
  <tr>
    <td colspan="8" class="odd">
      <b>{tr}No records found{/tr}</b>
    </td>
  </tr>
{/section}
</table>

<br />
{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
