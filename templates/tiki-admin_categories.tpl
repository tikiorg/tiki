<a class="pagetitle" href="tiki-admin_categories.php">{tr}Admin categories{/tr}</a>
<h3>{tr}Current category{/tr}: {$path}</h3>
<table class="normalnoborder" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {tr}Child categories{/tr} [<a href="tiki-admin_categories.php?parentId={$father}" class="cboxtlink">{tr}up{/tr}</a>|<a href="tiki-admin_categories.php?parentId=0" class="cboxtlink">{tr}top{/tr}</a>] 
      </div>
      <div class="cbox-data">
        <table class="normal">
        <tr>
          <td class="heading">{tr}name{/tr}</td>
          <td class="heading">{tr}subs{/tr}</td>
          <td class="heading">{tr}objs{/tr}</td>
          <td class="heading">&nbsp;</td>
        </tr>
        {section name=ix loop=$children}
        <tr>
          <td class="even">
            <a class="link" href="tiki-admin_categories.php?parentId={$children[ix].categId}">{$children[ix].name}</a>
          </td>
          <td class="even">
            {$children[ix].children}
          </td>
          <td class="even">
            {$children[ix].objects}
          </td>
          <td>
            [<a class="link" href="tiki-admin_categories.php?parentId={$children[ix].parentId}&amp;categId={$children[ix].categId}">{tr}edit{/tr}</a>|<a class="link" href="tiki-admin_categories.php?parentId={$parentId}&amp;removeCat={$children[ix].categId}">{tr}x{/tr}</a>]
          </td>
        </tr>
        {/section}
        </table>
      </div>
    </div>
  </td>
</tr>
<tr>  
</table>
<br/>
<table class="normalnoborder" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {tr}Edit or add category{/tr}  [<a href="tiki-admin_categories.php?parentId={$parentId}" class="cboxtlink">{tr}new{/tr}</a>]
      </div>
      <div class="cbox-data">
      <form action="tiki-admin_categories.php" method="post">
      <input type="hidden" name="parentId" value="{$parentId}" />
      <input type="hidden" name="categId" value="{$categId}" />
      <table>
        <tr><td class="form">{tr}Name{/tr}:</td><td class="form"><input type="text" name="name" value="{$name}" /></td></tr>
        <tr><td class="form">{tr}Description{/tr}:</td><td class="form"><textarea rows="4" cols="16" name="description">{$description}</textarea></td></tr>
        <tr><td class="form" align="center" colspan="2"><input type="submit" name="save" value="{tr}save{/tr}" /></td></tr>
      </table>
      </form>
      </div>
    </div>
  </td>
</tr>
</table>
<br/>
<table class="normalnoborder" cellpadding="0" cellspacing="0">
<tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {tr}Objects in category{/tr}  
      </div>
      <div class="cbox-data">
      
      <table class="findtable">
      <tr><td class="findtable">{tr}Find{/tr}</td>
      <td class="findtable">
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find" />
        <input type="hidden" name="parentId" value="{$parentId}" />
        <input type="submit" value="{tr}find{/tr}" name="search" />
        <input type="hidden" name="sort_mode" value="{$sort_mode}" />
        <input type="hidden" name="find_objects" value="{$find_objects}" />
        </form>
      </td>
      </tr>
      </table>
      
      <table class="normal">
      <tr>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
        <td class="heading"><a class="tableheading" href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
        <td class="heading">&nbsp;</td>
      </tr>
      {section name=ix loop=$objects}
      <tr>
        <td class="even"><a class="link" href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:25:"(...)":true}</a></td>
        <td class="even">{$objects[ix].type}</td>
        <td class="even">[<a class="link" href="tiki-admin_categories.php?parentId={$parentId}&amp;removeObject={$objects[ix].catObjectId}&amp;fromCateg={$parentId}">{tr}x{/tr}</a>]</td>
      </tr>
      {/section}
      </table>
      
      <div align="center">
        <div class="mini">
        {if $prev_offset >= 0}
          [<a class="prevnext" href="tiki-admin_categories.php?find={$find}&amp;parentId={$parentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
        {/if}
        {tr}Page{/tr}: {$actual_page}/{$cant_pages}
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
  <br/>
<table class="normalnoborder" cellpadding="0" cellspacing="0">  
  <tr>
  <td valign="top">
    <div class="cbox">
      <div class="cbox-title">
      {tr}Add objects to category{/tr}
      </div>
      <div class="cbox-data">
      <table class="findtable">
      <tr><td class="findtable">{tr}Find{/tr}</td>
      <td>
        <form method="get" action="tiki-admin_categories.php">
        <input type="text" name="find_objects" />
        <input type="hidden" name="parentId" value="{$parentId}" />
        <input type="submit" value="{tr}filter{/tr}" name="search_objects" />
        <input type="hidden" name="sort_mode" value="{$sort_mode}" />
        <input type="hidden" name="offset" value="{$offset}" />
        <input type="hidden" name="find" value="{$find}" />
        </form>
      </td>
      </tr>
      </table>
      <form action="tiki-admin_categories.php" method="post">
      <input type="hidden" name="parentId" value="{$parentId}" />
      <table>
        <tr>
          <td class="form">{tr}page{/tr}:</td>
          <td class="form"><select name="pageName">{section name=ix loop=$pages}<option value="{$pages[ix].pageName}">{$pages[ix].pageName|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpage" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}article{/tr}:</td>
          <td class="form"><select name="articleId">{section name=ix loop=$articles}<option value="{$articles[ix].articleId}">{$articles[ix].title|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addarticle" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}blog{/tr}:</td>
          <td class="form"><select name="blogId">{section name=ix loop=$blogs}<option value="{$blogs[ix].blogId}">{$blogs[ix].title|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addblog" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}image gal{/tr}:</td>
          <td class="form"><select name="galleryId">{section name=ix loop=$galleries}<option value="{$galleries[ix].galleryId}">{$galleries[ix].name|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addgallery" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}file gal{/tr}:</td>
          <td class="form"><select name="file_galleryId">{section name=ix loop=$file_galleries}<option value="{$file_galleries[ix].galleryId}">{$file_galleries[ix].name|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfilegallery" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}forum{/tr}:</td>
          <td class="form"><select name="forumId">{section name=ix loop=$forums}<option value="{$forums[ix].forumId}">{$forums[ix].name|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addforum" value="{tr}add{/tr}" /></td>
        </tr>
        <tr>
          <td class="form">{tr}poll{/tr}:</td>
          <td class="form"><select name="pollId">{section name=ix loop=$polls}<option value="{$polls[ix].pollId}">{$polls[ix].title|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addpoll" value="{tr}add{/tr}" /></td>
        </tr>        
        <tr>
          <td class="form">{tr}faq{/tr}:</td>
          <td class="form"><select name="faqId">{section name=ix loop=$faqs}<option value="{$faqs[ix].faqId}">{$faqs[ix].title|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addfaq" value="{tr}add{/tr}" /></td>
        </tr>        
        <tr>
          <td class="form">{tr}quiz{/tr}:</td>
          <td class="form"><select name="quizId">{section name=ix loop=$quizzes}<option value="{$quizzes[ix].quizId}">{$quizzes[ix].name|truncate:20:"(...)":true}</option>{/section}</select></td>
          <td class="form"><input type="submit" name="addquiz" value="{tr}add{/tr}" /></td>
        </tr>        


      </table>
      </form>
      </div>
    </div>
  </td>
<tr>
</table>
