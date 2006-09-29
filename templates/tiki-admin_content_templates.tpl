<h1><a class="pagetitle" href="tiki-admin_content_templates.php">{tr}Admin templates{/tr}</a>
  
{if $feature_help eq 'y'}
<a href="{$helpurl}Content+Templates" target="tikihelp" class="tikihelp" title="{tr}admin content templates{/tr}"><img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_content_templates.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin content templates template{/tr}"><img src="pics/icons/shape_square_edit.png" border="0" width="16" height="16" alt='{tr}Edit template{/tr}' /></a>
{/if}</h1>

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
<input type="hidden" name="templateId" value="{$templateId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}name{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="name" value="{$info.name|escape}" /></td></tr>
{if $feature_cms_templates eq 'y'}
<tr><td class="formcolor">{tr}use in cms{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_cms" {if $info.section_cms eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $feature_wiki_templates eq 'y'}
<tr><td class="formcolor">{tr}use in wiki{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_wiki" {if $info.section_wiki eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $feature_newsletters eq 'y'}
<tr><td class="formcolor">{tr}use in newsletters{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_newsletters" {if $info.section_newsletters eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $feature_events eq 'y'}
<tr><td class="formcolor">{tr}use in events{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_events" {if $info.section_events eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $feature_html_pages eq 'y'}
<tr><td class="formcolor">{tr}use in HTML pages{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_html" {if $info.section_html eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
<tr><td class="formcolor">{tr}template{/tr}:<br /><br />
{assign var=area_name value="editwiki"}
{include file="textareasize.tpl" area_name='editwiki' formId='editpageform'}<br /><br />
{include file=tiki-edit_help_tool.tpl}</td>
<td class="formcolor"><textarea id='editwiki' name="content" rows="25" cols="60">{$info.content|escape}</textarea></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>


<h2>{tr}Templates{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="tiki-admin_content_templates.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}last modif{/tr}</a></td>
<td class="heading">{tr}sections{/tr}</td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false advance=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].name}</td>
<td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>
<td class="{cycle advance=false}">
{section name=ix loop=$channels[user].sections}
({$channels[user].sections[ix]} <a title="{tr}delete{/tr}" class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}" 
><img src="pics/icons/cross.png" border="0" width="8" height="8" alt='{tr}delete{/tr}' /></a>)&nbsp;&nbsp;
{/section}
</td>
<td class="{cycle advance=true}">
   &nbsp;&nbsp;
   <a title="{tr}edit{/tr}" class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}">
   <img src="pics/icons/page_edit.png" border="0" width="16" height="16"  alt='{tr}edit{/tr}' /></a> &nbsp;
   <a title="{tr}delete{/tr}" class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}" >
   <img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a>
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
[<a class="prevnext" href="tiki-admin_content_templates.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_content_templates.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_content_templates.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
