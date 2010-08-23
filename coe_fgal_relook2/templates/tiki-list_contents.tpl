{title help="Dynamic+Content"}{tr}Dynamic Content System{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}{tr}To use content blocks in a text area (Wiki page, etc), a <a class="rbox-link" href="tiki-admin_modules.php">module</a> or a template, use {literal}{content id=x}{/literal}, where x is the ID of the content block.{/tr} {tr}You can also use {literal}{content label=x}{/literal}, where x is the label of the content block.{/tr}{/remarksbox}

<h2>
{if $contentId}
  {tr}Edit content block{/tr}
{else}
  {tr}Create content block{/tr}
{/if}
</h2>

{if $contentId ne ''}
	<div class="navbar">
		{button href="tiki-list_contents.php" _text="{tr}Create New Block{/tr}"}
	</div>
{/if}

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
    <th>{self_link _sort_arg='sort_mode' _sort_field='contentId'}{tr}Id{/tr}{/self_link}</th>
    <th>{self_link _sort_arg='sort_mode' _sort_field='contentLabel'}{tr}Label{/tr}{/self_link}</th>
    <th>{self_link _sort_arg='sort_mode' _sort_field='data'}{tr}Current Value{/tr}{/self_link}</th>
    <th>{self_link _sort_arg='sort_mode' _sort_field='actual'}{tr}Current ver{/tr}{/self_link}</th>
    <th>{self_link _sort_arg='sort_mode' _sort_field='next'}{tr}Next ver{/tr}{/self_link}</th>
    <th>{self_link _sort_arg='sort_mode' _sort_field='future'}{tr}Future vers{/tr}{/self_link}</th>
    <th>{tr}Action{/tr}</th>
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
      <a class="link" href="tiki-edit_programmed_content.php?contentId={$listpages[changes].contentId}" title="{tr}Program{/tr}">{icon _id=wrench alt="{tr}Program{/tr}"}</a>
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

{pagination_links cant=$cant step=$prefs.maxRecords offset=$offset}{/pagination_links}
