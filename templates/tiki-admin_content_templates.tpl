<a class="pagetitle" href="tiki-admin_content_templates.php">{tr}Admin templates{/tr}</a><br/>
{if $preview eq 'y'}
<div class="wikitext">{$parsed}</div>
{/if}
<h2>{tr}Create/edit templates{/tr}</h2>
<form action="tiki-admin_content_templates.php" method="post">
<input type="hidden" name="templateId" value="{$templateId}" />
<table class="normal">
<tr><td class="formcolor">{tr}name{/tr}:</td><td class="formcolor"><input type="text" maxlength="255" size="40" name="name" value="{$info.name}" /></td></tr>
<tr><td class="formcolor">{tr}use in cms{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_cms" {if $info.section_cms eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}use in wiki{/tr}:</td><td class="formcolor"><input type="checkbox" name="section_wiki" {if $info.section_wiki eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}template{/tr}:</td><td class="formcolor"><textarea name="content" rows="25" cols="60">{$info.content}</textarea></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="preview" value="{tr}Preview{/tr}" /></td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>


<h2>{tr}Templates{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_content_templates.php">
     <input type="text" name="find" value="{$find}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode}" />
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
{section name=user loop=$channels}
{if $smarty.section.user.index % 2}
<tr>
<td class="odd">{$channels[user].name}</td>
<td class="odd">{$channels[user].created|date_format:"%d of %b [%H:%M]"}</td>
<td class="odd">
[{section name=ix loop=$channels[user].sections}
({$channels[user].sections[ix]} <a class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}">x</a>)
{/section}]
</td>
<td class="odd">
   <a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}">{tr}edit{/tr}</a>
</td>
</tr>
{else}
<tr>
<td class="even">{$channels[user].name}</td>
<td class="even">{$channels[user].created|date_format:"%d of %b [%H:%M]"}</td>
<td class="even">
[{section name=ix loop=$channels[user].sections}
({$channels[user].sections[ix]} <a class="link" href="tiki-admin_content_templates.php?removesection={$channels[user].sections[ix]}&amp;rtemplateId={$channels[user].templateId}">x</a>)
{/section}]
</td>
<td class="even">
   <a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].templateId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_content_templates.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;templateId={$channels[user].templateId}">{tr}edit{/tr}</a>
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
</div>
</div>

