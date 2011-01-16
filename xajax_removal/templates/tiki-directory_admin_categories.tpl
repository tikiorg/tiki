{title help="Directory Categories" url="tiki-directory_admin_categories.php?parent=$parent"}{tr}Admin directory categories{/tr}{/title}

{* Display the title using parent *}
{include file='tiki-directory_admin_bar.tpl'}
{* Navigation bar to admin, admin related, etc *}
<h2>{tr}Parent directory category:{/tr}</h2>
{* Display the path adding manually the top category id=0 *}
<form name="path" method="post" action="tiki-directory_admin_categories.php">
  <select name="parent" onchange="javascript:path.submit();">
    <option value="0">{tr}Top{/tr}</option>
    
{section name=ix loop=$categs}

    <option value="{$categs[ix].categId|escape}" {if $parent eq $categs[ix].categId}selected="selected"{/if}>{$categs[ix].path|escape}</option>
    
{/section}

  </select>
  <input type="submit" name="go" value="{tr}Go{/tr}" />
</form>
{* Dislay a form to add or edit a category *} <br />
{if $categId eq 0}
<h2>{tr}Add a directory category{/tr}</h2>
{else}
<h2>{tr}Edit this directory category:{/tr} {$info.name}</h2>
<a href="tiki-directory_admin_categories.php">{tr}Add a Directory Category{/tr}</a> {/if}
<form action="tiki-directory_admin_categories.php" method="post">
  <input type="hidden" name="parent" value="{$parent|escape}" />
  <input type="hidden" name="categId" value="{$categId|escape}" />
  <table class="formcolor">
    <tr>
      <td>{tr}Name:{/tr}</td>
      <td><input type="text" name="name" value="{$info.name|escape}" />
    </tr>
    <tr>
      <td>{tr}Description:{/tr}</td>
      <td><textarea rows="5" cols="60" name="description">{$info.description|escape}</textarea></td>
    </tr>
    <tr>
      <td>{tr}Children type:{/tr}</td>
      <td><select name="childrenType">
          <option value='c' {if $info.childrenType eq 'c'}selected="selected"{/if}>{tr}Most visited directory sub-categories{/tr}</option>
          <option value='d' {if $info.childrenType eq 'd'}selected="selected"{/if}>{tr}Directory Category description{/tr}</option>
          <option value='r' {if $info.childrenType eq 'r'}selected="selected"{/if}>{tr}Random directory sub-categories{/tr}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>{tr}Maximum number of children to show:{/tr}</td>
      <td><select name="viewableChildren">
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
      <td>{tr}Allow sites in this directory category:{/tr}</td>
      <td><input name="allowSites" type="checkbox" {if $info.allowSites eq 'y'}checked="checked"{/if} /></td>
    </tr>
    <tr>
      <td>{tr}Show number of sites in this directory category:{/tr}</td>
      <td><input name="showCount" type="checkbox" {if $info.showCount eq 'y'}checked="checked"{/if} /></td>
    </tr>
    <tr>
      <td>{tr}Editor group:{/tr}</td>
      <td><select name="editorGroup">
          <option value="">{tr}None{/tr}</option>
          
        {section name=ux loop=$groups}
        
          <option value="{$groups[ux]|escape}" {if $editorGroup eq $groups[ux]}selected="selected"{/if}>{$groups[ux]}</option>
          
        {/section}
      
        </select>
      </td>
    </tr>
    {include file='categorize.tpl'}
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="save" value="{tr}Save{/tr}" />
    </tr>
  </table>
</form>
<br />
<h2>{tr}Directory Subcategories{/tr}</h2>
{* Display the list of categories (items) using pagination *}
{* Links to edit, remove, browse the categories *}
<table class="normal">
  <tr>
    <th><a href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></th>
    <th><a href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'childrenType_desc'}childrenType_asc{else}childrenType_desc{/if}">{tr}cType{/tr}</a></th>
    <th><a href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'viewableChildren_desc'}viewableChildren_asc{else}viewableChildren_desc{/if}">{tr}View{/tr}</a></th>
    <th><a href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'allowSites_desc'}allowSites_asc{else}allowSites_desc{/if}">{tr}allow{/tr}</a></th>
    <th><a href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'showCount_desc'}showCount_asc{else}showCount_desc{/if}">{tr}count{/tr}</a></th>
    <th><a href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'editorGroup_desc'}editorGroup_asc{else}editorGroup_desc{/if}">{tr}editor{/tr}</a></th>
    <th>{tr}Action{/tr}</th>
  </tr>
  {cycle values="odd,even" print=false}
  {section name=user loop=$items}
  <tr class="{cycle}">
    <td><a class="tablename" href="tiki-directory_admin_categories.php?parent={$items[user].categId}">{$items[user].name|escape}</a></td>
    <td>{$items[user].childrenType}</td>
    <td>{$items[user].viewableChildren}</td>
    <td>{$items[user].allowSites}{if $items[user].allowSites eq 'y'} ({$items[user].sites}) {/if}</td>
    <td>{$items[user].showCount}</td>
    <td>{$items[user].editorGroup}</td>
    <td><a class="link" href="tiki-directory_admin_related.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;categId={$items[user].categId}"><img src='img/icons2/admin_move.gif' alt="{tr}relate{/tr}" title="{tr}relate{/tr}" /></a> <a class="link" href="tiki-directory_admin_categories.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;parent={$parent}&amp;categId={$items[user].categId}">{icon _id='page_edit'}</a> <a class="link" href="tiki-directory_admin_categories.php?parent={$parent}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$items[user].categId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a> </td>
  </tr>
  {sectionelse}
		{norecords _colspan=7}
  {/section}
</table>
{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links} 
