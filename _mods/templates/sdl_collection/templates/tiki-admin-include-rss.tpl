<div class="cbox">
<div class="cbox-title">{tr}RSS Feeds{/tr}</div>
<div class="cbox-data">
        <form action="tiki-admin.php?page=rss" method="post">
        <table class="admin">
        <tr><td class="form">{tr}<b>Feed</b>{/tr}</td>
            <td class="form">{tr}<b>Enable/Disable</b>{/tr}</td>
            <td class="form">{tr}<b>Max number of items</b>{/tr}</td>
        </tr>
        <tr><td class="form">{tr}Feed for Articles{/tr}:</td><td><input type="checkbox" name="rss_articles" {if $rss_articles eq 'y'}checked="checked"{/if}/></td><td class="form"><input type="text" name="max_rss_articles" size="5" value="{$max_rss_articles|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Weblogs{/tr}:</td><td><input type="checkbox" name="rss_blogs" {if $rss_blogs eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_blogs" size="5" value="{$max_rss_blogs|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Image Galleries{/tr}:</td><td><input type="checkbox" name="rss_image_galleries" {if $rss_image_galleries eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_image_galleries" size="5" value="{$max_rss_image_galleries|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for File Galleries{/tr}:</td><td><input type="checkbox" name="rss_file_galleries" {if $rss_file_galleries eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_file_galleries" size="5" value="{$max_rss_file_galleries|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for the Wiki{/tr}:</td><td><input type="checkbox" name="rss_wiki" {if $rss_wiki eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_wiki" size="5" value="{$max_rss_wiki|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Individual Image Galleries{/tr}:</td><td><input type="checkbox" name="rss_image_gallery" {if $rss_image_gallery eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_image_gallery" size="5" value="{$max_rss_image_gallery|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Individual File Galleries{/tr}:</td><td><input type="checkbox" name="rss_file_gallery" {if $rss_file_gallery eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_file_gallery" size="5" value="{$max_rss_file_gallery|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Individual Weblogs{/tr}:</td><td><input type="checkbox" name="rss_blog" {if $rss_blog eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_blog" size="5" value="{$max_rss_blog|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Forums{/tr}:</td><td><input type="checkbox" name="rss_forums" {if $rss_forums eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_forums" size="5" value="{$max_rss_forums|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Individual Forums{/tr}:</td><td><input type="checkbox" name="rss_forum" {if $rss_forum eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_forum" size="5" value="{$max_rss_forum|escape}" /></td></tr>
        <tr><td class="form">{tr}Feed for Mapfiles{/tr}:</td><td><input type="checkbox" name="rss_mapfiles" {if $rss_mapfiles eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_mapfiles" size="5" value="{$max_rss_mapfiles|escape}" /></td></tr>

        <tr><td class="form" colspan="3">&nbsp;</td></tr>
        <tr><td class="form">{tr}Default RDF version{/tr}:</td><td class="form"><input type="text" name="rssfeed_default_version" size="1" value="{$rssfeed_default_version|escape}" />.0</td><td class="form">{tr}Specification{/tr} <a href="http://www.w3.org/TR/rdf-schema/" class="tikihelp" title="{tr}Specification{/tr}: RDF 1.0">RDF 1.0</a>, <a href="http://blogs.law.harvard.edu/tech/rss" class="tikihelp" title="{tr}Specification{/tr}: RDF 2.0">RDF 2.0</a></td></tr>
        <tr><td class="form">{tr}Append CSS file to feed urls{/tr}:</td><td class="form"><input type="checkbox" name="rssfeed_css" {if $rssfeed_css eq 'y'}checked="checked"{/if}/></td></tr>
        <tr><td class="form"><a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Language{/tr}</a>:</td><td class="form"><input type="text" name="rssfeed_language" size="10" length="40" value="{$rssfeed_language|escape}" /></td></tr>
        <tr><td class="form" colspan="3">&nbsp;</td></tr>

        <tr><td class="form"><a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Publisher{/tr}</a>: (RDF 1.0)</td><td colspan="2"><input type="text" name="rssfeed_publisher" size="50" value="{$rssfeed_publisher|escape}" /></td></tr>
        <tr><td class="form"><a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Creator{/tr}</a>: (RDF 1.0)</td><td colspan="2"><input type="text" name="rssfeed_creator" size="50" value="{$rssfeed_creator|escape}" /></td></tr>
        <tr><td class="form"><a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Editor{/tr}</a>: (RDF 2.0)</td><td colspan="2"><input type="text" name="rssfeed_editor" size="50" value="{$rssfeed_editor|escape}" /></td></tr>
        <tr><td class="form"><a href="http://blogs.law.harvard.edu/tech/rss#optionalChannelElements" target="tikihelp" class="tikihelp" title="{tr}Documentation{/tr}: RDF">{tr}Webmaster{/tr}</a>: (RDF 2.0)</td><td colspan="2"><input type="text" name="rssfeed_webmaster" size="50" value="{$rssfeed_webmaster|escape}" /></td></tr>
       
        <tr><td colspan="3" class="button"><input type="submit" name="rss" value="{tr}Change Preferences{/tr}" /></td></tr>
        </table>
        </form>
</div>
</div>
