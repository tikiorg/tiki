{*$Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin-include-anchors.tpl,v 1.30.2.5 2008-03-16 18:52:39 luciash Exp $*}
<a href="tiki-admin.php?page=general" title="{tr}General{/tr}" class="link"><img border="0" src="pics/large/icon-configuration.png" alt="{tr}General{/tr}" width="32" height="32" /></a>
<a href="tiki-admin.php?page=features" title="{tr}Features{/tr}" class="link"><img border="0" src="pics/large/boot.png" alt="{tr}Features{/tr}" width="32" height="32" /></a>
<a href="tiki-admin.php?page=login" title="{tr}Login{/tr}" class="link"><img border="0" src="pics/large/stock_quit.png" alt="{tr}Login{/tr}" width="32" height="32" /></a>
{if $prefs.feature_wiki eq 'y'}
<a href="tiki-admin.php?page=wiki" title="{tr}Wiki{/tr}" class="link"><img border="0" src="pics/large/wikipages.png" alt="{tr}Wiki{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_galleries eq 'y'}
<a href="tiki-admin.php?page=gal" title="{tr}Image Galleries{/tr}" class="link"><img border="0" src="pics/large/stock_select-color.png" alt="{tr}Image Galleries{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_articles eq 'y'}
<a href="tiki-admin.php?page=cms" title="{tr}Articles{/tr}" class="link"><img border="0" src="pics/large/stock_bold.png" alt="{tr}Articles{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_blogs eq 'y'}
<a href="tiki-admin.php?page=blogs" title="{tr}Blogs{/tr}" class="link"><img border="0" src="pics/large/blogs.png" alt="{tr}Blogs{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_forums eq 'y'}
<a href="tiki-admin.php?page=forums" title="{tr}Forums{/tr}" class="link"><img border="0" src="pics/large/stock_index.png" alt="{tr}Forums{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_directory eq 'y'}
<a href="tiki-admin.php?page=directory" title="{tr}Directory{/tr}" class="link"><img border="0" src="pics/large/gnome-fs-server.png" alt="{tr}Directory{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_file_galleries eq 'y'}
<a href="tiki-admin.php?page=fgal" title="{tr}File Galleries{/tr}" class="link"><img border="0" src="pics/large/file-manager.png" alt="{tr}File Galleries{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_faqs eq 'y'}
<a href="tiki-admin.php?page=faqs" title="{tr}FAQs{/tr}" class="link"><img border="0" src="pics/large/stock_dialog_question.png" alt="{tr}FAQs{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_maps eq 'y'}
<a href="tiki-admin.php?page=maps" title="{tr}Maps{/tr}" class="link"><img border="0" src="pics/large/maps.png" alt="{tr}Maps{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_trackers eq 'y'}
<a href="tiki-admin.php?page=trackers" title="{tr}Trackers{/tr}" class="link"><img border="0" src="pics/large/gnome-settings-font.png" alt="{tr}Trackers{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_calendar eq 'y'}
<a href="tiki-admin.php?page=calendar" title="{tr}Calendar{/tr}" class="link"><img border="0" src="pics/large/date.png" alt="{tr}Calendar{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_userfiles eq 'y'}
<a href="tiki-admin.php?page=userfiles" title="{tr}User files{/tr}" class="link"><img border="0" src="pics/large/userfiles.png" alt="{tr}User files{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_polls eq 'y'}
<a href="tiki-admin.php?page=polls" title="{tr}Polls{/tr}" class="link"><img border="0" src="pics/large/stock_missing-image.png" alt="{tr}Polls{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_search eq 'y'}
<a href="tiki-admin.php?page=search" title="{tr}Search{/tr}" class="link"><img border="0" src="pics/large/xfce4-appfinder.png" alt="{tr}Search{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_webmail eq 'y'}
<a href="tiki-admin.php?page=webmail" title="{tr}Webmail{/tr}" class="link"><img border="0" src="pics/large/evolution.png" alt="{tr}Webmail{/tr}" width="32" height="32" /></a>
{/if}
<a href="tiki-admin.php?page=rss" title="{tr}RSS{/tr}" class="link"><img border="0" src="pics/large/gnome-globe.png" alt="{tr}RSS{/tr}" width="32" height="32" /></a>
{if $prefs.feature_score eq 'y'}
<a href="tiki-admin.php?page=score" title="{tr}Score{/tr}" class="link"><img border="0" src="pics/large/stock_about.png" alt="{tr}Score{/tr}" width="32" height="32" /></a>
{/if}
<a href="tiki-admin.php?page=metatags" title="{tr}Meta Tags{/tr}" class="link"><img border="0" src="pics/large/metatags.png" alt="{tr}Meta Tags{/tr}" width="32" height="32" /></a>
<a href="tiki-admin.php?page=community" title="{tr}Community{/tr}" class="link"><img border="0" src="pics/large/users.png" alt="{tr}Community{/tr}" width="32" height="32" /></a>
{if $prefs.feature_messages eq 'y'}
<a href="tiki-admin.php?page=messages" title="{tr}Messages{/tr}" class="link"><img border="0" src="pics/large/messages.png" alt="{tr}Messages{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_intertiki eq 'y'}
<a href="tiki-admin.php?page=intertiki" title="{tr}Intertiki{/tr}" class="link"><img border="0" src="pics/large/intertiki.png" alt="{tr}InterTiki{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_freetags eq 'y'}
<a href="tiki-admin.php?page=freetags" title="{tr}Freetags{/tr}" class="link"><img border="0" src="pics/large/vcard.png" alt="{tr}Freetags{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_gmap eq 'y'}
<a href="tiki-admin.php?page=gmap" title="{tr}Google Maps{/tr}" class="link"><img border="0" src="pics/large/google_maps.png" alt="{tr}Google Maps{/tr}" width="32" height="32" /></a>
{/if}
<a href="tiki-admin.php?page=i18n" title="{tr}i18n{/tr}" class="link"><img border="0" src="pics/large/i18n.png" alt="{tr}i18n{/tr}" width="32" height="32" /></a>
{if $prefs.feature_wysiwyg eq 'y'}
<a href="tiki-admin.php?page=wysiwyg" title="{tr}Wysiwyg editor{/tr}" class="link"><img border="0" src="pics/large/wysiwyg.png" alt="{tr}Wysiwyg editor{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_categories eq 'y'}
<a href="tiki-admin.php?page=category" title="{tr}Category{/tr}" class="link"><img border="0"
   src="pics/large/categories.png" alt="{tr}Category{/tr}" /></a>
{/if}
<a href="tiki-admin.php?page=module" title="{tr}Module{/tr}" class="link"><img border="0"
   src="pics/large/display-capplet.png" alt="{tr}Module{/tr}" /></a>   
   
<a href="tiki-admin.php?page=look" title="{tr}Customize look and feel of your Tiki{/tr}" class="link"><img border="0"
   src="pics/large/gnome-settings-background.png" alt="{tr}Look &amp; Feel{/tr}" /></a>

<a href="tiki-admin.php?page=textarea" title="{tr}Text area{/tr}" class="link"><img border="0"
   src="img/icons/admin_textarea.png" alt="{tr}Text area{/tr}" /></a>      
{if $prefs.feature_copyright eq 'y'}
<a href="tiki-admin.php?page=copyright" title="{tr}Copyright{/tr}" class="link"><img border="0"
   src="pics/large/copyright48x48.png" alt="{tr}Copyright{/tr}" width="32" height="32" /></a>
{/if}
{if $prefs.feature_multimedia eq 'y'}
<a href="tiki-admin.php?page=multimedia" title="{tr}Multimedia{/tr}" class="link"><img border="0"
   src="img/icons/multimedia.png" alt="{tr}Multimedia{/tr}"  width="32" height="32" /></a>
{/if}
{if $prefs.feature_banners eq 'y'}
<a href="tiki-admin.php?page=ads" title="{tr}Site Ads and Banners{/tr}" class="link"><img border="0"
   src="pics/large/ads.png" alt="{tr}Site Ads and Banners{/tr}" width="32" height="32" /></a>
{/if}


