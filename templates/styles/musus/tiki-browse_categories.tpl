{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-browse_categories.tpl,v 1.4 2004-02-02 18:44:22 musus Exp $ *}

<a class="pagetitle" href="tiki-browse_categories.php">{tr}Categories{/tr}</a>
<br /><br />


<div align="center">
<form method="post" action="tiki-browse_categories.php">
  {tr}search category{/tr}: <input type="text" name="find" value="{$find|escape}" size="35" />
  {tr}deep{/tr}:            <input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/>
                            <input type="hidden" name="parentId" value="{$parentId|escape}" />
                            <input type="submit" value="{tr}find{/tr}" name="search" />
                            <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>
</div>

<br /><br />
<div class="treetitle">{tr}Current category{/tr}: 
<a href="tiki-browse_categories.php?parentId=0" class="categpath">{tr}Top{/tr}</a>
{section name=x loop=$path}
&nbsp;::&nbsp;
<a class="categpath" href="tiki-browse_categories.php?parentId={$path[x].categId}">{$path[x].name}</a>
{/section}
</div>

{if $parentId ne '0'}
<div class="treenode">
<a class="catname" href="tiki-browse_categories.php?parentId={$father}" title="Upper level">..</a>
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
      [<a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;parentId={$parentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
    {/if}
    {tr}Page{/tr}: {$actual_page}/{$cant_pages}
    {if $next_offset >= 0}
      &nbsp;[<a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;parentId={$parentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
    {/if}
    {if $direct_pagination eq 'y'}
      <br />
      {section loop=$cant_pages name=foo}
        {assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
        <a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;parentId={$parentId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
          {$smarty.section.foo.index_next}
        </a>&nbsp;
      {/section}
    {/if}
 </div>
</div>
{/if}
</td></tr></table>

