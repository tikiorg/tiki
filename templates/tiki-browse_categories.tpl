<a class="pagetitle" href="tiki-browse_categories.php">Categories</a><br/><br/>
<table class="tcategpath">
<tr>
  <td class="tdcategpath">{tr}Current category{/tr}: {$path}</td>
  <td class="tdcategpath" align="right">[<a class="categpath" href="tiki-browse_categories.php?parentId={$father}">up</a>|<a class="categpath" href="tiki-browse_categories.php?parentId=0">top</a>]</td>
</tr>  
</table>
<div align="center">
<form method="post" action="tiki-browse_categories.php">
{tr}search category{/tr}: <input type="text" name="find" value="{$find}" size="35" /> {tr}deep{/tr}:<input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/>
        <input type="hidden" name="parentId" value="{$parentId}" />
        <input type="submit" value="{tr}find{/tr}" name="search" />
        <input type="hidden" name="sort_mode" value="{$sort_mode}" />
</form>
<h3>{tr}sub categories{/tr}</h3>
<table class="subcats">
<tr>
{section name=ix loop=$children}
<td class="tdsubcat"><a class="categlink" href="tiki-browse_categories.php?parentId={$children[ix].categId}">{$children[ix].name}</a></td>
{if $smarty.section.ix.index % 4 eq 3}
</tr><tr>
{/if}
{/section}
</tr>
</table>
</div>
<h3>Objects ({$cantobjects})</h3>
<table class="catobjects">
{section name=ix loop=$objects}
<tr>
  <td class="categobjectsname" valign="top"><a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a><br/>
  ({$objects[ix].type|replace:"wiki_page":"Wiki"|replace:"article":"Article"})
  </td>
  <td class="categobjectsdata" valign="top">{$objects[ix].description}</td>
</tr>
{/section}
</table>
        <br/>
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
<br/>
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;parentId={$parentId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

        </div>
      </div>
