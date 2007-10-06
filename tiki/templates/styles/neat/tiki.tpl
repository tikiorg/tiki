{include file="header.tpl"}
{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}
{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
<div id="tiki-main">
  {if $user}
	  <div id="tiki-top-menu">
			{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
			  &nbsp;<a class="tikitopmenu" href="javascript:toggle('debugconsole');">{tr}debug{/tr}</a> //
			{/if}
		  {if $prefs.feature_userPreferences eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/user.gif' /><a class="tikitopmenu" href="tiki-user_preferences.php">{tr}Preferences{/tr}</a>
		  {/if}
		  &nbsp;&nbsp;<img src='styles/neat/user.gif' /><a class="tikitopmenu" href="tiki-my_tiki.php">{tr}MyTiki{/tr}</a>
		  {if $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="messu-mailbox.php">{tr}Messages{/tr}</a>
		  {/if}
		  {if $prefs.feature_userfiles eq 'y' and $tiki_p_userfiles eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-userfiles.php">{tr}User files{/tr}</a>
		  {/if}
		  {if $prefs.feature_minical eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-minical.php">{tr}Calendar{/tr}</a>
		  {/if}
		  {if $prefs.feature_usermenu eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-usermenu.php">{tr}Favorites{/tr}</a>
		  {/if}
 	      {if $prefs.feature_tasks eq 'y' and $tiki_p_tasks eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-user_tasks.php">{tr}Tasks{/tr}</a>
		  {/if}
		  {if $prefs.feature_user_bookmarks eq 'y' and $tiki_p_create_bookmarks eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-user_bookmarks.php">{tr}Bookmarks{/tr}</a>
		  {/if}
		  {if $prefs.feature_newsreader eq 'y' and $tiki_p_newsreader eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-newsreader_servers.php">{tr}Newsreader{/tr}</a>
		  {/if}
		  {if $prefs.user_assigned_modules eq 'y' and $tiki_p_configure_modules eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-user_assigned_modules.php">{tr}Modules{/tr}</a>
		  {/if}
		  {if $prefs.feature_webmail eq 'y' and $tiki_p_use_webmail eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-webmail.php">{tr}Webmail{/tr}</a>
		  {/if}
		  {if $prefs.feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-notepad_list.php">{tr}Notepad{/tr}</a>
		  {/if}
		  {if $prefs.feature_user_watches eq 'y'}
		  	&nbsp;&nbsp;<img src='styles/neat/linkOpaque.gif' /><a class="tikitopmenu" href="tiki-user_watches.php">{tr}Watches{/tr}</a>
		  {/if}
		  &nbsp;&nbsp;&nbsp;&nbsp;
	  </div>
  {/if}
  
  {if $prefs.feature_usermenu eq 'y'}	
	  <div id="usermenu">
	  	&nbsp;&nbsp;<a href="tiki-usermenu.php?url={$smarty.server.REQUEST_URI|escape:"url"}"><img src='img/icons/add.gif' border='0' alt='{tr}Add{/tr}' title='{tr}Add{/tr}' /></a>
	  	{section name=ix loop=$usr_user_menus}
  			&nbsp;&nbsp;<img style="vertical-align:bottom;" src="styles/neat/logoIcon.gif" /><a {if $usr_user_menus[ix].mode eq 'n'}target='_blank'{/if} href="{$usr_user_menus[ix].url}" class="tikitopmenu2">{$usr_user_menus[ix].name}</a>
  		{/section}
  		
	  </div>
  {/if}
 


  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" >
    <tr>
      {if $prefs.feature_left_column ne 'n'}
      <td id="leftcolumn">
      {section name=homeix loop=$left_modules}
      {$left_modules[homeix].data}
      {/section}
      
      </td>
      {/if}
      <td id="centercolumn">
			{/if}
			<div id="tiki-center">{$mid_data}
      </div>
			{if $prefs.feature_fullscreen != 'y' or $smarty.session.fullscreen != 'y'}
      </td>
      {if $prefs.feature_right_column ne 'n'}
      <td id="rightcolumn">
      {section name=homeix loop=$right_modules}
      {$right_modules[homeix].data}
      {/section}
      
      </td>
      {/if}
    </tr>
    </table>
  </div>
  {if $prefs.feature_bot_bar eq 'y'}
  <div id="tiki-bot">
    {include file="tiki-bot_bar.tpl"}
  </div>
  {/if}
</div>
{/if}
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
