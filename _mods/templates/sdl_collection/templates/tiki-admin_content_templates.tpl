<a class="pagetitle" href="tiki-admin_content_templates.php">{tr}Admin Templates{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=ContentTemplates" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin content templates{/tr}"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_content_templates.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin content templates tpl{/tr}"><img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>
{/if}

<!-- begin -->

<br />
{if $preview eq 'y'}
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
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="name" value="{$info.name|escape}" /></td></tr>
{if $feature_cms_templates eq 'y'}
<tr><td class="formcolor">{tr}Use in Articles{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_cms" {if $info.section_cms eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $feature_wiki_templates eq 'y'}
<tr><td class="formcolor">{tr}Use in Wiki{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_wiki" {if $info.section_wiki eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
{if $feature_newsletters eq 'y'}
<tr><td class="formcolor">{tr}Use in Newsletters{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_newsletters" {if $info.section_newsletters eq 'y'}checked="checked"{/if} /></td></tr>
{/if}
<tr><td class="formcolor">{tr}Use in HTML pages{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_html" {if $info.section_html eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Template{/tr}:</td><td class="formcolor"><textarea name="content" rows="25" cols="60">{$info.content|escape}</textarea></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" />
&nbsp;<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>


<h2>{tr}Templates{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Search{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_content_templates.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Go{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Last Modified{/tr}</a></td>
<td class="heading">{tr}Sections{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].created|tiki_short_datetime}</td>
<td class="odd">
[{section name=ix loop=$channels[user].sections}
&nbsp;&nbsp;({$channels[user].sections[ix]|replace:"wiki":"Wiki"|replace:"cms":"Article"|replace:"html":"HTML"|replace:"newsletters":"Newsletters"}&nbsp;&nbsp;<a class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this 
template?{/tr}')" title="{tr}Click here to delete this template{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete2.gif" /></a>)&nbsp;&nbsp;
{/section}]
</td>
<td class="odd">
&nbsp;&nbsp;<a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this template?{/tr}')" 
title="{tr}Click here to delete this template{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].created|tiki_short_datetime}</td>
<td class="even">
[{section name=ix loop=$channels[user].sections}
&nbsp;&nbsp;({$channels[user].sections[ix]|replace:"wiki":"Wiki"|replace:"cms":"Article"|replace:"html":"HTML"|replace:"newsletters":"Newsletters"}&nbsp;&nbsp;<a class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this template?{/tr}')"
title="{tr}Click here to delete this template{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete2.gif" /></a>)&nbsp;&nbsp;
{/section}]
</td>
<td class="even">
&nbsp;&nbsp;<a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this template?{/tr}')" 
title="{tr}Click here to delete this template{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;
   <a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>
</td>
</tr>
{/if}
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