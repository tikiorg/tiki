{* $Id$ *}

{title help="MyTiki"}{tr}My Tiki{/tr}{/title}

  {include file='tiki-mytiki_bar.tpl'}
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
              <th>
                <a href="tiki-my_tiki.php?sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a>
              </th>
              <th>{tr}Creator{/tr}</th>
              <th>{tr}Last editor{/tr}</th>
              <th>
                <a href="tiki-my_tiki.php?sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}Last modification{/tr}</a>
              </th>
              <th style="width:50px">{tr}Actions{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_pages}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" title="{tr}View:{/tr} {$user_pages[ix].pageName}" href="tiki-index.php?page={$user_pages[ix].pageName|escape:"url"}">{$user_pages[ix].pageName|truncate:40:"(...)"}</a>
                </td>
                <td class="username">
                  {if $userwatch eq $user_pages[ix].creator}{tr}y{/tr}{else}&nbsp;{/if}
                </td>
                <td class="username">
                  {if $userwatch eq $user_pages[ix].lastEditor}{tr}y{/tr}{else}&nbsp;{/if}
                </td>
                <td class="date">
                  {$user_pages[ix].date|tiki_short_datetime}
                </td>
                <td class="action">
                  <a class="link" href="tiki-editpage.php?page={$user_pages[ix].pageName|escape:"url"}">
                    {icon _id='page_edit' title="{tr}Edit:{/tr} `$user_pages[ix].pageName`"}
                  </a>
                </td>
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$user_pages|@count}</div>
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
              <th>{tr}Gallery{/tr}</th>
              <th style="width:50px">{tr}Actions{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_galleries}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" href="{$user_galleries[ix].galleryId|sefurl:gallery}">{$user_galleries[ix].name}</a>
                </td>
                <td class="action">
                  <a class="link" href="tiki-galleries.php?editgal={$user_galleries[ix].galleryId}">
                    {icon _id='page_edit'}
                  </a>
                </td>
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$user_galleries|@count}</div>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_articles eq 'y' and $mytiki_articles eq 'y'}
    <div id="content2" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My Articles{/tr}{else}{tr}User Articles{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th>{tr}Article{/tr}</th>
              <th style="width:50px">{tr}Actions{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_articles}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" href="{$user_articles[ix].articleId|sefurl:article}">{$user_articles[ix].title}</a>
                </td>
                <td class="action">
                  <a class="link" href="tiki-edit_article.php?articleId={$user_articles[ix].articleId}">
                    {icon _id='page_edit'}
                  </a>
                </td>
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$user_articles|@count}</div>
        </div>
      </div>
    </div>
  {/if}

  {if $prefs.feature_trackers eq 'y' and $mytiki_user_items eq 'y'}
    <div id="content3" class="content">
      <div class="cbox">
        <div class="cbox-title">
          {if $userwatch eq $user}{tr}My User Items{/tr}{else}{tr}User Items{/tr}{/if}
        </div>
        <div class="cbox-data">
          <table class="normal">
            <tr>
              <th>{tr}Item{/tr}</th>
              <th>{tr}Tracker{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_items}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_tracker_item.php?trackerId={$user_items[ix].trackerId}&amp;itemId={$user_items[ix].itemId}">{$user_items[ix].value}</a>
                </td>
                <td class="text">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_tracker.php?trackerId={$user_items[ix].trackerId}">{$user_items[ix].name}</a>
                </td>
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">
		  	   {tr}Records:{/tr} {$user_items|@count}
			   {if !empty($nb_item_comments)}<br />{tr}Comments:{/tr} {$nb_item_comments}{/if}
		  </div>
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
              <th>{tr}Subject{/tr}</th>
              <th>{tr}From{/tr}</th>
              <th>{tr}Date{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$msgs}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" title="{tr}View{/tr}" href="messu-read.php?offset=0&amp;flag=&amp;flagval=&amp;find=&amp;sort_mode=date_desc&amp;priority=&amp;msgId={$msgs[ix].msgId}">{$msgs[ix].subject}</a>
                </td>
                <td class="text">
                  {$msgs[ix].user_from}
                </td>
                <td class="date">
                  {$msgs[ix].date|tiki_short_datetime}
                </td>
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$msgs|@count}</div>
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
              <th>{tr}Tasks{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$tasks}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" href="tiki-user_tasks.php?taskId={$tasks[ix].taskId}">{$tasks[ix].title}</a>
                </td>
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$tasks|@count}</div>
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
              <th>{tr}Forum topics{/tr}</th>              
              <th>{tr}Date of post{/tr}</th>              
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_forum_topics}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_forum_thread.php?comments_parentId={$user_forum_topics[ix].threadId}&amp;forumId={$user_forum_topics[ix].object}">{$user_forum_topics[ix].title}</a>
                </td>                
                <td class="date">
                  {$user_forum_topics[ix].commentDate|tiki_short_datetime}
                </td>  
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$user_forum_topics|@count}</div>
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
              <th>{tr}Forum replies{/tr}</th>              
              <th>{tr}Date of post{/tr}</th>              
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_forum_replies}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" title="{tr}View{/tr}" href="tiki-view_forum_thread.php?comments_parentId={$user_forum_replies[ix].threadId}&amp;forumId={$user_forum_replies[ix].object}">{$user_forum_replies[ix].title}</a>
                </td>
                <td class="date">
                  {$user_forum_replies[ix].commentDate|tiki_short_datetime}
                </td>                  
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$user_forum_replies|@count}</div>
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
              <th>{tr}Blogs{/tr}</th>
              <th style="width:50px">{tr}Actions{/tr}</th>
            </tr>
            {cycle values="even,odd" print=false}
            {section name=ix loop=$user_blogs}
              <tr class="{cycle}">
                <td class="text">
                  <a class="link" title="{tr}View{/tr}" href="{$user_blogs[ix].blogId|sefurl:blog}">{$user_blogs[ix].title}</a>
                </td>
                <td class="action">
                  <a class="link" href="tiki-edit_blog.php?blogId={$user_blogs[ix].blogId}">
                    {icon _id='page_edit'}
                  </a>
                </td>
              </tr>
            {/section}
          </table>
		  <div style="text-align:right;">{tr}Records:{/tr} {$user_blogs|@count}</div>
        </div>
      </div>
    </div>
  {/if}

  {/capture}

  {$smarty.capture.my}
    {if $smarty.capture.my|strip:'' eq ''}
      {tr}To display the objects you created or contributed to:{/tr} <a href="tiki-user_preferences.php?tab3#MyTiki">{tr}My Tiki{/tr}</a>
    {/if}
