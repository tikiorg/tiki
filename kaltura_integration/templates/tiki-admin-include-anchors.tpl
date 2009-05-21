{*$Id$*}

<a href="tiki-admin.php?page=general" title="{tr}General{/tr}">{icon _id="pics/large/icon-configuration.png" alt="{tr}General{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{icon _id="pics/large/boot.png" alt="{tr}Features{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=login" title="{tr}Login{/tr}" class="icon">{icon _id="pics/large/stock_quit.png" alt="{tr}Login{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=profiles" title="{tr}Profiles{/tr}" class="icon">{icon _id="pics/large/profiles.png" alt="{tr}Profiles{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=look" title="{tr}Customize look and feel of your Tiki{/tr}" class="icon">{icon _id="pics/large/gnome-settings-background.png" alt="{tr}Look &amp; Feel{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=i18n" title="{tr}i18n{/tr}" class="icon">{icon _id="pics/large/i18n.png" alt="{tr}i18n{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=textarea" title="{tr}Editing and Plugins{/tr}" class="icon">{icon _id="img/icons/admin_textarea.png" alt="{tr}Text area{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>      

<a href="tiki-admin.php?page=module" title="{tr}Module{/tr}" class="icon">{icon _id="pics/large/display-capplet.png" alt="{tr}Module{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>   

<a href="tiki-admin.php?page=metatags" title="{tr}Meta Tags{/tr}" class="icon">{icon _id="pics/large/metatags.png" alt="{tr}Meta Tags{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=rss" title="{tr}RSS{/tr}" class="icon">{icon _id="pics/large/gnome-globe.png" alt="{tr}RSS{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

<a href="tiki-admin.php?page=community" title="{tr}Community{/tr}" class="icon">{icon _id="pics/large/users.png" alt="{tr}Community{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>

{if $prefs.feature_wiki eq 'y'}
<a href="tiki-admin.php?page=wiki" title="{tr}Wiki{/tr}" class="icon">{icon _id="pics/large/wikipages.png" alt="{tr}Wiki{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_blogs eq 'y'}
<a href="tiki-admin.php?page=blogs" title="{tr}Blogs{/tr}" class="icon">{icon _id="pics/large/blogs.png" alt="{tr}Blogs{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_galleries eq 'y'}
<a href="tiki-admin.php?page=gal" title="{tr}Image Galleries{/tr}" class="icon">{icon _id="pics/large/stock_select-color.png" alt="{tr}Image Galleries{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_file_galleries eq 'y'}
<a href="tiki-admin.php?page=fgal" title="{tr}File Galleries{/tr}" class="icon">{icon _id="pics/large/file-manager.png" alt="{tr}File Galleries{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_articles eq 'y'}
<a href="tiki-admin.php?page=cms" title="{tr}Articles{/tr}" class="icon">{icon _id="pics/large/stock_bold.png" alt="{tr}Articles{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_forums eq 'y'}
<a href="tiki-admin.php?page=forums" title="{tr}Forums{/tr}" class="icon">{icon _id="pics/large/stock_index.png" alt="{tr}Forums{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_trackers eq 'y'}
<a href="tiki-admin.php?page=trackers" title="{tr}Trackers{/tr}" class="icon">{icon _id="pics/large/gnome-settings-font.png" alt="{tr}Trackers{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_polls eq 'y'}
<a href="tiki-admin.php?page=polls" title="{tr}Polls{/tr}" class="icon">{icon _id="pics/large/stock_missing-image.png" alt="{tr}Polls{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_calendar eq 'y'}
<a href="tiki-admin.php?page=calendar" title="{tr}Calendar{/tr}" class="icon">{icon _id="pics/large/date.png" alt="{tr}Calendar{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_categories eq 'y'}
<a href="tiki-admin.php?page=category" title="{tr}Category{/tr}" class="icon">{icon _id="pics/large/categories.png" alt="{tr}Category{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_score eq 'y'}
<a href="tiki-admin.php?page=score" title="{tr}Score{/tr}" class="icon">{icon _id="pics/large/stock_about.png" alt="{tr}Score{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_freetags eq 'y'}
<a href="tiki-admin.php?page=freetags" title="{tr}Freetags{/tr}" class="icon">{icon _id="pics/large/vcard.png" alt="{tr}Freetags{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_search eq 'y'}
<a href="tiki-admin.php?page=search" title="{tr}Search{/tr}" class="icon">{icon _id="pics/large/xfce4-appfinder.png" alt="{tr}Search{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_faqs eq 'y'}
<a href="tiki-admin.php?page=faqs" title="{tr}FAQs{/tr}" class="icon">{icon _id="pics/large/stock_dialog_question.png" alt="{tr}FAQs{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_directory eq 'y'}
<a href="tiki-admin.php?page=directory" title="{tr}Directory{/tr}" class="icon">{icon _id="pics/large/gnome-fs-server.png" alt="{tr}Directory{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_maps eq 'y'}
<a href="tiki-admin.php?page=maps" title="{tr}Maps{/tr}" class="icon">{icon _id="pics/large/maps.png" alt="{tr}Maps{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_gmap eq 'y'}
<a href="tiki-admin.php?page=gmap" title="{tr}Google Maps{/tr}" class="icon">{icon _id="pics/large/google_maps.png" alt="{tr}Google Maps{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_multimedia eq 'y'}
<a href="tiki-admin.php?page=multimedia" title="{tr}Multimedia{/tr}" class="icon">{icon _id="pics/large/multimedia.png" alt="{tr}Multimedia{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_copyright eq 'y'}
<a href="tiki-admin.php?page=copyright" title="{tr}Copyright{/tr}" class="icon">{icon _id="pics/large/copyright.png" alt="{tr}Copyright{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_messages eq 'y'}
<a href="tiki-admin.php?page=messages" title="{tr}Messages{/tr}" class="icon">{icon _id="pics/large/messages.png" alt="{tr}Messages{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_userfiles eq 'y'}
<a href="tiki-admin.php?page=userfiles" title="{tr}User files{/tr}" class="icon">{icon _id="pics/large/userfiles.png" alt="{tr}User files{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_webmail eq 'y'}
<a href="tiki-admin.php?page=webmail" title="{tr}Webmail{/tr}" class="icon">{icon _id="pics/large/evolution.png" alt="{tr}Webmail{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_wysiwyg eq 'y'}
<a href="tiki-admin.php?page=wysiwyg" title="{tr}Wysiwyg editor{/tr}" class="icon">{icon _id="pics/large/wysiwyg.png" alt="{tr}Wysiwyg editor{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_banners eq 'y'}
<a href="tiki-admin.php?page=ads" title="{tr}Site Ads and Banners{/tr}" class="icon">{icon _id="pics/large/ads.png" alt="{tr}Site Ads and Banners{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_intertiki eq 'y'}
<a href="tiki-admin.php?page=intertiki" title="{tr}Intertiki{/tr}" class="icon">{icon _id="pics/large/intertiki.png" alt="{tr}InterTiki{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_semantic neq 'n'}
<a href="tiki-admin.php?page=semantic" title="{tr}Semantic wiki links{/tr}" class="icon">{icon _id="pics/large/semantic.png" alt="{tr}Semantic links{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}

{if $prefs.feature_webservices neq 'n'}
<a href="tiki-admin.php?page=webservices" title="{tr}Webservices{/tr}" class="icon">{icon _id="pics/large/webservices.png" alt="{tr}Webservices{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}
{if $prefs.feature_sefurl neq 'n'}
<a href="tiki-admin.php?page=sefurl" title="{tr}Sef URL{/tr}" class="icon">{icon _id="pics/large/goto.png" alt="{tr}Sef URL{/tr}" class="reflect" style="vertical-align: middle" width="32" height="32"}</a>
{/if}
<br class="clear" />
