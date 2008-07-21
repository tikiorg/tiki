<h1><a class="pagetitle" href="tiki-directory_admin_categories.php?parent={$parent}">{tr}Admin directory categories{/tr}</a>

      {if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Categories" target="tikihelp" class="tikihelp" title="{tr}Categories{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>{/if}

      {if $prefs.feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-directory_admin_categories.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Directory Categories tpl{/tr}">
<img src="img/icons/info.gif" border="0" height="16" width="16" alt='{tr}Edit Tpl{/tr}' /></a>{/if}</h1>

{* Display the title using parent *}
{include file=tiki-directory_admin_bar.tpl}
{* Navigation bar to admin, admin related, etc *}

<h2>{tr}Parent category{/tr}:</h2>
{* Display the path adding manually the top category id=0 *}
<form name="path" method="post" action="tiki-directory_admin_categories.php">
<select name="parent" onchange="javascript:path.submit();">
<option value="0">{tr}Top{/tr}</option>
{section name=ix loop=$categs}
<option value="{$categs[ix].categId|escape}" {if $parent eq $categs[ix].categId}selected="selected"{/if}>{$categs[ix].path}</option>
{/section}
</select>
<input type="submit" name="go" value="{tr}Go{/tr}" />
</form>

{* Dislay a form to add or edit a category *}
<br />{if $categId eq 0}
<h2>{tr}Add a directory category{/tr}</h2>
{else}
<h2>{tr}Edit this directory category{/tr}: {$info.name}</h2>
<a href="tiki-directory_admin_categories.php">{tr}Add a directory category{/tr}</a>
{/if}
<form action="tiki-directory_admin_categories.php" method="post">
<input type="hidden" name="parent" value="{$parent|escape}" />
<input type="hidden" name="categId" value="{$categId|escape}" />
<table class="normal">
  <tr>
    <td class="formcolor">{tr}Name{/tr}:</td>
    <td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" />
  </tr>
  <tr>
    <td class="formcolor">{tr}Description{/tr}:</td>
    <td class="formcolor"><textarea rows="5" cols="60" name="description">{$info.description|escape}</textarea></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Children type{/tr}:</td>
    <td class="formcolor">
       <select name="childrenType">
         <option value='c' {if $info.childrenType eq 'c'}selected="selected"{/if}>{tr}Most visited sub-categories{/tr}</option>
         <option value='d' {if $info.childrenType eq 'd'}selected="selected"{/if}>{tr}Category description{/tr}</option>
         <option value='r' {if $info.childrenType eq 'r'}selected="selected"{/if}>{tr}Random sub-categories{/tr}</option>
       </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Maximum number of children to show{/tr}:</td>
    <td class="formcolor">
      <select name="viewableChildren">
        <option value="0" {if $info.viewableChildren eq 0}selected="selected"{/if}>{tr}none{/tr}</option>
        <option value="1" {if $info.viewableChildren eq 1}selected="selected"{/if}>1</option>
        <option value="2" {if $info.viewableChildren eq 2}selected="selected"{/if}>2</option>
        <option value="3" {if $info.viewableChildren eq 3}selected="selected"{/if}>3</option>
        <option value="4" {if $info.viewableChildren eq 4}selected="selected"{/if}>4</option>
        <option value="5" {if $info.viewableChildren eq 5}selected="selected"{/if}>5</option>
      </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Allow sites in this category{/tr}:</td>
    <td class="formcolor"><input name="allowSites" type="checkbox" {if $info.allowSites eq 'y'}checked="checked"{/if} /></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Show number of sites in this category{/tr}:</td>
    <td class="formcolor"><input name="showCount" type="checkbox" {if $info.showCount eq 'y'}checked="checked"{/if} /></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Editor group{/tr}:</td>
    <td class="formcolor">
      <select name="editorGroup">
        <option value="">{tr}None{/tr}</option>
        {section name=ux loop=$groups}
        <option value="{$groups[ux]|escape}" {if $editorGroup eq $groups[ux]}selected="selected"{/if}>{$groups[ux]}</option>
        {/section}
      </select>
    </td>
  </tr>
  {include file=categorize.tpl}
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" />
  </tr>
</table>
</form>
<br />
<h2>{tr}Subcategories{/tr}</h2>

{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<table class="normal">
  <tr>
    <th class="heading"><a class="tableheading" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
    <th class="heading"><a class="tableheading" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'childrenType_desc'}childrenType_asc{else}childrenType_desc{/if}">{tr}cType{/tr}</a></th>
    <th class="heading"><a class="tableheading" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'viewableChildren_desc'}viewableChildren_asc{else}viewableChildren_desc{/if}">{tr}View{/tr}</a></th>
    <th class="heading"><a class="tableheading" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'allowSites_desc'}allowSites_asc{else}allowSites_desc{/if}">{tr}allow{/tr}</a></th>
    <th class="heading"><a class="tableheading" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'showCount_desc'}showCount_asc{else}showCount_desc{/if}">{tr}count{/tr}</a></th>
    <th class="heading"><a class="tableheading" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'editorGroup_desc'}editorGroup_asc{else}editorGroup_desc{/if}">{tr}editor{/tr}</a></th>
    <th class="heading">{tr}Action{/tr}</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr>
<td class="{cycle advance=false}"><a class="tablename" href="tiki-directory_admin_categories.php?parent={$items[user].categId}">{$items[user].name}</a></td>
<td class="{cycle advance=false}">{$items[user].childrenType}</td>
<td class="{cycle advance=false}">{$items[user].viewableChildren}</td>
<td class="{cycle advance=false}">{$items[user].allowSites}{if $items[user].allowSites eq 'y'} ({$items[user].sites}) {/if}</td>
<td class="{cycle advance=false}">{$items[user].showCount}</td>
<td class="{cycle advance=false}">{$items[user].editorGroup}</td>
<td class="{cycle}">
   <a class="link" href="tiki-directory_admin_related.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;categId={$items[user].categId}"><img src='img/icons2/admin_move.gif' border='0' alt='{tr}relate{/tr}' title='{tr}relate{/tr}' /></a>
   <a class="link" href="tiki-directory_admin_categories.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;parent={$parent}&amp;categId={$items[user].categId}">{icon _id='page_edit'}</a>
   <a class="link" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].categId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
</td>
</tr>
{sectionelse}<tr><td class="odd" colspan="7">{tr}No records found.{/tr}</td></tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-directory_admin_categories.php?parent={$parent}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-directory_admin_categories.php?parent={$parent}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $prefs.direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
<a class="prevnext" href="tiki-directory_admin_categories.php?parent={$parent}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
