<h2><a class="wiki" href="tiki-admin.php">{tr}Administration{/tr}</a></h2>

<div class="cbox">
<div class="cbox-title">Features</div>
<div class="cbox-data">
<form action="tiki-admin.php" method="post">
<table>
<tr><td class="text">{tr}Wiki{/tr}:</td><td><input type="checkbox" name="feature_wiki" {if $feature_wiki eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Search{/tr}:</td><td><input type="checkbox" name="feature_search" {if $feature_search eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Image Galleries{/tr}:</td><td><input type="checkbox" name="feature_galleries" {if $feature_galleries eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Featured links{/tr}:</td><td><input type="checkbox" name="feature_featuredLinks" {if $feature_featuredLinks eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Hotwords{/tr}:</td><td><input type="checkbox" name="feature_hotwords" {if $feature_hotwords eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}User preferences screen{/tr}:</td><td><input type="checkbox" name="feature_userPreferences" {if $feature_userPreferences eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Articles{/tr}:</td><td><input type="checkbox" name="feature_articles" {if $feature_articles eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Submissions{/tr}:</td><td><input type="checkbox" name="feature_submissions" {if $feature_submissions eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Blogs{/tr}:</td><td><input type="checkbox" name="feature_blogs" {if $feature_blogs eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}XMLRPC API{/tr}:</td><td><input type="checkbox" name="feature_xmlrpc" {if $feature_xmlrpc eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td align="center" class="text" colspan="2"><input type="submit" name="features" value="{tr}Set features{/tr}" /></td></tr>
</table>
</form>
</div>
</div>

<div class="cbox">
<div class="cbox-title">General preferences and settings</div>
<div class="cbox-data">
<form action="tiki-admin.php" method="post">
<div class="simplebox">
<table>
<tr><td class="text">{tr}Index page{/tr}:</td><td>
<select name="tikiIndex">
<option value="tiki-index.php" {if $tikiIndex eq 'tiki-index.php'}selected="selected"{/if}>Wiki</option>
<option value="tiki-view_articles.php" {if $tikiIndex eq 'tiki-view_articles.php'}selected="selected"{/if}>Articles</option>
<option value="{$home_blog_url}" {if $tikiIndex eq $home_blog_url}selected="selected"{/if}>Blog: {$home_blog_name}</option>
<option value="{$home_gallery_url}" {if $tikiIndex eq $home_gallery_url}selected="selected"{/if}>Gallery: {$home_gal_name}</option>
</select>
</td></tr>
<tr><td class="text">{tr}Users can register{/tr}:</td><td><input type="checkbox" name="allowRegister" {if $allowRegister eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Open external links in new window{/tr}:</td><td><input type="checkbox" name="popupLinks" {if $popupLinks eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Display modules to all groups always{/tr}:</td><td><input type="checkbox" name="modallgroups" {if $modallgroups eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Use cache for external pages{/tr}:</td><td><input type="checkbox" name="cachepages" {if $cachepages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Use cache for external images{/tr}:</td><td><input type="checkbox" name="cacheimages" {if $cacheimages eq 'y'}checked="checked"{/if}/></td></tr>
<tr><td class="text">{tr}Maximum number of versions for history{/tr}: </td><td><input size="5" type="text" name="maxVersions" value="{$maxVersions}" /></td></tr>
<tr><td class="text">{tr}Maximum number of records in listings{/tr}: </td><td><input size="5" type="text" name="maxRecords" value="{$maxRecords}" /></td></tr>
<!--<tr><td class="text">{tr}Wiki_Tiki_Title{/tr}: </td><td><input type="text" size="5" name="title" value="{$title}" /></td></tr>-->
<tr><td class="text">{tr}Theme{/tr}:</td><td>
        <select name="style">
        {section name=ix loop=$styles}
        <option value="{$styles[ix]}" {if $style eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
        {/section}
        </select></td></tr>
<tr><td class="text">{tr}Language{/tr}:</td><td>
        <select name="language">
        {section name=ix loop=$languages}
        <option value="{$languages[ix]}" {if $language eq $languages[ix]}selected="selected"{/if}>{$languages[ix]}</option>
        {/section}
        </select></td></tr>
<tr><td align="center" class="text" colspan="2"><input type="submit" name="prefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</div>
<div class="simplebox">
<table width="80%" cellpadding="0" cellspacing="0">
<tr>
  <td>
  <form method="post" action="tiki-admin.php">
    <table>
    <tr><td>{tr}Change admin password{/tr}:</td><td><input type="password" name="adminpass" /></td></tr>
    <tr><td>{tr}Again{/tr}:</td><td><input type="password" name="again" /></td></tr>
    <tr><td>&nbsp;</td><td><input type="submit" name="newadminpass" value="{tr}change{/tr}" /></td></tr>
    </table>
  </form>
  </td>
</tr>
</table>
</div>
<form>
</div>
</div>

<div class="cbox">
<div class="cbox-title">Wiki settings</div>
<div class="cbox-data">
    <table width="100%">
    <tr><td width="60%" valign="top">
    <div class="simplebox">
    {tr}Dumps{/tr}:<br/>
    <a class="link" href="tiki-admin.php?dump=1">{tr}Generate dump{/tr}</a><br/>
    <a class="link" href="dump/new.tar">{tr}Download last dump{/tr}</a>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php" method="post">
    {tr}Create a tag for the current wiki{/tr}<br/>
    {tr}Tag Name{/tr}<input  maxlength="20" size="10" type="text" name="tagname"/><br/>
    <input type="submit" name="createtag" value="{tr}create{/tr}"/>
    </form>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php" method="post">
    {tr}Restore the wiki{/tr}<br/>
    {tr}Tag Name{/tr}: <select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select><br/>
    <input type="submit" name="restoretag" value="{tr}restore{/tr}"/>          
    </form>
    </div>
    
    <div class="simplebox">
    <form action="tiki-admin.php" method="post">
    {tr}Remove a tag{tr}<br/>
    {tr}Tag Name{/tr}<select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select><br/>
    <input type="submit" name="removetag" value="{tr}remove{/tr}"/>          
    </form>
    </div>    
    <td width="40%" valign="top">
    <div class="simplebox">
    {tr}Wiki Features{/tr}:<br/>
    <form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="text">{tr}Sandbox{/tr}:</td><td><input type="checkbox" name="feature_sandbox" {if $feature_sandbox eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}Last changes{/tr}:</td><td><input type="checkbox" name="feature_lastChanges" {if $feature_lastChanges eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}Dump{/tr}:</td><td><input type="checkbox" name="feature_dump" {if $feature_dump eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}Ranking{/tr}:</td><td><input type="checkbox" name="feature_ranking" {if $feature_ranking eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}History{/tr}:</td><td><input type="checkbox" name="feature_history" {if $feature_history eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}List pages{/tr}:</td><td><input type="checkbox" name="feature_listPages" {if $feature_listPages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}Backlinks{/tr}:</td><td><input type="checkbox" name="feature_backlinks" {if $feature_backlinks eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}Like pages{/tr}:</td><td><input type="checkbox" name="feature_likePages" {if $feature_likePages eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="text">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_wiki_rankings" {if $feature_wiki_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td align="center" class="text" colspan="2"><input type="submit" name="wikifeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
    </form>
    </div>
    </td></tr>
    </table>
</div>
</div>

<div class="cbox">
<div class="cbox-title">Image galleries</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td>{tr}Home Gallery (main gallery){/tr}</td><td>
<select name="homeGallery">
{section name=ix loop=$galleries}
<option value="{$galleries[ix].galleryId}" {if $galleries[ix].galleryId eq $home_gallery}selected="selected"{/if}>{$galleries[ix].name|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" class="text" colspan="2"><input type="submit" name="galset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
<div class="simplebox">
{tr}Galleries features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="text">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_gal_rankings" {if $feature_gal_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td align="center" class="text" colspan="2"><input type="submit" name="galfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>
</div>
</div>

<div class="cbox">
<div class="cbox-title">CMS settings</div>
<div class="cbox-data">
<div class="simplebox">
{tr}CMS features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="text">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_cms_rankings" {if $feature_cms_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td align="center" class="text" colspan="2"><input type="submit" name="cmsfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>
<div class="simplebox">
<form method="post" action="tiki-admin.php">
<table>
  <tr><td class="text">{tr}Maximum number of articles in home{/tr}: </td><td><input size="5" type="text" name="maxArticles" value="{$maxArticles}" /></td></tr>
  <tr><td align="center" colspan="2"><input type="submit" name="cmsprefs" value="{tr}Change preferences{/tr}" /></td></tr>
</table>
</form>
</div>
</div>
</div>

<div class="cbox">
<div class="cbox-title">Blog settings</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php" method="post">
<table width="100%">
<tr><td>{tr}Home Blog (main blog){/tr}</td><td>
<select name="homeBlog">
{section name=ix loop=$blogs}
<option value="{$blogs[ix].blogId}" {if $blogs[ix].blogId eq $home_blog}selected="selected"{/if}>{$blogs[ix].title|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" class="text" colspan="2"><input type="submit" name="blogset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>
<div class="simplebox">
{tr}Blog features{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table width="100%">
    <tr><td class="text">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_blog_rankings" {if $feature_blog_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td align="center" class="text" colspan="2"><input type="submit" name="blogfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>
</div>
</div>





