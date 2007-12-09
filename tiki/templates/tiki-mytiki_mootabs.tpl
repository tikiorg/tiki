<div id="container" >
	<div id="tab-block-1" >
		{if $prefs.feature_userPreferences eq 'y'}
		<h5><img  border="0" src="img/mytiki/mytiki.gif" alt="{tr}MyTiki{/tr}" />{tr}MyTiki{/tr}</h5>
		<div>
		</div>
		{/if}
		{if $prefs.feature_userPreferences eq 'y'}
		<h5><a {ajax_href template=tiki-user_preferences.tpl
		htmlelement=user_preferences}tiki-user_preferences.php{/ajax_href}><img  border="0" src="img/mytiki/prefs.gif" alt="{tr}Prefs{/tr}" />{tr}Preferences{/tr}</a></h5>
		<div id="user_preferences">
		</div>
		{/if}
		
		<h5><a {ajax_href template=tiki-user_information.tpl
		htmlelement=user_information}tiki-user_information.php{/ajax_href}>{tr}My Infos{/tr}</a></h5>
		<div id="user_information">
		</div>
		{if $prefs.feature_ajax eq "y" && $prefs.feature_mootools eq "y"}
		<h5><a {ajax_href template=tiki-mypages.tpl
		htmlelement=user_pages}tiki-mypages.php{/ajax_href}>{tr}My Pages{/tr}</a></h5>
		<div id="user_pages">
		</div>
		{/if}

		{if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
		<h5><a {ajax_href template=messu-mailbox.tpl
		htmlelement=user_messages}messu-mailbox.php{/ajax_href}><img  border="0" src="img/mytiki/messages.gif" alt="{tr}Messages{/tr}" />{tr}Messages{/tr}<small>({$unread})</small></a></h5>
		<div id="user_messages">
		</div>
		{/if}

		{if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
		<h5><a {ajax_href template=tiki-user_tasks.tpl
		htmlelement=user_tasks}tiki-user_tasks.php{/ajax_href}><img  border="0" src="img/mytiki/tasks.gif" alt="{tr}Tasks{/tr}" />{tr}Tasks{/tr}</a></h5>
		<div id="user_tasks">
		</div>
		{/if}

		{if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
		<h5><a {ajax_href template=tiki-user_bookmarks.tpl
		htmlelement=user_bookmarks}tiki-user_bookmarks.php{/ajax_href}><img  border="0" src="img/mytiki/bookmarks.gif" alt="{tr}Bookmarks{/tr}" />{tr}Bookmarks{/tr}</a></h5>
		<div id="user_bookmarks">
		</div>
		{/if}

		{if $prefs.user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
		<h5><a {ajax_href template=tiki-user_assigned_modules.tpl
		htmlelement=user_modules}tiki-user_assigned_modules.php{/ajax_href}><img  border="0" src="img/mytiki/modules.gif" alt="{tr}Modules{/tr}" />{tr}Modules{/tr}</a></h5>
		<div id="user_modules">
		</div>
		{/if}

		{if $prefs.feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
		<h5><a {ajax_href template=tiki-newsreader_servers.tpl
		htmlelement=user_newsreaders}tiki-newsreader_servers.php{/ajax_href}><img  border="0" src="img/mytiki/news.gif" alt="{tr}Newsreader{/tr}" />{tr}Newsreader{/tr}</a></h5>
		<div id="user_newsreaders">
		</div>
		{/if}

		{if $prefs.feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
		<h5><a {ajax_href template=tiki-webmail.tpl
		htmlelement=user_webmails}tiki-webmail.php{/ajax_href}><img  border="0" src="img/mytiki/webmail.gif" alt="{tr}Webmail{/tr}" />{tr}Webmail{/tr}</a></h5>
		<div id="user_webmails">
		</div>
		{/if}

		{if $prefs.feature_contacts eq 'y'}
		<h5><a {ajax_href template=tiki-contacts.tpl
		htmlelement=user_contacts}tiki-contacts.php{/ajax_href}><img  border="0" src="img/mytiki/stock_contact.png" alt="{tr}My Contacts{/tr}" />{tr}My Contacts{/tr}</a></h5>
		<div id="user_contacts">
		</div>
		{/if}
		
		{*if $prefs.feature_newsletters eq 'y'}
		<h5><a {ajax_href template=tiki-user_newsletters.tpl
		htmlelement=user_newsletters}tiki-user_newsletters.php{/ajax_href}><img  border="0" src="img/mytiki/stock_contact.png" alt="{tr}My Newsletters{/tr}" />{tr}My Newsletters{/tr}</a></h5>
		<div id="user_newsletters">
		</div>
		{/if*}
		
		{if $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		<h5><a {ajax_href template=tiki-notepad_list.tpl
		htmlelement=user_notepads}tiki-notepad_list.php{/ajax_href}><img border="0" src="img/mytiki/notes.gif" alt="{tr}Notepad{/tr}" />{tr}Notepad{/tr}</a></h5>
		<div id="user_notepads">
		</div>
		{/if}

		{if $prefs.feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
		<h5><a {ajax_href template=tiki-userfiles.tpl
		htmlelement=user_files}tiki-userfiles.php{/ajax_href}><img  border="0" src="img/mytiki/myfiles.gif" alt="{tr}MyFiles{/tr}" />{tr}MyFiles{/tr}</a></h5>
		<div id="user_files">
		</div>
		{/if}

		{if $prefs.feature_minical eq 'y' and $tiki_p_minical eq 'y'}
		<h5><a {ajax_href template=tiki-minical.tpl
		htmlelement=user_minical}tiki-minical.php{/ajax_href}><img  border="0" src="img/mytiki/minical.gif" alt="{tr}Mini Calendar{/tr}" />{tr}Mini Calendar{/tr}</a></h5>
		<div id="user_minical">
		</div>
		{/if}

		{if $prefs.feature_user_watches eq 'y'}
		<h5><a {ajax_href template=tiki-user_watches.tpl
		htmlelement=user_watches}tiki-user_watches.php{/ajax_href}><img  border="0" src="img/mytiki/mywatches.gif" alt="{tr}My watches{/tr}" />{tr}My watches{/tr}</a></h5>
		<div id="user_watches">
		</div>
		{/if}

		{if $prefs.feature_actionlog == 'y' and !empty($user) and $tiki_p_view_actionlog eq 'y'}
		<h5><a {ajax_href template=tiki-admin_actionlog.tpl
		htmlelement=user_logs}tiki-admin_actionlog.php?selectedUsers[]=$user{/ajax_href}"><img  border="0" src="img/mytiki/gnome-vumeter.png" alt="{tr}Action Log{/tr}" width="32" height="32" />{tr}Action Log{/tr}</a></h5>
		<div id="user_logs">
		</div>
		{/if}

		{if $prefs.feature_wiki eq 'y' and $mytiki_pages eq 'y'}
		<h5>{tr}My Wiki Pages{/tr}</h5>
		<div class="content">
  			<div class="cbox">
  				<div class="cbox-title">{if $userwatch eq $user}{tr}My pages{/tr}{else}{tr}User Pages{/tr}{/if}</div>
  			<div class="cbox-data">
  				<table class="normal">
  				<tr>
  					<th class="heading"><a class="tableheading" href="tiki-my_tiki.php?sort_mode={if $sort_mode eq 'pageName_desc'}pageName_asc{else}pageName_desc{/if}">{tr}Page{/tr}</a></th>
  					<th class="heading">{tr}Creator{/tr}</th>
  					<th class="heading">{tr}Last editor{/tr}</th>
  					<th class="heading"><a class="tableheading" href="tiki-my_tiki.php?sort_mode={if $sort_mode eq 'date_desc'}date_asc{else}date_desc{/if}">{tr}Last modification{/tr}</a></th><th class="heading">{tr}Actions{/tr}</th></tr>
  					{cycle values="even,odd" print=false}
  					{section name=ix loop=$user_pages}
  						<tr>
  							<td class="{cycle advance=false}"><a class="link" title="{tr}View{/tr}: {$user_pages[ix].pageName}" href="tiki-index.php?page={$user_pages[ix].pageName|escape:"url"}">{$user_pages[ix].pageName|truncate:40:"(...)"}</a></td>
  							<td class="{cycle advance=false}" style="text-align:center;">{if $userwatch eq $user_pages[ix].creator}{tr}y{/tr}{else}&nbsp;{/if}</td>
  							<td class="{cycle advance=false}" style="text-align:center;">{if $userwatch eq $user_pages[ix].lastEditor}{tr}y{/tr}{else}&nbsp;{/if}</td>
  							<td class="{cycle advance=false}">{$user_pages[ix].date|tiki_short_datetime}</td>
  							<td class="{cycle}" style="text-align:center;"><a class="link" href="tiki-editpage.php?page={$user_pages[ix].pageName|escape:"url"}"><img border="0" alt="{tr}Edit{/tr}" title="{tr}Edit{/tr}: {$user_pages[ix].pageName}" src="pics/icons/page_edit.png" width="16" height="16" /></a></td>
  						</tr>
  					{/section}
  				</table>
  			</div>
  		</div>
	</div>
	{/if}
	
	{if $prefs.feature_galleries eq 'y' and $mytiki_gals eq 'y'}
		<h5>{tr}My Galleries{/tr}</h5>
		<div class="content">
  			<div class="cbox">
  				<div class="cbox-title">{if $userwatch eq $user}{tr}My galleries{/tr}{else}{tr}User Galleries{/tr}{/if}</div>
  				<div class="cbox-data">
  					<table class="normal">
  						{cycle values="even,odd" print=false}
  						{section name=ix loop=$user_galleries}
  						<tr><td class="{cycle advance=false}">
  							<a class="link" href="tiki-browse_gallery.php?galleryId={$user_galleries[ix].galleryId}">{$user_galleries[ix].name}</a>
  						</td><td class="{cycle}" style="text-align:center;">
  							<a class="link" href="tiki-galleries.php?editgal={$user_galleries[ix].galleryId}"><img border="0" alt="{tr}Edit{/tr}" title="{tr}Edit{/tr}" src="pics/icons/page_edit.png" width="16" height="16" /></a>
  						</td></tr>
  						{/section}
  					</table>
  				</div>
  			</div>
		</div>
	{/if}
	{if $prefs.feature_trackers eq 'y' and $mytiki_items eq 'y'}
		<h5>{tr}My Tracker Items{/tr}</h5>
		<div class="content">
  			<div class="cbox">
  				<div class="cbox-title">{if $userwatch eq $user}{tr}My items{/tr}{else}{tr}Assigned items{/tr}{/if}</div>
  				<div class="cbox-data">
  					<table class="normal">
  					<tr><th class="heading">{tr}Item{/tr}</th><th class="heading">{tr}Tracker{/tr}</th></tr>
  					{cycle values="even,odd" print=false}
   					{section name=ix loop=$user_items}
  					<tr><td class="{cycle advance=false}">
  						<a class="link" title="{tr}View{/tr}" href="tiki-view_tracker_item.php?trackerId={$user_items[ix].trackerId}&amp;itemId={$user_items[ix].itemId}">{$user_items[ix].value}</a></td>
   					<td class="{cycle}"><a class="link" title="{tr}View{/tr}" href="tiki-view_tracker.php?trackerId={$user_items[ix].trackerId}">{$user_items[ix].name}</a></td>
  					</tr>
  					{/section}
  					</table>
  				</div>
  			</div>
		</div>
	{/if}
	{if $prefs.feature_messages eq 'y' or $mytiki_msgs eq 'y'}
		<h5>{tr}My Messages{/tr}</h5>
		<div class="content">
  			<div class="cbox">
  				<div class="cbox-title">{tr}Unread Messages{/tr}</div>
  				<table  class="normal">
  					<tr><th class="heading">{tr}Subject{/tr}</th><th class="heading">{tr}From{/tr}</th><th class="heading">{tr}Date{/tr}</th></tr>
  					{cycle values="even,odd" print=false}
  					{section name=ix loop=$msgs}
  					<tr><td class="{cycle advance=false}">
  						<a class="link" title="{tr}View{/tr}" href="messu-read.php?offset=0&amp;flag=&amp;flagval=&amp;find=&amp;sort_mode=date_desc&amp;priority=&amp;msgId={$msgs[ix].msgId}">{$msgs[ix].subject}</a>
  					     </td>
  					<td class="{cycle advance=false}">{$msgs[ix].user_from}</td><td class="{cycle}">{$msgs[ix].date|tiki_short_datetime}</td></tr>
  					{/section}
  				</table>
  			</div>
		</div>
	{/if}
	
	{if $prefs.feature_tasks eq 'y' and $mytiki_tasks eq 'y'}
		<div class="content">
			<h5>{tr}My Tasks{/tr}</h5>
  			<div class="cbox">
  				<div class="cbox-title">{if $userwatch eq $user}{tr}My tasks{/tr}{else}{tr}User tasks{/tr}{/if}</div>
  				<table  class="normal">
  				{cycle values="even,odd" print=false}
  				{section name=ix loop=$tasks}
  				<tr><td class="{cycle}">
  				<a class="link" href="tiki-user_tasks.php?taskId={$tasks[ix].taskId}">{$tasks[ix].title}</a>
  				</td></tr>
  				{/section}
  				</table>
  			</div>
		</div>
	{/if}
	</div>
</div>
{literal}
<script type="text/javascript">
var tabs1 = new SimpleTabs($('tab-block-1'), {
	entrySelector: 'h5',
	ajaxOptions: {
		method: 'get'
	},
	onSelect: function(toggle, container) {
		toggle.addClass('tab-selected');
		container.effect('opacity').start(0, 1); // 1) first start the effect
		container.setStyle('display', ''); // 2) then show the element, to prevent flickering
		},
	onShow: function(toggle, container, index) {
			toggle.addClass('tab-selected');
			container.effect('opacity').start(0, 7); // 1) first start the effect
			container.setStyle('display', '');
		}
});

</script>
{/literal}
