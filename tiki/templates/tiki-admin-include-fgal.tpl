<a name="fgal"></a>
[ <a href="#features" class="link">{tr}feat{/tr}</a> |
<a href="#general" class="link">{tr}gral{/tr}</a> |
<a href="#login" class="link">{tr}login{/tr}</a> |
<a href="#wiki" class="link">{tr}wiki{/tr}</a> |
<a href="#gal" class="link">{tr}img gls{/tr}</a> |
<a href="#fgal" class="link">{tr}file gls{/tr}</a> |
<a href="#blogs" class="link">{tr}blogs{/tr}</a> |
<a href="#forums" class="link">{tr}frms{/tr}</a> |
<a href="#polls" class="link">{tr}polls{/tr}</a> |
<a href="#rss" class="link">{tr}rss{/tr}</a> |
<a href="#cms" class="link">{tr}cms{/tr}</a> |
<a href="#faqs" class="link">{tr}FAQs{/tr}</a> |
<a href="#trackers" class="link">{tr}trckrs{/tr}</a> |
<a href="#webmail" class="link">{tr}webmail{/tr}</a> |
<a href="#directory" class="link">{tr}directory{/tr}</a> |
<a href="#userfiles" class="link">{tr}userfiles{/tr}</a>
]
<div class="cbox">
<div class="cbox-title">{tr}File galleries{/tr}</div>
<div class="cbox-data">
<div class="simplebox">
<form action="tiki-admin.php#fgal" method="post">
<table width="100%">
<tr><td class="form">{tr}Home Gallery (main gallery){/tr}</td><td>
<select name="homeFileGallery">
{section name=ix loop=$file_galleries}
<option value="{$file_galleries[ix].galleryId}" {if $file_galleries[ix].galleryId eq $home_file_gallery}selected="selected"{/if}>{$file_galleries[ix].name|truncate:20:"(...)":true}</option>
{/section}
</select>
</td></tr>
<tr><td align="center" colspan="2"><input type="submit" name="filegalset" value="{tr}Set prefs{/tr}" /></td></tr>    
</table>
</form>
</div>


<div class="simplebox">
{tr}Galleries features{/tr}<br/>
<form action="tiki-admin.php#fgal" method="post">
    <table width="100%">
    <tr><td class="form">{tr}Rankings{/tr}:</td><td><input type="checkbox" name="feature_file_galleries_rankings" {if $feature_file_galleries_rankings eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_file_galleries_comments" {if $feature_file_galleries_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use database to store files{/tr}:</td><td><input type="radio" name="fgal_use_db" value="y" {if $fgal_use_db eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Use a directory to store files{/tr}:</td><td class="form"><input type="radio" name="fgal_use_db" value="n" {if $fgal_use_db eq 'n'}checked="checked"{/if}/> {tr}Directory path{/tr}:<input type="text" name="fgal_use_dir" value="{$fgal_use_dir}" /> </tr>
    <tr><td class="form">{tr}Uploaded filenames must match regex{/tr}:</td><td><input type="text" name="fgal_match_regex" value="{$fgal_match_regex}"/></td></tr>
    <tr><td class="form">{tr}Uploaded filenames cannot match regex{/tr}:</td><td><input type="text" name="fgal_nmatch_regex" value="{$fgal_nmatch_regex}"/>
    <a class="link" {popup sticky="true" trigger="onClick" caption="Storing files in a directory" text="If you decide to store files in a directory you must ensure that the user cannot access directly to the directory. You have two options to accomplish this:<br/><ul><li>Use a directory ourside your document root, make sure your php script can read and write to that directory</li><li>Use a directory inside the document root and use and .htaccess to prevent the user from listing the directory contents</li></ul>To configure the directory path use UNIX like paths for example files/ or c:/foo/files or /www/files/"}>{tr}please read{/tr}</a></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="filegalfeatures" value="{tr}Set features{/tr}" /></td></tr>    
    </table>
</form>
</div>

    <div class="simplebox">
    {tr}File galleries comments settings{/tr}
    <form method="post" action="tiki-admin.php#fgal">
    <table>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="file_galleries_comments_per_page" value="{$file_galleries_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="file_galleries_comments_default_ordering">
    <option value="commentDate_desc" {if $file_galleries_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $file_galleries_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="filegalcomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>


</div>
</div>
