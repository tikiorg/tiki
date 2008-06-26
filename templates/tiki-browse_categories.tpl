{* $Id$ *}

<h1><a class="pagetitle" href="tiki-browse_categories.php">{if $parentId ne 0}{tr}Category{/tr} {$p_info.name}{else}{tr}Categories{/tr}{/if}</a></h1>
{if $parentId and $p_info.description}<div class="description">{$p_info.description}</div>{/if}
{if $tiki_p_admin_categories eq 'y'}
<div class="navbar"><a class="linkbut" href="tiki-admin_categories.php?parentId={$parentId}" title="{tr}Admin the Category System{/tr}">{tr}Admin Category{/tr}</a></div>
{/if}
{if $user and $prefs.feature_user_watches eq 'y'}
	{if $user_watching_category eq 'n'}
		<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=add">{icon _id='eye' alt='{tr}Watch Only This Category{/tr}'}</a>
		<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=add_desc">{icon _id='eye_arrow_down' alt='{tr}Watch This Category and Their Descendants{/tr}'}</a>
	{else}
		<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=remove">{icon _id='no_eye' alt='{tr}Stop Watching Only This Category{/tr}'}</a>
		<a href="tiki-browse_categories.php?parentId={$parentId|escape:"url"}&amp;watch_event=category_changed&amp;watch_object={$parentId|escape:"url"}&amp;deep={$deep}&amp;watch_action=remove_desc">{icon _id='no_eye_arrow_down' alt='{tr}Stop Watching This Category and Their Descendants{/tr}'}</a>
	{/if}
{/if}  
<br /><br />
{tr}Browse in{/tr}:<br />

<a class="linkbut" {if $type eq ''} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}All{/tr}</a>

{if $prefs.feature_wiki eq 'y'}
  <a class="linkbut" {if $type eq 'wiki page'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=wiki+page&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Wiki pages{/tr}</a>
{/if}

{if $prefs.feature_galleries eq 'y'}
  <a class="linkbut" {if $type eq 'image gallery'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Image galleries{/tr}</a>
  <a class="linkbut" {if $type eq 'image'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=image&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Images{/tr}</a>
{/if}

{if $prefs.feature_file_galleries eq 'y'}
  <a class="linkbut" {if $type eq 'file gallery'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=file+gallery&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}File galleries{/tr}</a>
  
  <a class="linkbut" {if $type eq 'file'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=file&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Files{/tr}</a>
{/if}

{if $prefs.feature_blogs eq 'y'}
  <a class="linkbut" {if $type eq 'blog'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=blog&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Blogs{/tr}</a>
{/if}

{if $prefs.feature_trackers eq 'y'}
  <a class="linkbut" {if $type eq 'tracker'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=tracker&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Trackers{/tr}</a>
  <a class="linkbut" {if $type eq 'trackerItem'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=trackerItem&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Trackers Items{/tr}</a>
{/if}

{if $prefs.feature_quizzes eq 'y'}
<a class="linkbut" {if $type eq 'quiz'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=quiz&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Quizzes{/tr}</a>
{/if}

{if $prefs.feature_polls eq 'y'}
<a class="linkbut" {if $type eq 'poll'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=poll&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Polls{/tr}</a>
{/if}

{if $prefs.feature_surveys eq 'y'}
<a class="linkbut" {if $type eq 'survey'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=survey&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Surveys{/tr}</a>
{/if}

{if $prefs.feature_directory eq 'y'}
<a class="linkbut" {if $type eq 'directory'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=directory&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Directory{/tr}</a>
{/if}

{if $prefs.feature_faqs eq 'y'}
<a class="linkbut" {if $type eq 'faq'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=faq&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}FAQs{/tr}</a>
{/if}

{if $prefs.feature_sheet eq 'y'}
<a class="linkbut" {if $type eq 'sheet'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=sheet&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Sheets{/tr}</a>
{/if}

{if $prefs.feature_articles eq 'y'}
<a class="linkbut" {if $type eq 'article'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=article&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Articles{/tr}</a>
{/if}

{if $prefs.feature_forums eq 'y'}
<a class="linkbut" {if $type eq 'forum'} id="highlight"{/if} href="tiki-browse_categories.php?find={$find|escape}&amp;deep={$deep}&amp;type=forum&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Forums{/tr}</a>
{/if}


<br /><br />
<form method="post" action="tiki-browse_categories.php">
  {tr}Find:{/tr} {$p_info.name} <input type="text" name="find" value="{$find|escape}" size="35" />
  {tr}in the current category - and its subcategories: {/tr}<input type="checkbox" name="deep" {if $deep eq 'on'}checked="checked"{/if}/>
                            <input type="hidden" name="parentId" value="{$parentId|escape}" />
                            <input type="hidden" name="type" value="{$type|escape}" />
                            <input type="submit" value="{tr}Find{/tr}" name="search" />
                            <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</form>
<br />
{if $deep eq 'on'}
<a class="link" href="tiki-browse_categories.php?find={$find|escape}&amp;type={$type|escape}&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Hide subcategories objects{/tr}</a>
{else}
<a class="link" href="tiki-browse_categories.php?find={$find|escape}&amp;type={$type|escape}&amp;deep=on&amp;parentId={$parentId}&amp;sort_mode={$sort_mode}">{tr}Show subcategories objects{/tr}</a>
{/if}
<br /><br />
{if $path}
<div class="treetitle">{tr}Current category{/tr}:
<a href="tiki-browse_categories.php?parentId=0&amp;deep={$deep}&amp;type={$type|escape}" class="categpath">{tr}Top{/tr}</a>
{section name=x loop=$path}
&nbsp;::&nbsp;
<a class="categpath" href="tiki-browse_categories.php?parentId={$path[x].categId}&amp;deep={$deep}&amp;type={$type|escape}">{$path[x].name|tr_if}</a>
{/section}
</div>

{if $parentId ne '0'}
<div class="treenode">
<a class="catname" href="tiki-browse_categories.php?parentId={$father}&amp;deep={$deep}&amp;type={$type}" title="{tr}Upper level{/tr}">..</a>
</div>
{/if}
{elseif $paths}
{section name=x loop=$paths}
{section name=y loop=$paths[x]}
&nbsp;::&nbsp;
<a class="categpath" href="tiki-browse_categories.php?parentId={$paths[x][y].categId}&amp;deep={$deep}&amp;type={$type|escape}">{$paths[x][y].name|tr_if}</a>
{/section}
<br />
{/section}
{/if}

<table class="admin">
  <tr>
    <td>
      {$tree}
    </td>
    
    <td width="20">
      &nbsp;
    </td>

    <td>
      {if $cantobjects > 0}
        <table class="normal">
          <tr class="heading">
            <th class="heading">
              {tr}Name{/tr}
            </th>
            <th class="heading">
              {tr}Type{/tr}
            </th>
            {if $deep eq 'on'}
              <th class="heading">
                {tr}Category{/tr}
              </th>
            {/if}
          </tr>
  
          {cycle values="odd,even" print=false}
          {section name=ix loop=$objects}
          <tr class="{cycle}" >

            <td>
              <a href="{$objects[ix].href}" class="catname">{$objects[ix].name|default:'&nbsp;'}</a>
              <div class="subcomment">  {$objects[ix].description} </div>
            </td>
            
            <td>
              <strong>{tr}{$objects[ix].type|replace:"wiki page":"Wiki"|replace:"article":"Article"|regex_replace:"/tracker [0-9]*/":"tracker item"}{/tr}</strong>
            </td>
            {if $deep eq 'on'}
              <td>
                {$objects[ix].categName|tr_if}
              </td>
            {/if}

          </tr>
		  {sectionelse}
		  <tr>
			<td colspan="2" class="odd">{tr}No records found{/tr}</td>
		  </tr>
          {/section}
        </table>
        <br />
      {/if}

    </td>
  </tr>
</table>


{if $cantobjects > 0}
  <div class="mini">
    {if $prev_offset >= 0}
      [<a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;type={$type}&amp;parentId={$parentId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
    {/if}
    {tr}Page{/tr}: {$actual_page}/{$cant_pages}
    {if $next_offset >= 0}
      &nbsp;[<a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;type={$type}&amp;parentId={$parentId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
    {/if}
    {if $prefs.direct_pagination eq 'y'}
      <br />
      {section loop=$cant_pages name=foo}
        {assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
        <a class="prevnext" href="tiki-browse_categories.php?find={$find}&amp;deep={$deep}&amp;type={$type}&amp;parentId={$parentId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
          {$smarty.section.foo.index_next}
        </a>&nbsp;
      {/section}
    {/if}
 </div>
{/if}
