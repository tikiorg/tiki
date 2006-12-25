{*
 * If you want to change this page, check http://www.tikiwiki.org/tiki-index.php?page=AdministrationDev
 * there you"ll find attached a gimp image containing this page with icons in separated layers
 *}

<div class="rbox" name="tip">
<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
<div class="rbox-data" name="tip">{tr}Enable/disable Tiki features in {/tr} <a class="rbox-link" href="tiki-admin.php?page=features">{tr}Admin->Features{/tr}</a>{tr}, but configure them elsewhere{/tr}</div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>
  <div class="cbox-data">
    <a title="{tr}Features{/tr}" href="tiki-admin.php?page=features" class="admbox">
      <img border="0" src="pics/large/boot48x48.png" alt="{tr}Features{/tr}" /><br />{tr}Features{/tr}
    </a>
    <a title="{tr}General{/tr}" href="tiki-admin.php?page=general" class="admbox">
      <img border="0" src="pics/large/icon-configuration48x48.png" alt="{tr}General{/tr}" /><br />{tr}General{/tr}
    </a>
    <a title="{tr}Login{/tr}" href="tiki-admin.php?page=login" class="admbox">
      <img border="0" src="pics/large/stock_quit48x48.png" alt="{tr}Login{/tr}" /><br />{tr}Login{/tr}
    </a>
    <a title="{tr}Wiki{/tr}" href="tiki-admin.php?page=wiki" class="admbox{if $feature_wiki ne 'y'} off{/if}">
      <img border="0" src="pics/large/wikipages48x48.png" alt="{tr}Wiki{/tr}" /><br />{tr}Wiki{/tr}
    </a>
    <a href="tiki-admin.php?page=gal" title="{tr}Image Galleries{/tr}" class="admbox{if $feature_galleries ne 'y'} off{/if}">
      <img border="0" src="pics/large/stock_select-color48x48.png" alt="{tr}Image Galleries{/tr}" /><br />{tr}Image Galleries{/tr}
    </a>
    <a href="tiki-admin.php?page=cms" title="{tr}Articles{/tr}" class="admbox{if $feature_articles ne 'y'} off{/if}">
      <img border="0" src="pics/large/stock_bold48x48.png" alt="{tr}Articles{/tr}" /><br />{tr}Articles{/tr}
    </a>        
    <a href="tiki-admin.php?page=blogs" title="{tr}Blogs{/tr}" class="admbox{if $feature_blogs ne 'y'} off{/if}">
      <img border="0" src="pics/large/blogs48x48.png" alt="{tr}Blogs{/tr}" /><br />{tr}Blogs{/tr}
    </a>
    <a href="tiki-admin.php?page=forums" title="{tr}Forums{/tr}" class="admbox{if $feature_forums ne 'y'} off{/if}">
      <img border="0" src="pics/large/stock_index48x48.png" alt="{tr}Forums{/tr}" /><br />{tr}Forums{/tr}
    </a>
    <a href="tiki-admin.php?page=directory" title="{tr}Directory{/tr}" class="admbox{if $feature_directory ne 'y'} off{/if}">
      <img border="0" src="pics/large/gnome-fs-server48x48.png" alt="{tr}Directory{/tr}" /><br />{tr}Directory{/tr}
    </a>
    <a href="tiki-admin.php?page=fgal" title="{tr}File Galleries{/tr}" class="admbox{if $feature_file_galleries ne 'y'} off{/if}">
      <img border="0" src="pics/large/file-manager48x48.png" alt="{tr}File Galleries{/tr}" /><br />{tr}File Galleries{/tr}
    </a>
    <a href="tiki-admin.php?page=faqs" title="{tr}FAQs{/tr}" class="admbox{if $feature_faqs ne 'y'} off{/if}">
      <img border="0" src="pics/large/stock_dialog_question48x48.png" alt="{tr}FAQs{/tr}" /><br />{tr}FAQs{/tr}
    </a>
    <a href="tiki-admin.php?page=maps" title="{tr}Maps{/tr}" class="admbox{if $feature_maps ne 'y'} off{/if}">
      <img border="0" src="pics/large/maps48x48.png" alt="{tr}Maps{/tr}" /><br />{tr}Maps{/tr}
    </a>
    <a href="tiki-admin.php?page=trackers" title="{tr}Trackers{/tr}" class="admbox{if $feature_trackers ne 'y'} off{/if}">
      <img border="0" src="pics/large/gnome-settings-font48x48.png" alt="{tr}Trackers{/tr}" /><br />{tr}Trackers{/tr}
    </a>
    <a href="tiki-admin.php?page=calendar" title="{tr}Calendar{/tr}" class="admbox{if $feature_calendar ne 'y'} off{/if}">
      <img border="0" src="pics/large/date48x48.png" alt="{tr}Calendar{/tr}" /><br />{tr}Calendar{/tr}
    </a>
    <a href="tiki-admin.php?page=userfiles" title="{tr}User files{/tr}" class="admbox{if $feature_userfiles ne 'y'} off{/if}">
      <img border="0" src="pics/large/userfiles48x48.png" alt="{tr}User files{/tr}" /><br />{tr}User files{/tr}
    </a>
    <a href="tiki-admin.php?page=polls" title="{tr}Polls{/tr}" class="admbox{if $feature_polls ne 'y'} off{/if}">
      <img border="0" src="pics/large/stock_missing-image48x48.png" alt="{tr}Polls{/tr}" /><br />{tr}Polls{/tr}
    </a>
    <a href="tiki-admin.php?page=search" title="{tr}Search{/tr}" class="admbox{if $feature_search ne 'y'} off{/if}">
      <img border="0" src="pics/large/xfce4-appfinder48x48.png" alt="{tr}Search{/tr}" /><br />{tr}Search{/tr}
    </a>
    <a href="tiki-admin.php?page=webmail" title="{tr}Webmail{/tr}" class="admbox{if $feature_webmail ne 'y'} off{/if}">
      <img border="0" src="pics/large/evolution48x48.png" alt="{tr}Webmail{/tr}" /><br />{tr}Webmail{/tr}
    </a>
    <a href="tiki-admin.php?page=rss" title="{tr}RSS{/tr}" class="admbox">
      <img border="0" src="pics/large/gnome-globe48x48.png" alt="{tr}RSS{/tr}" /><br />{tr}RSS{/tr}
    </a>
    <a href="tiki-admin.php?page=score" title="{tr}Score{/tr}" class="admbox{if $feature_score ne 'y'} off{/if}">
      <img border="0" src="pics/large/stock_about48x48.png" alt="{tr}Score{/tr}" /><br />{tr}Score{/tr}
    </a>
    <a href="tiki-admin.php?page=metatags" title="{tr}Meta Tags{/tr}" class="admbox">
      <img border="0" src="pics/large/metatags48x48.png" alt="{tr}Meta Tags{/tr}" /><br />{tr}Meta Tags{/tr}
    </a>
    <a href="tiki-admin.php?page=community" title="{tr}Community{/tr}" class="admbox">
      <img border="0" src="pics/large/users48x48.png" alt="{tr}Community{/tr}" /><br />{tr}Community{/tr}
    </a>
    <a href="tiki-admin.php?page=siteid" title="{tr}Site Identity{/tr}" class="admbox{if $feature_siteidentity ne 'y'} off{/if}">
      <img border="0" src="pics/large/gnome-settings-background48x48.png" alt="{tr}Site Identity{/tr}" /><br />{tr}Site Identity{/tr}
    </a>
    <a href="tiki-admin.php?page=intertiki" title="{tr}InterTiki{/tr}" class="admbox{if $feature_intertiki ne 'y'} off{/if}">
      <img border="0" src="pics/large/intertiki48x48.png" alt="{tr}InterTiki{/tr}" /><br />{tr}InterTiki{/tr}
    </a>
    <a href="tiki-admin.php?page=freetags" title="{tr}Freetags{/tr}" class="admbox{if $feature_freetags ne 'y'} off{/if}">
      <img border="0" src="pics/large/vcard48x48.png" alt="{tr}Freetags{/tr}" /><br />{tr}Freetags{/tr}
    </a>
    <a href="tiki-admin.php?page=gmap" title="{tr}Google Maps{/tr}" class="admbox{if $feature_gmap ne 'y'} off{/if}">
      <img border="0" src="pics/large/google_maps48x48.png" alt="{tr}Google Maps{/tr}" /><br />{tr}Google Maps{/tr}
    </a>
    <a href="tiki-admin.php?page=i18n" title="{tr}i18n{/tr}" class="admbox">
      <img border="0" src="pics/large/i18n48x48.png" alt="{tr}i18n{/tr}" /><br />{tr}i18n{/tr}
    </a>
    <a href="tiki-admin.php?page=wysiwyg" title="{tr}wysiwyg{/tr}" class="admbox">
      <img border="0" src="pics/large/wysiwyg48x48.png" alt="{tr}wysiwyg{/tr}" /><br />{tr}wysiwyg{/tr}
    </a>
  </div>
</div>
<br />
