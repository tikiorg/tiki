{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-browse_categories.tpl,v 1.11 2005-01-22 22:56:21 mose Exp $ *}

<a class="pagetitle" href="tiki-browse_categories.php">{tr}Categories{/tr}</a>
<br /><br />
{if $tiki_p_admin_categories eq 'y'}
<a class="linkbut" href="tiki-admin_categories.php?parentId={$parentId}" title="admin the category system">{tr}admin category{/tr}</a>
<br /><br />
{/if}
{tr}Browse in{/tr}:<br />
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}All{/tr}</a>
{if $feature_wiki eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=wiki+page&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Wiki pages{/tr}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Image galleries{/tr}</a>
{/if}
{if $feature_galleries eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Images{/tr}</a>
{/if}
{if $feature_file_galleries eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=file+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}File galleries{/tr}</a>
{/if}
{if $feature_blogs eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=blog&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Blogs{/tr}</a>
{/if}
{if $feature_trackers eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=tracker&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Trackers{/tr}</a>
{/if}
{if $feature_quizzes eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=quiz&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Quizzes{/tr}</a>
{/if}
{if $feature_polls eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=poll&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Polls{/tr}</a>
{/if}
{if $feature_surveys eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=survey&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Surveys{/tr}</a>
{/if}
{if $feature_directory eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=directory&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Directory{/tr}</a>
{/if}
{if $feature_faqs eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=faq&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}FAQs{/tr}</a>
{/if}
{if $feature_sheet eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=sheet&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Sheets{/tr}</a>
{/if}
{if $feature_articles eq 'y'}
<a class="linkbut" href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=article&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Articles{/tr}</a>
{/if}
<br /><br />
<form method="post" action="tiki-browse_categories.php">
  {tr}search category{/tr}: <input type="text" name="find" value="{$find|escape}" size="35" />
  {tr}deep{/tr}:            <input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/>
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
<div class="treetitle">{tr}Current category{/tr}: 
<a href="tiki-browse_categories.php?parentId=0&amp;deep={$deep}&amp;type={$type|escape}" class="categpath">{tr}Top{/tr}</a>
{section name=x loop=$path}
&nbsp;::&nbsp;
<a class="categpath" href="tiki-browse_categories.php?parentId={$path[x].categId}&amp;deep={$deep}&amp;type={$type|escape}">{$path[x].name}</a>
{/section}
</div>

{if $parentId ne '0'}
<div class="treenode">
<a class="catname" href="tiki-browse_categories.php?parentId={$father}&amp;deep={$deep}&amp;type={$type}" title="Upper level">..</a>
</div>
{/if}

<table width="100%">
<tr><td>
<div class="tree">
{$tree}
</div>
</td
<td width="20">&nbsp;</td>
<td>

<h3>{tr}Objects{/tr} ({$cantobjects})</h3>
{if $cantobjects > 0}
<table class="normal">
{cycle values="odd,even" print=false}
{section name=ix loop=$objects}
<tr class="{cycle}" >
<td>{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"}{/tr}</td>
<td><a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a></td>
<td>{$objects[ix].description}&nbsp;</td>
</tr>
{/section}
</table>
<br />

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
</td></tr></table>

