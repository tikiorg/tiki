{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-browse_categories.tpl,v 1.1 2004-05-09 23:09:15 damosoft Exp $ *}

<a class="pagetitle" href="tiki-browse_categories.php">{tr}Categories{/tr}</a>
<br /><br />

{* Current position and basic navigation *}


{* Search form *}

<div align="center">
<form method="post" action="tiki-browse_categories.php">
  {tr}Search category{/tr}: <input type="text" name="find" value="{$find|escape}" size="35" />
  {tr}deep{/tr}:            <input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/>
                            <input type="hidden" name="parentId" value="{$parentId|escape}" />
                            <input type="submit" value="{tr}Go{/tr}" name="search" />
                            <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>
</div>

<br /><br /><br />

<div class="tree" id="top">
<table class="tcategpath">
<tr>
  <td class="tdcategpath">{tr}Current Category{/tr}: {$path} </td>
  <td class="tdcategpath" align="right">
  <table><tr><td>
  {* Don't show 'TOP' button if we already on TOP but resserve space to avoid visual effects on change view *}
  <div class="button2" style="visibility:{if $parentId ne '0'}visible{else}hidden{/if}">
      <a class="linkbut" href="tiki-browse_categories.php?parentId=0">{tr}Top{/tr}</a>
    </div>
  </td></tr></table></td>
</tr>
</table>

{* Show tree *}
{ * If not TOP level, append '..' as first node :) *}
{if $parentId ne '0'}
<div class="treenode">
  <a class="catname" href="tiki-browse_categories.php?parentId={$father}" title="Upper level"><img src="./img/icons/up.gif" border=0 /></a>
</div>
{/if}
{$tree}
</div>

{* List of object in category *}

<h3>{tr}Objects{/tr} ({$cantobjects})</h3>
{if $cantobjects > 0}
<table class="catobjects">
{cycle values="odd,even" print=false}
{section name=ix loop=$objects}
<tr>
  <td class="categobjectsname{cycle advance=false}" valign="top">
    <a href="{$objects[ix].href}" class="catname">{$objects[ix].name}</a><br />
    ({tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|replace:"directory":"Directory"|replace:"image gallery":"Image Gallery"|replace:"file gallery":"File Gallery"|replace:"forum":"Forum"|replace:"faq":"FAQ"}{/tr}&nbsp;)
  </td>
  <td class="categobjectsdata{cycle}" valign="top">{$objects[ix].description}&nbsp;</td>
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
