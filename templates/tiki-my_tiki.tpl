{* $Id$ *}

<h1>
  <a class="pagetitle" href="tiki-my_tiki.php">{tr}My Tiki{/tr}</a>

  {if $prefs.feature_help eq 'y'}
    <a href="{$prefs.helpurl}MyTiki" target="tikihelp" class="tikihelp" title="{tr}My Tiki{/tr}">
      {icon _id='help'}
    </a>
  {/if}

  {if $prefs.feature_view_tpl eq 'y'}
    <a href="tiki-edit_templates.php?template=tiki-my_tiki.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}my tiki tpl{/tr}">
      {icon _id='shape_square_edit' alt='{tr}Edit Template{/tr}'}
    </a>
  {/if}
</h1>

{if $prefs.feature_mootools eq 'y' and $prefs.feature_ajax eq 'y'}
  {include file=tiki-mytiki_mootabs.tpl}
{else}
  {include file=tiki-mytiki_bar.tpl}
  <br />

  {capture name=my}
  {if $prefs.feature_wiki eq 'y' and $mytiki_pages eq 'y'}
    <div id="content1" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}
            {tr}My pages{/tr}{else}{tr}User Pages{/tr}
          {/if}
        </div>
        <div class="cbox-data">
          
          <table class="normal">
            <tr>
              <th class="heading">
                <a class="tableheading" href="tiki-my_tiki.php?sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a>
              </th>
              <th class="heading">{tr}Creator{/tr}</th>
              <th class="heading">{tr}Last editor{/tr}</th>
              <th class="heading">
                <a class="tableheading" href="tiki-my_tiki.php?sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}Last modification{/tr}</a>
              </th>
              <th class="heading" width="50px">{tr}Actions{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_pages}
              <tr>
                <td class="{cycle advance=false}">
                  <a class="link" title="{tr}View{/tr}: {$user_pages[ix].pageName}" href="tiki-index.php?page={$user_pages[ix].pageName|escape:"url"}">{$user_pages[ix].pageName|truncate:40:"(...)"}</a>
                </td>
                <td class="{cycle advance=false}" style="text-align:center;">
                  {if $userwatch eq $user_pages[ix].creator}{tr}y{/tr}{else}&nbsp;{/if}
                </td>
                <td class="{cycle advance=false}" style="text-align:center;">
                  {if $userwatch eq $user_pages[ix].lastEditor}{tr}y{/tr}{else}&nbsp;{/if}
                </td>
                <td class="{cycle advance=false}">
                  {$user_pages[ix].date|tiki_short_datetime}
                </td>
                <td class="{cycle}" style="text-align:center;" width="50px">
                  <a class="link" href="tiki-editpage.php?page={$user_pages[ix].pageName|escape:"url"}">
                    {icon _id='page_edit' title="{tr}Edit{/tr}: `$user_pages[ix].pageName`"}
                  </a>
                </td>
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_galleries eq 'y' and $mytiki_gals eq 'y'}
    <div id="content2" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My galleries{/tr}{else}{tr}User Galleries{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th class="heading">{tr}Gallery{/tr}</th>
              <th class="heading" width="50px">{tr}Actions{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_galleries}
              <tr>
                <td class="{cycle advance=false}">
                  <a class="link" href="tiki-browse_gallery.php?galleryId={$user_galleries[ix].galleryId}">{$user_galleries[ix].name}</a>
                </td>
                <td class="{cycle}" style="text-align:center;" width="50px">
                  <a class="link" href="tiki-galleries.php?editgal={$user_galleries[ix].galleryId}">
                    {icon _id='page_edit'}
                  </a>
                </td>
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_trackers eq 'y' and $mytiki_items eq 'y'}
    <div id="content3" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My items{/tr}{else}{tr}Assigned items{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th class="heading">{tr}Item{/tr}</th>
              <th class="heading">{tr}Tracker{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_items}
              <tr>
                <td class="{cycle advance=false}">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_tracker_item.php?trackerId={$user_items[ix].trackerId}&amp;itemId={$user_items[ix].itemId}">{$user_items[ix].value}</a>
                </td>
                <td class="{cycle}">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_tracker.php?trackerId={$user_items[ix].trackerId}">{$user_items[ix].name}</a>
                </td>
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_messages eq 'y' and $mytiki_msgs eq 'y'}
    <div id="content4" class="content">
      <div class="cbox">
        <div class="cbox-title">{tr}Unread Messages{/tr}</div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th class="heading">{tr}Subject{/tr}</th>
              <th class="heading">{tr}From{/tr}</th>
              <th class="heading">{tr}Date{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$msgs}
              <tr>
                <td class="{cycle advance=false}">
                  <a class="link" title="{tr}View{/tr}" href="messu-read.php?offset=0&amp;flag=&amp;flagval=&amp;find=&amp;sort_mode=date_desc&amp;priority=&amp;msgId={$msgs[ix].msgId}">{$msgs[ix].subject}</a>
                </td>
                <td class="{cycle advance=false}">
                  {$msgs[ix].user_from}
                </td>
                <td class="{cycle}">
                  {$msgs[ix].date|tiki_short_datetime}
                </td>
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_tasks eq 'y' and $mytiki_tasks eq 'y'}
    <div id="content5" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My tasks{/tr}{else}{tr}User tasks{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th class="heading">{tr}Tasks{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$tasks}
              <tr>
                <td class="{cycle}">
                  <a class="link" href="tiki-user_tasks.php?taskId={$tasks[ix].taskId}">{$tasks[ix].title}</a>
                </td>
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_forums eq 'y' && $mytiki_forum_topics eq 'y'}
    <div id="content8" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My forum topics{/tr}{else}{tr}User forum topics{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th class="heading">{tr}Forum topics{/tr}</th>              
              <th class="heading">{tr}Date of post{/tr}</th>              
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_forum_topics}
              <tr>
                <td class="{cycle advance=false}">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_forum_thread.php?comments_parentId={$user_forum_topics[ix].threadId}&amp;forumId={$user_forum_topics[ix].object}">{$user_forum_topics[ix].title}</a>
                </td>                
                <td class="{cycle}">
                  {$user_forum_topics[ix].commentDate|tiki_short_datetime}
                </td>  
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}
  
    {if $prefs.feature_forums eq 'y' && $mytiki_forum_replies eq 'y'}
    <div id="content9" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My forum replies{/tr}{else}{tr}User forum replies{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th class="heading">{tr}Forum replies{/tr}</th>              
              <th class="heading">{tr}Date of post{/tr}</th>              
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_forum_replies}
              <tr>
                <td class="{cycle advance=false}">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_forum_thread.php?comments_parentId={$user_forum_replies[ix].threadId}&amp;forumId={$user_forum_replies[ix].object}">{$user_forum_replies[ix].title}</a>
                </td>
                <td class="{cycle}">
                  {$user_forum_replies[ix].commentDate|tiki_short_datetime}
                </td>                  
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}
  
  {if $prefs.feature_blogs eq 'y' && $mytiki_blogs eq 'y'}
    <div id="content6" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My blogs{/tr}{else}{tr}User Blogs{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th class="heading">{tr}Blogs{/tr}</th>
              <th class="heading" width="50px">{tr}Actions{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_blogs}
              <tr>
                <td class="{cycle advance=false}">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_blog.php?blogId={$user_blogs[ix].blogId}">{$user_blogs[ix].title}</a>
                </td>
                <td class="{cycle}" style="text-align:center;" width="50px">
                  <a class="link" href="tiki-edit_blog.php?blogId={$user_blogs[ix].blogId}">
                    {icon _id='page_edit'}
                  </a>
                </td>
              </tr>
            {/section}
          </table>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_workflow eq 'y' && $tiki_p_use_workflow eq 'y' && $mytiki_workflow eq 'y'}
    <div id="content7" class="content">
      {include file="tiki-g-my_activities.tpl"}
      <br /><br />
      {include file="tiki-g-my_instances.tpl"}
    </div>
  {/if}
  {/capture}

  {$smarty.capture.my}
    {if $smarty.capture.my|strip:'' eq ''}
      {tr}To display the objects you participate:{/tr} <a href="tiki-user_preferences.php?tab3#MyTiki">{tr}My Tiki{/tr}</a>
    {/if}
{/if}
