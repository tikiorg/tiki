<a name="rss"></a>
{include file="tiki-admin-include-anchors-empty.tpl"}
<div class="cbox">
<div class="cbox-title">{tr}RSS feeds{/tr}</div>
<div class="cbox-data">
    <table>
    <tr>
      <td valign="top">
        <form action="tiki-admin.php" method="post">
        <table>
        <tr><td class="form">{tr}<b>Feed</b>{/tr}</td>
            <td class="form">{tr}<b>enable/disable</b>{/tr}</td>
            <td class="form">{tr}<b>Max number of items</b>{/tr}</td>
        </tr>
        <tr><td class="form">{tr}Feed for Articles{/tr}:</td><td><input type="checkbox" name="rss_articles" {if $rss_articles eq 'y'}checked="checked"{/if}/></td><td class="form"><input type="text" name="max_rss_articles" size="5" value="{$max_rss_articles}" /></td></tr>
        <tr><td class="form">{tr}Feed for Weblogs{/tr}:</td><td><input type="checkbox" name="rss_blogs" {if $rss_blogs eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_blogs" size="5" value="{$max_rss_blogs}" /></td></tr>
        <tr><td class="form">{tr}Feed for Image Galleries{/tr}:</td><td><input type="checkbox" name="rss_image_galleries" {if $rss_image_galleries eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_image_galleries" size="5" value="{$max_rss_image_galleries}" /></td></tr>
        <tr><td class="form">{tr}Feed for File Galleries{/tr}:</td><td><input type="checkbox" name="rss_file_galleries" {if $rss_file_galleries eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_file_galleries" size="5" value="{$max_rss_file_galleries}" /></td></tr>
        <tr><td class="form">{tr}Feed for the Wiki{/tr}:</td><td><input type="checkbox" name="rss_wiki" {if $rss_wiki eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_wiki" size="5" value="{$max_rss_wiki}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual Image Galleries{/tr}:</td><td><input type="checkbox" name="rss_image_gallery" {if $rss_image_gallery eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_image_gallery" size="5" value="{$max_rss_image_gallery}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual File Galleries{/tr}:</td><td><input type="checkbox" name="rss_file_gallery" {if $rss_file_gallery eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_file_gallery" size="5" value="{$max_rss_file_gallery}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual weblogs{/tr}:</td><td><input type="checkbox" name="rss_blog" {if $rss_blog eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_blog" size="5" value="{$max_rss_blog}" /></td></tr>
        <tr><td class="form">{tr}Feed for forums{/tr}:</td><td><input type="checkbox" name="rss_forums" {if $rss_forums eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_forums" size="5" value="{$max_rss_forums}" /></td></tr>
        <tr><td class="form">{tr}Feed for individual forums{/tr}:</td><td><input type="checkbox" name="rss_forum" {if $rss_forum eq 'y'}checked="checked"{/if}/></td><td><input type="text" name="max_rss_forum" size="5" value="{$max_rss_forum}" /></td></tr>
        
        <tr><td align="center" colspan="3"><input type="submit" name="rss" value="{tr}Set feeds{/tr}" /></td></tr>    
        </table>
        </form>
      </td>
    </tr>
    </table>
</div>
