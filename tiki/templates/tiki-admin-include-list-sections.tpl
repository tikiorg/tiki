{*
 * If you want to change this page, check http://www.tikiwiki.org/tiki-index.php?page=AdministrationDev
 * there you"ll find attached a gimp image containing this page with icons in separated layers
 *}

<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	<div class="rbox-data" name="tip">{tr}Enable/disable Tiki features in {/tr}<a class="rbox-link" href="tiki-admin.php?page=features">{tr}Admin{/tr}&nbsp;{$prefs.site_crumb_seper}&nbsp;{tr}Features{/tr}</a>{tr}, but configure them elsewhere{/tr}</div>
</div>

<div class="cbox">
  <div class="cbox-title">
    {tr}{$crumbs[$crumb]->description}{/tr}
    {help crumb=$crumbs[$crumb]}
  </div>
  <div class="cbox-data">
    <a href="tiki-admin.php?page=general" class="admbox" style="background-image: url('pics/large/icon-configuration48x48.png')">
      <img src="pics/trans.png" alt="{tr}General{/tr}" title="{tr}General{/tr}" /><span>{tr}General{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=features" class="admbox" style="background-image: url('pics/large/boot48x48.png')">
      <img src="pics/trans.png" alt="{tr}Features{/tr}" title="{tr}Features{/tr}" /><span>{tr}Features{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=login" class="admbox" style="background-image: url('pics/large/stock_quit48x48.png')">
      <img src="pics/trans.png" alt="{tr}Login{/tr}" title="{tr}Login{/tr}" /><span>{tr}Login{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=wiki" class="admbox{if $prefs.feature_wiki ne 'y'} off{/if}" style="background-image: url('pics/large/wikipages48x48.png')">
      <img src="pics/trans.png" alt="{tr}Wiki{/tr}" title="{tr}Wiki{/tr}{if $prefs.feature_wiki ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Wiki{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=gal" class="admbox{if $prefs.feature_galleries ne 'y'} off{/if}" style="background-image: url('pics/large/stock_select-color48x48.png')">
      <img src="pics/trans.png" alt="{tr}Image Galleries{/tr}" title="{tr}Image Galleries{/tr}{if $prefs.feature_galleries ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Image Galleries{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=cms" class="admbox{if $prefs.feature_articles ne 'y'} off{/if}" style="background-image: url('pics/large/stock_bold48x48.png')">
      <img src="pics/trans.png" alt="{tr}Articles{/tr}" title="{tr}Articles{/tr}{if $prefs.feature_articles ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Articles{/tr}</span>
    </a>        
    <a href="tiki-admin.php?page=blogs" class="admbox{if $prefs.feature_blogs ne 'y'} off{/if}" style="background-image: url('pics/large/blogs48x48.png')">
      <img src="pics/trans.png" alt="{tr}Blogs{/tr}" title="{tr}Blogs{/tr}{if $prefs.feature_blogs ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Blogs{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=forums" class="admbox{if $prefs.feature_forums ne 'y'} off{/if}" style="background-image: url('pics/large/stock_index48x48.png')">
      <img src="pics/trans.png" alt="{tr}Forums{/tr}" title="{tr}Forums{/tr}{if $prefs.feature_forums ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Forums{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=directory" class="admbox{if $prefs.feature_directory ne 'y'} off{/if}" style="background-image: url('pics/large/gnome-fs-server48x48.png')">
      <img src="pics/trans.png" alt="{tr}Directory{/tr}" title="{tr}Directory{/tr}{if $prefs.feature_directory ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Directory{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=fgal" class="admbox{if $prefs.feature_file_galleries ne 'y'} off{/if}" style="background-image: url('pics/large/file-manager48x48.png')">
      <img src="pics/trans.png" alt="{tr}File Galleries{/tr}" title="{tr}File Galleries{/tr}{if $prefs.feature_file_galleries ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}File Galleries{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=faqs" class="admbox{if $prefs.feature_faqs ne 'y'} off{/if}" style="background-image: url('pics/large/stock_dialog_question48x48.png')">
      <img src="pics/trans.png" alt="{tr}FAQs{/tr}" title="{tr}FAQs{/tr}{if $prefs.feature_faqs ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}FAQs{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=maps" class="admbox{if $prefs.feature_maps ne 'y'} off{/if}" style="background-image: url('pics/large/maps48x48.png')">
      <img src="pics/trans.png" alt="{tr}Maps{/tr}" title="{tr}Maps{/tr}{if $prefs.feature_maps ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Maps{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=trackers" class="admbox{if $prefs.feature_trackers ne 'y'} off{/if}" style="background-image: url('pics/large/gnome-settings-font48x48.png')">
      <img src="pics/trans.png" alt="{tr}Trackers{/tr}" title="{tr}Trackers{/tr}{if $prefs.feature_trackers ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Trackers{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=calendar" class="admbox{if $prefs.feature_calendar ne 'y'} off{/if}" style="background-image: url('pics/large/date48x48.png')">
      <img src="pics/trans.png" alt="{tr}Calendar{/tr}" title="{tr}Calendar{/tr}{if $prefs.feature_calendar ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Calendar{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=userfiles" class="admbox{if $prefs.feature_userfiles ne 'y'} off{/if}" style="background-image: url('pics/large/userfiles48x48.png')">
      <img src="pics/trans.png" alt="{tr}User files{/tr}" title="{tr}User files{/tr}{if $prefs.feature_userfiles ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}User files{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=polls" class="admbox{if $prefs.feature_polls ne 'y'} off{/if}" style="background-image: url('pics/large/stock_missing-image48x48.png')">
      <img src="pics/trans.png" alt="{tr}Polls{/tr}" title="{tr}Polls{/tr}{if $prefs.feature_polls ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Polls{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=search" class="admbox{if $prefs.feature_search ne 'y'} off{/if}" style="background-image: url('pics/large/xfce4-appfinder48x48.png')">
      <img src="pics/trans.png" alt="{tr}Search{/tr}" title="{tr}Search{/tr}{if $prefs.feature_search ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Search{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=webmail" class="admbox{if $prefs.feature_webmail ne 'y'} off{/if}" style="background-image: url('pics/large/evolution48x48.png')">
      <img src="pics/trans.png" alt="{tr}Webmail{/tr}" title="{tr}Webmail{/tr}{if $prefs.feature_webmail ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Webmail{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=rss" class="admbox" style="background-image: url('pics/large/gnome-globe48x48.png')">
      <img src="pics/trans.png" alt="{tr}RSS{/tr}" title="{tr}RSS{/tr}" /><span>{tr}RSS{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=score" class="admbox{if $prefs.feature_score ne 'y'} off{/if}" style="background-image: url('pics/large/stock_about48x48.png')">
      <img src="pics/trans.png" alt="{tr}Score{/tr}" title="{tr}Score{/tr}{if $prefs.feature_score ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Score{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=metatags" class="admbox" style="background-image: url('pics/large/metatags48x48.png')">
      <img src="pics/trans.png" alt="{tr}Meta Tags{/tr}" title="{tr}Meta Tags{/tr}" /><span>{tr}Meta Tags{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=community" class="admbox" style="background-image: url('pics/large/users48x48.png')">
      <img src="pics/trans.png" alt="{tr}Community{/tr}" title="{tr}Community{/tr}" /><span>{tr}Community{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=intertiki" class="admbox{if $prefs.feature_intertiki ne 'y'} off{/if}" style="background-image: url('pics/large/intertiki48x48.png')">
      <img src="pics/trans.png" alt="{tr}InterTiki{/tr}" title="{tr}InterTiki{/tr}{if $prefs.feature_intertiki ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}InterTiki{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=freetags" class="admbox{if $prefs.feature_freetags ne 'y'} off{/if}" style="background-image: url('pics/large/vcard48x48.png')">
      <img src="pics/trans.png" alt="{tr}Freetags{/tr}" title="{tr}Freetags{/tr}{if $prefs.feature_freetags ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Freetags{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=gmap" class="admbox{if $prefs.feature_gmap ne 'y'} off{/if}" style="background-image: url('pics/large/google_maps48x48.png')">
      <img src="pics/trans.png" alt="{tr}Google Maps{/tr}" title="{tr}Google Maps{/tr}{if $prefs.feature_gmap ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Google Maps{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=i18n" class="admbox" style="background-image: url('pics/large/i18n48x48.png')">
      <img src="pics/trans.png" alt="{tr}i18n{/tr}" title="{tr}i18n{/tr}" /><span>{tr}i18n{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=wysiwyg" class="admbox{if $prefs.feature_wysiwyg ne 'y'} off{/if}" style="background-image: url('pics/large/wysiwyg48x48.png')">
      <img src="pics/trans.png" alt="{tr}Wysiwyg{/tr}" title="{tr}Wysiwyg{/tr}{if $prefs.feature_wysiwyg ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Wysiwyg{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=copyright" class="admbox{if $prefs.feature_copyright ne 'y'} off{/if}" style="background-image: url('pics/large/copyright48x48.png')">
      <img src="pics/trans.png" alt="{tr}Copyright{/tr}" title="{tr}Copyright{/tr}{if $prefs.feature_copyright ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Copyright{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=category" class="admbox{if $prefs.feature_categories ne 'y'} off{/if}" style="background-image: url('img/icons/admin_category.png')">
      <img src="pics/trans.png" alt="{tr}Categories{/tr}" title="{tr}Categories{/tr}{if $prefs.feature_categories ne 'y'} ({tr}Disabled{/tr}){/if}" /><span>{tr}Categories{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=module" class="admbox" style="background-image: url('img/mytiki/modules.gif')">
      <img src="pics/trans.png" alt="{tr}Module{/tr}" title="{tr}Module{/tr}" /><span>{tr}Module{/tr}</span>
    </a>
    <a href="tiki-admin.php?page=look" class="admbox" style="background-image: url('pics/large/gnome-settings-background48x48.png')">
      <img src="pics/trans.png" alt="{tr}Look &amp; Feel{/tr}" title="{tr}Customize look and feel of your Tiki{/tr}" /><span>{tr}Look &amp; Feel{/tr}</span>
    </a>
   <a href="tiki-admin.php?page=textarea" class="admbox" style="background-image: url('img/icons/admin_textarea.png')">
      <img src="pics/trans.png" alt="{tr}Text Area{/tr}" title="{tr}Text Area{/tr}" /><span>{tr}Text Area{/tr}</span>
    </a>
  <a href="tiki-admin.php?page=multimedia" class="admbox" style="background-image: url('img/icons/multimedia.png')">
      <img src="pics/trans.png" alt="{tr}Multimedia{/tr}" title="{tr}Multimedia{/tr}" /><span>{tr}Multimedia{/tr}</span>
    </a>

  </div>
</div>

