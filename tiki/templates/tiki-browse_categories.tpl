{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-browse_categories.tpl,v 1.21 2006-12-08 20:59:19 ohertel Exp $ *}

<h1><a class="pagetitle" href="tiki-browse_categories.php">{if $parentId ne 0}{tr}Category{/tr} {$p_info.name}{else}{tr}Categories{/tr}{/if}</a></h1>
{if $parentId and $p_info.description}<div class="description">{$p_info.description}</div>{/if}
{if $tiki_p_admin_categories eq 'y'}
<div class="navbar"><a class="linkbut" href="tiki-admin_categories.php?parentId={$parentId}" title="{tr}admin the category system{/tr}">{tr}admin category{/tr}</a></div>
{/if}
{tr}Browse in{/tr}:<br />
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}All{/tr}</a>
{if $feature_wiki eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=wiki+page&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'wiki page'}<span class="highlight">{/if}{tr}Wiki pages{/tr}{if $type eq 'wiki page'}</span>{/if}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'image gallery'}<span class="highlight">{/if}{tr}Image galleries{/tr}{if $type eq 'image gallery'}</span>{/if}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'image'}<span class="highlight">{/if}{tr}Images{/tr}{if $type eq 'image'}</span>{/if}</a>
{/if}
{if $feature_file_galleries eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=file+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'file gallery'}<span class="highlight">{/if}{tr}File galleries{/tr}{if $type eq 'file gallery'}</span>{/if}</a>
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=file&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'file'}<span class="highlight">{/if}{tr}Files{/tr}{if $type eq 'file'}</span>{/if}</a>
{/if}
{if $feature_blogs eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=blog&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'blog'}<span class="highlight">{/if}{tr}Blogs{/tr}{if $type eq 'blog'}</span>{/if}</a>
{/if}
{if $feature_trackers eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=tracker&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'tracker'}<span class="highlight">{/if}{tr}Trackers{/tr}{if $type eq 'tracker'}</span>{/if}</a>
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=trackerItem&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'trackerItem'}<span class="highlight">{/if}{tr}Trackers Items{/tr}{if $type eq 'trackerItem'}</span>{/if}</a>
{/if}
{if $feature_quizzes eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=quiz&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'quiz'}<span class="highlight">{/if}{tr}Quizzes{/tr}{if $type eq 'quiz'}</span>{/if}</a>
{/if}
{if $feature_polls eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=poll&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'poll'}<span class="highlight">{/if}{tr}Polls{/tr}{if $type eq 'poll'}</span>{/if}</a>
{/if}
{if $feature_surveys eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=survey&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'survey'}<span class="highlight">{/if}{tr}Surveys{/tr}{if $type eq 'survey'}</span>{/if}</a>
{/if}
{if $feature_directory eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=directory&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'directory'}<span class="highlight">{/if}{tr}Directory{/tr}{if $type eq 'directory'}</span>{/if}</a>
{/if}
{if $feature_faqs eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=faq&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'faq'}<span class="highlight">{/if}{tr}FAQs{/tr}{if $type eq 'faq'}</span>{/if}</a>
{/if}
{if $feature_sheet eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=sheet&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'sheet'}<span class="highlight">{/if}{tr}Sheets{/tr}{if $type eq 'sheet'}</span>{/if}</a>
{/if}
{if $feature_articles eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=article&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{if $type eq 'article'}<span class="highlight">{/if}{tr}Articles{/tr}{if $type eq 'article'}</span>{/if}</a>
{/if}
<br /><br />
<form method="post" action="tiki-browse_categories.php">
  {tr}Find:{/tr} {$p_info.name} <input type="text" name="find" value="{$find|escape}" size="35" />
  {tr}in the current category - and its subcategories: {/tr}<input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/>
                            <input type="hidden" name="parentId" value="{$parentId|escape}" />
                            <input type="hidden" name="type" value="{$type|escape}" />
                            <input type="submit" value="{tr}find{/tr}" name="search" />
                            <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>
<br />
{if $deep eq 'on'}
<a class="link" href="tiki-browse_categories.php?find={$find|escape}&amp;type={$type|escape}&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Hide subcategories objects{/tr}</a>
{else}
<a class="link" href="tiki-browse_categories.php?find={$find|escape}&amp;type={$type|escape}&amp;deep=on&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Show subcategories objects{/tr}</a>
{/if}
<br /><br />
{if $path}
<div class="treetitle">{tr}Current category{/tr}:
<a href="tiki-browse_categories.php?parentId=0&amp;deep={$deep}&amp;type={$type|escape}" class="categpath">{tr}Top{/tr}</a>
{section name=x loop=$path}
&nbsp;::&nbsp;
<a class="categpath" href="tiki-browse_categories.php?parentId={$path[x].categId}&amp;deep={$deep}&amp;type={$type|escape}">{$path[x].name}</a>
{/section}
</div>

{if $parentId ne '0'}
<div class="treenode">
<a class="catname" href="tiki-browse_categories.php?parentId={$father}&amp;deep={$deep}&amp;type={$type}" title="{tr}Upper level{/tr}">..</a>
</div>
{/if}
{elseif $paths}
{section name=x loop=$paths}
{section name=y loop=$paths[x]}
&nbsp;::&nbsp;
<a class="categpath" href="tiki-browse_categories.php?parentId={$paths[x][y].categId}&amp;deep={$deep}&amp;type={$type|escape}">{$paths[x][y].name}</a>
{/section}
<br />
{/section}
{/if}

<table class="admin">
<tr><td>
<div class="tree">
{$tree}
</div>
</td>
<td width="20">&nbsp;</td>
<td>

<h3>{tr}Objects{/tr} ({$cantobjects})</h3>
{if $cantobjects > 0}
<table class="normal">
{cycle values="odd,even" print=false}
{section name=ix loop=$objects}
<tr class="{cycle}" >
{if $deep eq 'on'}<td>{$objects[ix].categName}</td>{/if}
<td>{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}</td>
<td><a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a></td>
<td>{$objects[ix].description}&nbsp;</td>
</tr>
{/section}
</table>
<br />
{/if}

</td></tr></table>


{if $cantobjects > 0}
<div align="center">
  <div class="mini">
    {if $prev_offset >= 0}
      [<a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;type={$type}&amp;parentId={$parentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
    {/if}
    {tr}Page{/tr}: {$actual_page}/{$cant_pages}
    {if $next_offset >= 0}
      &nbsp;[<a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;type={$type}&amp;parentId={$parentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
    {/if}
    {if $direct_pagination eq 'y'}
      <br />
      {section loop=$cant_pages name=foo}
        {assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
        <a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;type={$type}&amp;parentId={$parentId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
          {$smarty.section.foo.index_next}
        </a>&nbsp;
      {/section}
    {/if}
 </div>
</div>

{/if}
