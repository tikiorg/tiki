{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_categories.tpl,v 1.29 2004-06-13 22:03:14 teedog Exp $ *}

<a class="pagetitle" href="tiki-admin_categories.php">{tr}Admin categories{/tr}</a>

<!-- the help link info -->
  
      {if $feature_help eq 'y'}
<a href="{$helpurl}CategoryAdmin" target="tikihelp" class="tikihelp" title="{tr}admin categories{/tr}">
<img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_categories.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}admin categories template{/tr}">
<img border="0"  alt="{tr}Edit template{/tr}" src="img/icons/info.gif" /></a>{/if}

<br />
<br />

<div class="tree" id="top">
<div class="treetitle">{tr}Current category{/tr}: 
<a href="tiki-admin_categories.php?parentId=0" class="categpath">{tr}Top{/tr}</a>
{section name=x loop=$path}
&nbsp;::&nbsp;
<a class="categpath" href="tiki-admin_categories.php?parentId={$path[x].categId}">{$path[x].name}</a>
{/section}
</div>
<div>
{assign var=categ value=''}
{assign var=parent value=''}
{assign var=space value=1}
{assign var=step value='-'}
{section name=dx loop=$catree}
{if $catree[dx].parentId eq $categ}
{assign var=space value=$space+1}
{assign var=step value='o'}
{elseif $catree[dx].parentId ne $parent}
{assign var=space value=$space-1}
{assign var=step value='c'}
{else}
{assign var=step value='-'}
{/if}
{if $step eq 'c'}
</div>
{/if}
{if $step eq 'o'}
<a href="javascript:toggle('id{$catree[dx].parentId}');" class="linkmenu">&gt;&gt;&gt;</a>
</div>
<div id="id{$catree[dx].parentId}" style="display:none;">
{else}
</div>
{/if}
<div class="treenode{if $catree[dx].categId eq $smarty.request.parentId}select{/if}" style="padding-left:{$space*30+20}px;">
<!-- {$catree[dx].parentId} :: {$catree[dx].categId} :: -->
{if $catree[dx].children > 0}<i class="mini">{$catree[dx].children} {tr}Child categories{/tr}</i>{/if}
{if $catree[dx].objects > 0}<i class="mini">{$catree[dx].objects} {tr}Child categories{/tr}</i>{/if}
{assign var=categ value=$catree[dx].categId}
{assign var=parent value=$catree[dx].parentId}
<a title="{tr}delete{/tr}" class="link" href="tiki-admin_categories.php?parentId={$parent}&amp;removeCat={$categ}" title="{tr}delete{/tr}"><img  
style="margin-right:{$space*10+10}px;" border="0" src="img/icons2/delete.gif" align="right" height="12" width="12" hspace="5" vspace="1"/></a>
<a title="{tr}edit{/tr}" class="link" href="tiki-admin_categories.php?parentId={$parent}&amp;categId={$categ}" title="{tr}edit{/tr}"><img  
border="0" src="img/icons/edit.gif" height="12" width="12" hspace="5" vspace="1"/></a>
{if $catree[dx].has_perm eq 'y'}
<a title="{tr}permissions{/tr}" href="tiki-categpermissions.php?categId={$catree[dx].categId}"><img border="0" alt="{tr}permissions{/tr}" src="img/icons/key_active.gif" /></a>
{else}
<a title="{tr}permissions{/tr}" href="tiki-categpermissions.php?categId={$catree[dx].categId}"><img border="0" alt="{tr}permissions{/tr}" src="img/icons/key.gif" /></a>
{/if}
<a class="catname" href="tiki-admin_categories.php?parentId={$catree[dx].categId}">{$catree[dx].name}</a>
{/section}
</div>
</div>
</div>
</div>

<br />
<a name="editcreate"></a>
<table class="normalnoborder" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {if $categId > 0}
      {tr}Edit this category:{/tr} {$name} [<a href="tiki-admin_categories.php?parentId={$parentId}#editcreate" class="cboxtlink">{tr}create new{/tr}</a>]
      {else}
      {tr}Add new category{/tr}
      {/if}
      </div>
      <div class="cbox-data">
      <form action="tiki-admin_categories.php" method="post">
      <input type="hidden" name="categId" value="{$categId|escape}" />
      <table>
        <tr><td class="form">{tr}Parent{/tr}:</td><td class="form">
				<select name="parentId">
				<option value="0">{tr}top{/tr}</option>
				{section name=ix loop=$catree}
				<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $parentId}selected="selected"{/if}>{$catree[ix].categpath}</option>
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
      {tr}Objects in category{/tr} <b>{$categ_name}</b>  
      </div>
      <div class="cbox-data">
      
      <table class="findtable">
      <tr><td class="findtable">{tr}Find{/tr}</td>
      <td class="findtable">
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find" />
        <input type="hidden" name="parentId" value="{$parentId|escape}" />
        <input type="submit" value="{tr}find{/tr}" name="search" />
        <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
        <input type="hidden" name="find_objects" value="{$find_objects|escape}" />
        </form>
      </td>
      </tr>
      </table>
      
      <table class="normal">
      <tr>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
        <td class="heading">{tr}delete{/tr}</td>
      </tr>
      {section name=ix loop=$objects}
      <tr>
        <td class="even"><a class="link" href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:25:"(...)":true}</a></td>
        <td class="even">{$objects[ix].type}</td>
        <td class="even"><a class="link" href="tiki-admin_categories.php?parentId={$parentId}&amp;removeObject={$objects[ix].catObjectId}&amp;fromCateg={$parentId}" title="{tr}delete{/tr}"><img alt="{tr}delete{/tr}" src="img/icons2/delete2.gif" border="0" /></a></td>
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
      {tr}Add objects to category{/tr} <b>{$categ_name}</b>
      </div>
      <div class="cbox-data">
      <table class="findtable">
      <tr><td class="findtable">{tr}Find{/tr}</td>
      <td>
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find_objects" />
        <input type="hidden" name="parentId" value="{$parentId|escape}" />
        <input type="submit" value="{tr}filter{/tr}" name="search_objects" />
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
          <td class="form">{tr}page{/tr}:</td>
          <td class="form"><select name="pageName[]" multiple="multiple" size="5">{section name=ix loop=$pages}<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpage" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}article{/tr}:</td>
          <td class="form"><select name="articleId">{section name=ix loop=$articles}<option value="{$articles[ix].articleId|escape}">{$articles[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addarticle" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}blog{/tr}:</td>
          <td class="form"><select name="blogId">{section name=ix loop=$blogs}<option value="{$blogs[ix].blogId|escape}">{$blogs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addblog" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}directory{/tr}:</td>
          <td class="form"><select name="directoryId">{section name=ix loop=$directories}<option value="{$directories[ix].categId|escape}">{$directories[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="adddirectory" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}image gal{/tr}:</td>
          <td class="form"><select name="galleryId">{section name=ix loop=$galleries}<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addgallery" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}file gal{/tr}:</td>
          <td class="form"><select name="file_galleryId">{section name=ix loop=$file_galleries}<option value="{$file_galleries[ix].galleryId|escape}">{$file_galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfilegallery" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}forum{/tr}:</td>
          <td class="form"><select name="forumId">{section name=ix loop=$forums}<option value="{$forums[ix].forumId|escape}">{$forums[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addforum" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}poll{/tr}:</td>
          <td class="form"><select name="pollId">{section name=ix loop=$polls}<option value="{$polls[ix].pollId|escape}">{$polls[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpoll" value="{tr}add{/tr}" /></td>
        </tr>        
        <tr>
          <td class="form">{tr}faq{/tr}:</td>
          <td class="form"><select name="faqId">{section name=ix loop=$faqs}<option value="{$faqs[ix].faqId|escape}">{$faqs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfaq" value="{tr}add{/tr}" /></td>
        </tr> 
	   <tr>
          <td class="form">{tr}tracker{/tr}:</td>
          <td class="form"><select name="trackerId">{section name=ix loop=$trackers}<option value="{$trackers[ix].trackerId|escape}">{$trackers[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addtracker" value="{tr}add{/tr}" /></td>
        </tr>          
        <tr>
          <td class="form">{tr}quiz{/tr}:</td>
          <td class="form"><select name="quizId">{section name=ix loop=$quizzes}<option value="{$quizzes[ix].quizId|escape}">{$quizzes[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addquiz" value="{tr}add{/tr}" /></td>
        </tr>        


      </table>
      </form>
      </div>
    </div>
  </td>
<tr>
</table>
