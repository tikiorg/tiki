<h1><a class="pagetitle" href="tiki-admin_content_templates.php">{tr}Admin templates{/tr}</a>
  
{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Content+Templates" target="tikihelp" class="tikihelp" title="{tr}Admin Content Templates{/tr}">{icon _id='help'}</a>
{/if}

{if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_content_templates.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}Admin Content Templates Template{/tr}">{icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}</a>
{/if}</h1>

<div class="rbox" name="tip">
  <div class="rbox-title" name="tip">Tip</div>  
  <div class="rbox-data" name="tip">Use the Administration page of each enabled feature to allow the use of content templates.</div>
</div>
<br />

{if $preview eq 'y'}
<h2>{tr}Preview{/tr}</h2>
<div class="wikitext">{$parsed}</div>
{/if}
{if $templateId > 0}
<h2>{tr}Edit this template:{/tr} {$info.name}</h2>
<a href="tiki-admin_content_templates.php">{tr}Create new template{/tr}</a>
{else}
<h2>{tr}Create new template{/tr}</h2>
{/if}
<form action="tiki-admin_content_templates.php" method="post">
{if $prefs.feature_wysiwyg eq 'y' and $prefs.wysiwyg_optional eq 'y'}
{if $wysiwyg ne 'y'}
<span class="button2"><a class="linkbut" href="?templateId={$templateId}&amp;wysiwyg=y">{tr}Use wysiwyg editor{/tr}</a></span>
{else}
<span class="button2"><a class="linkbut" href="?templateId={$templateId}&amp;wysiwyg=n">{tr}Use normal editor{/tr}</a></span>
{/if}
{/if}
<br />
<input type="hidden" name="templateId" value="{$templateId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Use in{/tr}:</td>
<td class="formcolor">
{if $prefs.feature_cms_templates eq 'y'}
<input type="checkbox" name="section_cms" {if $info.section_cms eq 'y'}checked="checked"{/if} /> {tr}CMS{/tr} ({tr}Articles{/tr}<br />{/if}
{if $prefs.feature_wiki_templates eq 'y'}<input type="checkbox" name="section_wiki" {if $info.section_wiki eq 'y'}checked="checked"{/if} /> {tr}Wiki{/tr}<br />
{/if}
{if $prefs.feature_newsletters eq 'y'}<input type="checkbox" name="section_newsletters" {if $info.section_newsletters eq 'y'}checked="checked"{/if} /> {tr}Newsletters{/tr}<br />
{/if}
{if $prefs.feature_events eq 'y'}<input type="checkbox" name="section_events" {if $info.section_events eq 'y'}checked="checked"{/if} /> {tr}Events{/tr}<br />
{/if}
{if $prefs.feature_html_pages eq 'y'}
<input type="checkbox" name="section_html" {if $info.section_html eq 'y'}checked="checked"{/if} /> {tr}HTML pages{/tr}<br />
{/if}
{if ($prefs.feature_cms_templates ne 'y') and ($prefs.feature_wiki_templates ne 'y') and ($prefs.feature_newsletters ne 'y') and ($prefs.feature_events ne 'y') and ($prefs.feature_html_pages ne 'y')}
{tr}No features are configured to use templates.{/tr}
{/if}
</td>
</tr>
{if $wysiwyg ne 'y' and $prefs.quicktags_over_textarea eq 'y'}
  <tr>
    <td class="formcolor"><label>{tr}Quicktags{/tr}</label></td>
    <td class="formcolor">
      {include file=tiki-edit_help_tool.tpl area_name='editwiki'}
    </td>
  </tr>
{/if}

<tr>
  {assign var=area_name value="editwiki"}
  {if $wysiwyg ne 'y'}
    <td class="formcolor">{tr}Template{/tr}:
      <br /><br />
      {tr}Edit{/tr}:
      <br /><br />
      {include file="textareasize.tpl" area_name='editwiki' formId='editpageform' ToolbarSet='Tiki'}<br />
      <br />
      {if $prefs.quicktags_over_textarea neq 'y'}
        {include file=tiki-edit_help_tool.tpl area_name='editwiki'}
      {/if}  
    </td>
    <td class="formcolor">
      <textarea id='editwiki' class="wikiedit" name="content" rows="{$rows}" cols="{$cols}" style="WIDTH: 100%;">{$info.content|escape}</textarea>
      <input type="hidden" name="rows" value="{$rows}"/>
      <input type="hidden" name="cols" value="{$cols}"/>
  {else}
    <td colspan="2">
    {editform Meat=$info.content InstanceName='content' ToolbarSet="Tiki"}
  {/if}
  </td>
</tr>

<tr>
  <td class="formcolor">&nbsp;</td>
  <td class="formcolor">
    <input type="submit" name="save" value="{tr}Save{/tr}" />
    <input type="submit" name="preview" value="{tr}Preview{/tr}" />
  </td>
</tr>
</table>
</form>

<hr />

<h2>{tr}Templates{/tr}</h2>
<div  align="center">
{if $channels or ($find ne '')}
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_content_templates.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
{/if}
<br />
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Last Modif{/tr}</a></td>
<td class="heading">{tr}Sections{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="odd,even" print=false advance=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>
<td class="{cycle advance=false}">
{section name=ix loop=$channels[user].sections}
({$channels[user].sections[ix]} <a title="{tr}Delete{/tr}" class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}" 
>{icon _id='cross' alt='{tr}Delete{/tr}'}</a>)&nbsp;&nbsp;
{/section}
</td>
<td class="{cycle advance=true}">
   &nbsp;&nbsp;
   <a title="{tr}Edit{/tr}" class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}">
   {icon _id='page_edit'}</a> &nbsp;
   <a title="{tr}Delete{/tr}" class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}" >
   {icon _id='cross' alt='{tr}Delete{/tr}'}</a>
</td>
</tr>
{sectionelse}
<tr><td colspan="4" class="odd">
{tr}No records found{/tr}
</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_content_templates.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_content_templates.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-admin_content_templates.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
