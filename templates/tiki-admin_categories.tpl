{* $Id$ *}

<h1>
  <a class="pagetitle" href="tiki-admin_categories.php">{tr}Admin Categories{/tr}</a>
  
  {if $prefs.feature_help eq 'y'}
    <a href="{$prefs.helpurl}Categories+Admin" target="tikihelp" class="tikihelp" title="{tr}Admin Categories{/tr}">
      {icon _id='help'}
    </a>
  {/if}

  {if $prefs.feature_view_tpl eq 'y'}
    <a href="tiki-edit_templates.php?template=tiki-admin_categories.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}Admin Categories tpl{/tr}">
      {icon _id='shape_square_edit' alt='{tr}Edit template{/tr}'}
    </a>
  {/if}
</h1>

<div class="navbar">
  <a class="linkbut" href="tiki-browse_categories.php?parentId={$parentId}" title="{tr}browse the category system{/tr}">{tr}Browse Category{/tr}</a>
</div>

{if !empty($errors)}
  <div class="simplebox highlight">{section name=ix loop=$errors}{$errors[ix]}{/section}</div>
{/if}

<div class="tree" id="top">
  <div class="treetitle">{tr}Current category{/tr}: 
    <a href="tiki-admin_categories.php?parentId=0" class="categpath">{tr}Top{/tr}</a>
    {section name=x loop=$path}
      &nbsp;::&nbsp;
      <a class="categpath" href="tiki-admin_categories.php?parentId={$path[x].categId}">{$path[x].name}</a>
    {/section}
    <br />
    {tr}Current Category ID:{/tr} {$parentId}
  </div>
</div>

{section name=dx loop=$catree}
{assign var=after value=$smarty.section.dx.index_next}
{assign var=before value=$smarty.section.dx.index_prev}
{if $smarty.section.dx.index > 0 and $catree[dx].deep > $catree[$before].deep}
<div id="id{$catree[$before].categId}" style="display:{if $catree[$before].incat eq 'y'}inline{else}none{/if};">
{/if}
<div class="treenode{if $catree[dx].categId eq $smarty.request.parentId}select{/if}">
<!-- {$catree[dx].parentId} :: {$catree[dx].categId} :: -->
{if $catree[dx].children > 0}<i class="mini">{$catree[dx].children} {tr}Child categories{/tr}</i>{/if}
{if $catree[dx].objects > 0}<i class="mini">{$catree[dx].objects} {tr}Child categories{/tr}</i>{/if}
<a class="link" href="tiki-admin_categories.php?parentId={$catree[dx].parentId}&amp;categId={$catree[dx].categId}" title="{tr}Edit{/tr}">
{icon _id='page_edit' hspace="5" vspace="1"}</a>
<a class="link" href="tiki-admin_categories.php?parentId={$catree[dx].parentId}&amp;removeCat={$catree[dx].categId}" title="{tr}Delete{/tr}">
{icon _id='cross' hspace="5" vspace="1"}</a>
{if $catree[dx].has_perm eq 'y'}
<a title="{tr}Edit permissions for this category{/tr}" href="tiki-categpermissions.php?categId={$catree[dx].categId}">{icon hspace="5" vspace="1" _id='key_active' alt="{tr}Edit permissions for this category{/tr}"}</a>
{else}
<a title="{tr}Assign Permissions{/tr}" href="tiki-categpermissions.php?categId={$catree[dx].categId}">{icon hspace="5" vspace="1" _id='key' alt="{tr}Assign Permissions{/tr}"}</a>
{/if}
<span style="padding-left:{$catree[dx].deep*30+5}px;">
<a class="catname" href="tiki-admin_categories.php?parentId={$catree[dx].categId}">{$catree[dx].name}</a>
{if $catree[dx].deep < $catree[$after].deep}
<a href="javascript:toggle('id{$catree[dx].categId}');" class="linkmenu">&gt;&gt;&gt;</a></div>
{elseif $catree[dx].deep eq $catree[$after].deep}
</div>
{else}
</div>
{repeat count=$catree[dx].deep-$catree[$after].deep}</div>{/repeat}
{/if}
</span>
{/section}
</div>

<a name="editcreate"></a>
    <div class="cbox">
      <div class="cbox-title">
      {if $categId > 0}
      {tr}Edit this category:{/tr} <b>{$name}</b> [<a href="tiki-admin_categories.php?parentId={$parentId}#editcreate" class="cboxtlink">{tr}Create New{/tr}</a>]
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
				<option value="0">{tr}Top{/tr}</option>
				{section name=ix loop=$catree}
				<option value="{$catree[ix].categId|escape}" {if $catree[ix].categId eq $parentId}selected="selected"{/if}>{$catree[ix].categpath}</option>
				{/section}
				</select>
				</td></tr>
        <tr><td class="form">{tr}Name{/tr}:</td><td class="form"><input type="text" name="name" value="{$name|escape}" /></td></tr>
        <tr><td class="form">{tr}Description{/tr}:</td><td class="form"><textarea rows="2" cols="40" name="description">{$description|escape}</textarea></td></tr>
        {if $categId <= 0}<tr><td class="form"><label for="assign_perms" title="{tr}Perms inherited from closest parent if possible or from global perms{/tr}">{tr}Assign permissions automatically{/tr}:<br /><i>({tr}recommended for best performance{/tr})</i></label></td>
        <td class="form"><input type="checkbox" name="assign_perms" id="assign_perms" checked="{$assign_perms}" /></td></tr>
        {else}<tr><td class="form" colspan="2"><a href="tiki-categpermissions.php?categId={$categId}">{tr}Edit permissions for this category{/tr}</a></td></tr>
        {/if}
        <tr><td class="form" align="center" colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
      </table>
      </form>
      </div>
    </div>

{if $categId <= 0}
<div class="cbox">
<div class="cbox-title">{tr}Batch upload (CSV file):{/tr} <a {popup text='category,description,parent<br />vegetable,vegetable<br />potato,,vegetable'}>{icon _id='help'}</a></div>
<div class="cbox-data"><form action="tiki-admin_categories.php" method="post" enctype="multipart/form-data"><input type="file" name="csvlist" /><br /><input type="submit" name="import" value="{tr}Add{/tr}" /></form>
</div></div>
{/if}

<a name="objects"></a>
    <div class="cbox">
      <div class="cbox-title">
      {tr}Objects in category{/tr} <b>{$categ_name}</b>  
      </div>
      <div class="cbox-data">
{if $objects}      
      <table class="findtable">
      <tr><td class="findtable">{tr}Find{/tr}</td>
      <td class="findtable">
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find" />
        <input type="hidden" name="parentId" value="{$parentId|escape}" />
        <input type="submit" value="{tr}Find{/tr}" name="search" />
        <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
        <input type="hidden" name="find_objects" value="{$find_objects|escape}" />
        </form>
      </td>
      </tr>
      </table>
{/if}      
      <table class="normal">
      <tr>
        <td class="heading">&nbsp;</td>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}#objects">{tr}Name{/tr}</a></td>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}#objects">{tr}Type{/tr}</a></td>
      </tr>
      {section name=ix loop=$objects}
      <tr>
        <td class="even"><a class="link" href="tiki-admin_categories.php?parentId={$parentId}&amp;removeObject={$objects[ix].catObjectId}&amp;fromCateg={$parentId}" title="{tr}Remove from this Category{/tr}">{icon _id='link_delete' alt="{tr}Remove from this Category{/tr}"}</a></td>
        <td class="even"><a class="link" href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:25:"(...)":true}</a></td>
        <td class="even">{tr}{$objects[ix].type}{/tr}</td>
      </tr>
{sectionelse}
      <tr><td class="even" colspan="3"><strong>{tr}No records found.{/tr}</strong></td></tr>
      {/section}
      </table>
      
        <div class="mini">
        {if $prev_offset >= 0}
          [<a class="prevnext" href="tiki-admin_categories.php?find={$find}&amp;parentId={$parentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}#objects">{tr}Prev{/tr}</a>]&nbsp;
        {/if}
        {tr}Page{/tr}: {$actual_page}/{if $cant_pages eq 0}1{else}{$cant_pages}{/if}
        {if $next_offset >= 0}
          &nbsp;[<a class="prevnext" href="tiki-admin_categories.php?find={$find}&amp;parentId={$parentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}#objects">{tr}Next{/tr}</a>]
        {/if}
        </div>

      
      </div>
    </div>

{if $parentId != 0}
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
        <input type="submit" value="{tr}Filter{/tr}" name="search_objects" />
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
				{if $prefs.feature_wiki eq 'y'}
        <tr>
          <td class="form">{tr}page{/tr}:</td>
          <td class="form"><select name="pageName[]" multiple="multiple" size="5">{section name=ix loop=$pages}<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpage" value="{tr}Add{/tr}" /></td>
        </tr>
				{/if}
				{if $prefs.feature_cms eq 'y'}
        <tr>
          <td class="form">{tr}Article{/tr}:</td>
          <td class="form"><select name="articleId">{section name=ix loop=$articles}<option value="{$articles[ix].articleId|escape}">{$articles[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addarticle" value="{tr}Add{/tr}" /></td>
        </tr>
				{/if}
				{if $prefs.feature_blogs eq 'y'}
        <tr>
          <td class="form">{tr}Blog{/tr}:</td>
          <td class="form"><select name="blogId">{section name=ix loop=$blogs}<option value="{$blogs[ix].blogId|escape}">{$blogs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addblog" value="{tr}Add{/tr}" /></td>
        </tr>
				{/if}
				{if $prefs.feature_directories eq 'y'}
        <tr>
          <td class="form">{tr}Directory{/tr}:</td>
          <td class="form"><select name="directoryId">{section name=ix loop=$directories}<option value="{$directories[ix].categId|escape}">{$directories[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="adddirectory" value="{tr}Add{/tr}" /></td>
        </tr>
				{/if}
				{if $prefs.feature_galleries eq 'y'}
        <tr>
          <td class="form">{tr}image gal{/tr}:</td>
          <td class="form"><select name="galleryId">{section name=ix loop=$galleries}<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addgallery" value="{tr}Add{/tr}" /></td>
        </tr>
				{/if}
				{if $prefs.feature_file_galleries eq 'y'}
        <tr>
          <td class="form">{tr}file gal{/tr}:</td>
          <td class="form"><select name="file_galleryId">{section name=ix loop=$file_galleries}<option value="{$file_galleries[ix].galleryId|escape}">{$file_galleries[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfilegallery" value="{tr}Add{/tr}" /></td>
        </tr>
				{/if}
				{if $prefs.feature_forums eq 'y'}
        <tr>
          <td class="form">{tr}Forum{/tr}:</td>
          <td class="form"><select name="forumId">{section name=ix loop=$forums}<option value="{$forums[ix].forumId|escape}">{$forums[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addforum" value="{tr}Add{/tr}" /></td>
        </tr>
				{/if}
				{if $prefs.feature_polls eq 'y'}
        <tr>
          <td class="form">{tr}Poll{/tr}:</td>
          <td class="form"><select name="pollId">{section name=ix loop=$polls}<option value="{$polls[ix].pollId|escape}">{$polls[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpoll" value="{tr}Add{/tr}" /></td>
        </tr>        
				{/if}
				{if $prefs.feature_faqs eq 'y'}
        <tr>
          <td class="form">{tr}FAQ{/tr}:</td>
          <td class="form"><select name="faqId">{section name=ix loop=$faqs}<option value="{$faqs[ix].faqId|escape}">{$faqs[ix].title|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfaq" value="{tr}Add{/tr}" /></td>
        </tr> 
				{/if}
				{if $prefs.feature_trackers eq 'y'}
	   <tr>
          <td class="form">{tr}Tracker{/tr}:</td>
          <td class="form"><select name="trackerId">{section name=ix loop=$trackers}<option value="{$trackers[ix].trackerId|escape}">{$trackers[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addtracker" value="{tr}Add{/tr}" /></td>
        </tr>          
				{/if}
				{if $prefs.feature_quizzes eq 'y'}
        <tr>
          <td class="form">{tr}quiz{/tr}:</td>
          <td class="form"><select name="quizId">{section name=ix loop=$quizzes}<option value="{$quizzes[ix].quizId|escape}">{$quizzes[ix].name|truncate:40:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addquiz" value="{tr}Add{/tr}" /></td>
        </tr>        
				{/if}

      </table>
      </form>
      </div>
    </div>
{/if}
