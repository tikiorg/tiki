{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/tiki-my_tiki.tpl,v 1.2 2004-01-16 19:34:01 musus Exp $ *}

<a class="pagetitle" href="tiki-my_tiki.php">{tr}My Tiki{/tr}</a>


{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=MyTikiDoc" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}MyTikiDoc{/tr}">
<img border='0' src='img/icons/help.gif' alt='{tr}help{/tr}' /></a>
{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-my_tiki.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}my tiki tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='{tr}edit template{/tr}' /></a>
{/if}



{include file=tiki-mytiki_bar.tpl}
<br /><br />



<div class="tabs">
	{if $mytiki_pages eq 'y'}
		<span id="tab1" class="tab tabActive">{tr}My Pages{/tr}</span>
	{/if}
	{if $mytiki_gals eq 'y'}
		<span id="tab2" class="tab">{tr}My galleries{/tr}</span>
	{/if}
	{if $mytiki_items eq 'y'}
		<span id="tab3" class="tab">{tr}My items{/tr}</span>
	{/if}
	{if $mytiki_msgs eq 'y'}
		<span id="tab4" class="tab">{tr}My messages{/tr}</span>
	{/if}
	{if $mytiki_tasks eq 'y'}
		<span id="tab5" class="tab">{tr}My tasks{/tr}</span>
	{/if}
	{if $mytiki_blogs eq 'y'}
		<span id="tab6" class="tab">{tr}My blogs{/tr}</span>
	{/if}
</div>

{if $mytiki_pages eq 'y'}
<div id="content1" class="content">
  <div class="tiki">
  <div class="tiki-title">
    <table width=100%>
    <tr>
      <td width=70%>{tr}User Pages{/tr}</td>
      <td><div class="button2"><a href="tiki-my_tiki.php?by=creator" title="{tr}List pages where I am a creator{/tr}">{tr}by creator{/tr}</a></div></td>
      <td><div class="button2"><a href="tiki-my_tiki.php?by=modificator" title="{tr}List pages where I am a modificator{/tr}">{tr}by modificator{/tr}</a></div></td>
    </tr>
    </table>
  </div>
  <div class="tiki-content">
  <table >
  {section name=ix loop=$user_pages}
  <tr><td>
  <a class="link" title="{$user_pages[ix].pageName}" href="tiki-index.php?page={$user_pages[ix].pageName|escape:"url"}">{$user_pages[ix].pageName|truncate:30:"(...)"}</a>
  </td><td align="right">
  (<a class="link" href="tiki-editpage.php?page={$user_pages[ix].pageName|escape:"url"}">{tr}edit{/tr}</a>)
  </td></tr>
  {/section}
  </table>
  </div>
  </div>
</div>
{/if}

{if $mytiki_gals eq 'y'}
<div id="content2" class="content">
  <div class="tiki">
  <div class="tiki-title">{tr}User Galleries{/tr}</div>
  <div class="tiki-content">
  <table >
  {section name=ix loop=$user_galleries}
  <tr><td>
  <a class="link" href="tiki-browse_gallery.php?galleryId={$user_galleries[ix].galleryId}">{$user_galleries[ix].name}</a>
  </td><td align="right">
  <a class="link" href="tiki-galleries.php?editgal={$user_galleries[ix].galleryId}">({tr}edit{/tr})</a>
  </td></tr>
  {/section}
  </table>
  </div>
  </div>
</div>
{/if}

{if $mytiki_items eq 'y'}
<div id="content3" class="content">
  <div class="tiki">
  <div class="tiki-title">{tr}Assigned items{/tr}</div>
  <div class="tiki-content">
  <table >
  {section name=ix loop=$user_items}
  <tr><td>
  <b>{$user_items[ix].value}</b> {tr}at tracker{/tr} {$user_items[ix].name}  
  </td><td align="right">
  <a class="link" href="tiki-view_tracker_item.php?trackerId={$user_items[ix].trackerId}&amp;itemId={$user_items[ix].itemId}">({tr}edit{/tr})</a>
  </td>
  </tr>
  {/section}
  </table>
  </div>
  </div>
</div>
{/if}

{if $mytiki_msgs eq 'y'}
<div id="content4" class="content">
  <div class="tiki">
  <div class="tiki-title">{tr}Unread Messages{/tr}</div>
  <table >
  {section name=ix loop=$msgs}
  <tr><td>
  <a class="link" href="messu-read.php?offset=0&amp;flag=&amp;flagval=&amp;find=&amp;sort_mode=date_desc&amp;priority=&amp;msgId={$msgs[ix].msgId}">{$msgs[ix].subject}</a>
  </td></tr>
  {/section}
  </table>
  </div>
</div>
{/if}

{if $mytiki_tasks eq 'y'}
<div id="content5" class="content">
  <div class="tiki">
  <div class="tiki-title">{tr}Tasks{/tr}</div>
  <table >
  {section name=ix loop=$tasks}
  <tr><td>
  <a class="link" href="tiki-user_tasks.php?taskId={$tasks[ix].taskId}">{$tasks[ix].title}</a>
  </td></tr>
  {/section}
  </table>
  </div>
</div>
{/if}



{if $mytiki_blogs eq 'y'}
<div id="content6" class="content">
  <div class="tiki">
  <div class="tiki-title">{tr}User Blogs{/tr}</div>
  <table >
  {section name=ix loop=$user_blogs}
  <tr><td>
  <a class="link" href="tiki-view_blog.php?blogId={$user_blogs[ix].blogId}">{$user_blogs[ix].title}</a>
  </td><td align="right">
  (<a class="link" href="tiki-edit_blog.php?blogId={$user_blogs[ix].blogId}">{tr}edit{/tr}</a>)
  </td></tr>
  {/section}
  </table>
  </div>
</div>
{/if}

