{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/tiki-admin_categories.tpl,v 1.1 2004-05-09 23:07:57 damosoft Exp $ *}

<a class="pagetitle" href="tiki-admin_categories.php">{tr}Admin Categories{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=CategoryAdmin" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}admin categories{/tr}">
<img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>{/if}

<!-- link to tpl -->

      {if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_categories.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin categories tpl{/tr}">
<img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>{/if}

<!-- begin -->

<br />
<br />

<div class="tree" id="top">
<table class="tcategpath" >
<tr>
  <td class="tdcategpath">{tr}Current Category{/tr}: {$path} </td>
  <td class="tdcategpath" align="right">
  <table><tr><td>
  {* Don't show 'TOP' button if we already on TOP but reserve space to avoid visual effects on change view *}
  <div class="button2" style="visibility:{if $parentId ne '0'}visible{else}hidden{/if}">
      <a class="linkbut" href="tiki-admin_categories.php?parentId=0">{tr}Top{/tr}</a>
    </div>
  </td></tr></table></td>
</tr>
</table>

{* Show tree *}
{ * If not TOP level, append '..' as first node :) *}
{if $parentId ne '0'}
<div class="treenode">
  <a class="catname" href="tiki-admin_categories.php?parentId={$father}" title="Upper level"><img src="./img/icons/up.gif" border=0 /></a>
</div>
{/if}
{$tree}
</div>


<br />
<a name="editcreate"></a>
<table class="normalnoborder" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {if $categId > 0}
      {tr}Edit this category:{/tr} {$name} [<a href="tiki-admin_categories.php?parentId={$parentId}#editcreate" class="cboxtlink">{tr}Create new{/tr}</a>]
      {else}
      {tr}Add New Category{/tr}
      {/if}
      </div>
      <div class="cbox-data">
      <form action="tiki-admin_categories.php" method="post">
      <input type="hidden" name="categId" value="{$categId|escape}" />
      <table>
        <tr><td class="form">{tr}Parent{/tr}:</td><td class="form">
				<select name="parentId">
				<option value="0">{tr}Top{/tr}</option>
				{section name=ix loop=$categories}
				<option value="{$categories[ix].categId|escape}" {if $categories[ix].categId eq $parentId}selected="selected"{/if}>{$categories[ix].name}</option>
				{/section}
				</select>
				</td></tr>
        <tr><td class="form">{tr}Name{/tr}:</td><td class="form"><input type="text" name="name" value="{$name|escape}" /></td></tr>
        <tr><td class="form">{tr}Description{/tr}:</td><td class="form"><textarea rows="4" cols="16" name="description">{$description|escape}</textarea></td></tr>
        <tr><td class="form" align="center" colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
      </table>
      </form>
      </div>
    </div>
  </td>
</tr>
</table>
<br />
<table class="normalnoborder" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {tr}Objects in Category{/tr}  
      </div>
      <div class="cbox-data">
      
      <table class="findtable">
      <tr><td class="findtable">{tr}Search{/tr}</td>
      <td class="findtable">
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find" />
        <input type="hidden" name="parentId" value="{$parentId|escape}" />
        <input type="submit" value="{tr}Go{/tr}" name="search" />
        <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
        <input type="hidden" name="find_objects" value="{$find_objects|escape}" />
        </form>
      </td>
      </tr>
      </table>
      
      <table class="normal">
      <tr>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a></td>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}Type{/tr}</a></td>
        <td class="heading">{tr}Delete{/tr}</td>
      </tr>
      {section name=ix loop=$objects}
      <tr>
        <td class="even"><a class="link" href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:25:"(...)":true}</a></td>
        <td class="even">{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|replace:"directory":"Directory"|replace:"image gallery":"Image Gallery"|replace:"file gallery":"File Gallery"|replace:"forum":"Forum"|replace:"faq":"FAQ"}</td>
        <td class="even"><a class="link" href="tiki-admin_categories.php?parentId={$parentId}&amp;removeObject={$objects[ix].catObjectId}&amp;fromCateg={$parentId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this category?{/tr}')" title="{tr}Delete item from category?{/tr}"><img alt="{tr}Remove{/tr}" src="img/icons2/delete2.gif" border="0" /></a></td>
      </tr>
      {/section}
      </table>
      
      <div align="center">
        <div class="mini">
        {if $prev_offset >= 0}
          [<a class="prevnext" href="tiki-admin_categories.php?find={$find}&amp;parentId={$parentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
        {/if}
        {tr}Page{/tr}: {$actual_page}/{if $cant_pages eq 0}1{else}{$cant_pages}{/if}
        {if $next_offset >= 0}
          &nbsp;[<a class="prevnext" href="tiki-admin_categories.php?find={$find}&amp;parentId={$parentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
        {/if}
        </div>
      </div>

      
      </div>
    </div>
  </td>
  </tr>
  </table>
  <br />
<table class="normalnoborder" cellpadding="0" cellspacing="0">  
  <tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {tr}Add Objects to Category{/tr}
      </div>
      <div class="cbox-data">
      <table class="findtable">
      <tr><td class="findtable">{tr}Filter{/tr}</td>
      <td>
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find_objects" />
        <input type="hidden" name="parentId" value="{$parentId|escape}" />
        <input type="submit" value="{tr}Go{/tr}" name="search_objects" />
        <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
        <input type="hidden" name="offset" value="{$offset|escape}" />
        <input type="hidden" name="find" value="{$find|escape}" />
        </form>
      </td>
      </tr>
      </table>
      <form action="tiki-admin_categories.php" method="post">
      <input type="hidden" name="parentId" value="{$parentId|escape}" />
      <table>
        <tr>
          <td class="form">{tr}Page{/tr}:</td>
          <td class="form"><select name="pageName[]" multiple="multiple" size="5">{section name=ix loop=$pages}<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpage" value="{tr}Add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Article{/tr}:</td>
          <td class="form"><select name="articleId">{section name=ix loop=$articles}<option value="{$articles[ix].articleId|escape}">{$articles[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addarticle" value="{tr}Add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Blog{/tr}:</td>
          <td class="form"><select name="blogId">{section name=ix loop=$blogs}<option value="{$blogs[ix].blogId|escape}">{$blogs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addblog" value="{tr}Add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Directory{/tr}:</td>
          <td class="form"><select name="directoryId">{section name=ix loop=$directories}<option value="{$directories[ix].categId|escape}">{$directories[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="adddirectory" value="{tr}Add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Image gal{/tr}:</td>
          <td class="form"><select name="galleryId">{section name=ix loop=$galleries}<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addgallery" value="{tr}Add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}File gal{/tr}:</td>
          <td class="form"><select name="file_galleryId">{section name=ix loop=$file_galleries}<option value="{$file_galleries[ix].galleryId|escape}">{$file_galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfilegallery" value="{tr}Add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Forum{/tr}:</td>
          <td class="form"><select name="forumId">{section name=ix loop=$forums}<option value="{$forums[ix].forumId|escape}">{$forums[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addforum" value="{tr}Add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}Poll{/tr}:</td>
          <td class="form"><select name="pollId">{section name=ix loop=$polls}<option value="{$polls[ix].pollId|escape}">{$polls[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpoll" value="{tr}Add{/tr}" /></td>
        </tr>        
        <tr>
          <td class="form">{tr}FAQ{/tr}:</td>
          <td class="form"><select name="faqId">{section name=ix loop=$faqs}<option value="{$faqs[ix].faqId|escape}">{$faqs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfaq" value="{tr}Add{/tr}" /></td>
        </tr> 
	   <tr>
          <td class="form">{tr}Tracker{/tr}:</td>
          <td class="form"><select name="trackerId">{section name=ix loop=$trackers}<option value="{$trackers[ix].trackerId|escape}">{$trackers[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addtracker" value="{tr}Add{/tr}" /></td>
        </tr>          
        <tr>
          <td class="form">{tr}Quiz{/tr}:</td>
          <td class="form"><select name="quizId">{section name=ix loop=$quizzes}<option value="{$quizzes[ix].quizId|escape}">{$quizzes[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addquiz" value="{tr}Add{/tr}" /></td>
        </tr>        


      </table>
      </form>
      </div>
    </div>
  </td>
<tr>
</table>
