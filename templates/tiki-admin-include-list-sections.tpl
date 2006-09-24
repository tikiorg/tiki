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
    <a title="{tr}Features{/tr}" href="tiki-admin.php?page=features" class="link">
      <div class="admbox">
      <img border="0" src="pics/jini/boot48x48.png" alt="icon" /><br />{tr}Features{/tr}
      </div>
    </a>
    <a title="{tr}General{/tr}" href="tiki-admin.php?page=general" class="link">
      <div class="admbox">
      <img border="0" src="pics/jini/icon-configuration48x48.png" alt="icon" /><br />{tr}General{/tr}
      </div>
    </a>
    <a title="{tr}Login{/tr}" href="tiki-admin.php?page=login" class="link">
      <div class="admbox">
      <img border="0" src="pics/jini/stock_quit48x48.png" alt="icon" /><br />{tr}Login{/tr}
      </div>
    </a>
    <a title="{tr}Wiki{/tr}" href="tiki-admin.php?page=wiki" class="link">
      <div class="admbox">
      {if $feature_wiki eq 'y'}
      <img border="0" src="pics/jini/stock_copy48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/stock_copy48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Wiki{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=gal" title="{tr}Image Galleries{/tr}" class="link">
      <div class="admbox">
      {if $feature_galleries eq 'y'}
      <img border="0" src="pics/jini/stock_select-color48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/stock_select-color48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Image Galleries{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=cms" title="{tr}Articles{/tr}" class="link">
      <div class="admbox">
      {if $feature_articles eq 'y'}
      <img border="0" src="pics/jini/stock_bold48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/stock_bold48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Articles{/tr}
      </div>
    </a>        
    <a href="tiki-admin.php?page=blogs" title="{tr}Blogs{/tr}" class="link">
      <div class="admbox">
      {if $feature_blogs eq 'y'}
      <img border="0" src="pics/jini/gnome-memo48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/gnome-memo48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Blogs{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=forums" title="{tr}Forums{/tr}" class="link">
      <div class="admbox">
      {if $feature_forums eq 'y'}
      <img border="0" src="pics/jini/stock_index48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/stock_index48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Forums{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=directory" title="{tr}Directory{/tr}" class="link">
      <div class="admbox">
      {if $feature_directory eq 'y'}
      <img border="0" src="pics/jini/gnome-fs-server48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/gnome-fs-server48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Directory{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=fgal" title="{tr}File Galleries{/tr}" class="link">
      <div class="admbox">
      {if $feature_file_galleries eq 'y'}
      <img border="0" src="pics/jini/file-manager48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/file-manager48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}File Galleries{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=faqs" title="{tr}FAQs{/tr}" class="link">
      <div class="admbox">
      {if $feature_faqs eq 'y'}
      <img border="0" src="pics/jini/stock_dialog_question48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/stock_dialog_question48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}FAQs{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=maps" title="{tr}Maps{/tr}" class="link">
      <div class="admbox">
      {if $feature_maps eq 'y'}
      <img border="0" src="pics/jini/gftp48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/gftp48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Maps{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=trackers" title="{tr}Trackers{/tr}" class="link">
      <div class="admbox">
      {if $feature_trackers eq 'y'}
      <img border="0" src="img/icons/admin_trackers.png" alt="icon" />
      {else}
      <img border="0" src="img/icons/admin_trackers_grey.png" alt="icon" />
      {/if}
      <br />{tr}Trackers{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=calendar" title="{tr}Calendar{/tr}" class="link">
      <div class="admbox">
      {if $feature_calendar eq 'y'}
      <img border="0" src="pics/jini/date48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/date48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Calendar{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=userfiles" title="{tr}User files{/tr}" class="link">
      <div class="admbox">
      {if $feature_userfiles eq 'y'}
      <img border="0" src="img/icons/admin_userfiles.png" alt="icon" />
      {else}
      <img border="0" src="img/icons/admin_userfiles_grey.png" alt="icon" />
      {/if}
      <br />{tr}User files{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=polls" title="{tr}Polls{/tr}" class="link">
      <div class="admbox">
      {if $feature_polls eq 'y'}
      <img border="0" src="img/icons/admin_polls.png" alt="icon" />
      {else}
      <img border="0" src="img/icons/admin_polls_grey.png" alt="icon" />
      {/if}
      <br />{tr}Polls{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=search" title="{tr}Search{/tr}" class="link">
      <div class="admbox">
      {if $feature_search eq 'y'}
      <img border="0" src="pics/jini/xfce4-appfinder48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/xfce4-appfinder48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Search{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=webmail" title="{tr}Webmail{/tr}" class="link">
      <div class="admbox">
      {if $feature_webmail eq 'y'}
      <img border="0" src="pics/jini/evolution48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/evolution48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Webmail{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=rss" title="{tr}RSS{/tr}" class="link">
      <div class="admbox">
      <img border="0" src="pics/jini/gnome-globe48x48.png" alt="icon" /><br />{tr}RSS{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=score" title="{tr}Score{/tr}" class="link">
      <div class="admbox">
      {if $feature_score eq 'y'}
      <img border="0" src="pics/jini/stock_about48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/stock_about48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Score{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=metatags" title="{tr}Meta Tags{/tr}" class="link">
      <div class="admbox">
      <img border="0" src="img/icons/admin_metatags.png" alt="icon" /><br />{tr}Meta Tags{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=community" title="{tr}Community{/tr}" class="link">
      <div class="admbox">
      <img border="0" src="pics/jini/users48x48.png" alt="icon" /><br />{tr}Community{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=siteid" title="{tr}Site Identity{/tr}" class="link">
      <div class="admbox">
      {if $feature_siteidentity eq 'y'}
      <img border="0" src="pics/jini/gnome-settings-background48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/gnome-settings-background48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Site Identity{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=intertiki" title="{tr}InterTiki{/tr}" class="link">
      <div class="admbox">
      {if $feature_intertiki eq 'y'}
      <img border="0" src="pics/jini/stock_line-in48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/stock_line-in48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}InterTiki{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=freetags" title="{tr}Freetags{/tr}" class="link">
      <div class="admbox">
      {if $feature_freetags eq 'y'}
      <img border="0" src="pics/jini/vcard48x48.png" alt="icon" />
      {else}
      <img border="0" src="pics/jini/vcard48x48grey.png" alt="icon" />
      {/if}
      <br />{tr}Freetags{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=gmap" title="{tr}Google Maps{/tr}" class="link">
      <div class="admbox">
      {if $feature_gmap eq 'y'}
      <img border="0" src="img/icons/admin_gmap.png" alt="icon" />
      {else}
      <img border="0" src="img/icons/admin_gmap_grey.png" alt="icon" />
      {/if}
      <br />{tr}Google Maps{/tr}
      </div>
    </a>
    <a href="tiki-admin.php?page=i18n" title="{tr}i18n{/tr}" class="link">
      <div class="admbox">
      <img border="0" src="img/icons/admin_i18n.png" alt="icon" />
      <br />{tr}i18n{/tr}
      </div>
    </a>
  </div>
</div>

