<div class="cbox">
<div class="cbox-title">
  {tr}{$crumbs[$crumb]->description}{/tr}
  {help crumb=$crumbs[$crumb]}
</div>
<div class="cbox-data">
        <form action="tiki-admin.php?page=metatags" method="post">
        <table class="admin">
        <tr><td class="form">{tr}<b>Item</b>{/tr}</td>
            <td class="form">{tr}<b>Value</b>{/tr}</td>
        </tr>
        <tr><td class="form">{tr}Meta Keywords{/tr}:</td><td><input type="text" name="metatag_keywords" value="{$metatag_keywords}" size="50" /></td></tr>
        <tr><td class="form">{tr}Meta Description{/tr}:</td><td><input type="text" name="metatag_description" value="{$metatag_description}" size="50" /></td></tr>
        <tr><td class="form">{tr}Meta Author{/tr}:</td><td><input type="text" name="metatag_author" value="{$metatag_author}" size="50" /></td></tr>
        <tr><td class="heading" colspan="3" align="center">{tr}Geourl{/tr}<a target="_blank" href="http://www.geourl.org/"><img src="img/icons/help.gif" border="0" height="16" width="16" alt="{tr}help{/tr}" /></a></td></tr>
        <tr><td class="form">{tr}geo.position{/tr}:</td><td><input type="text" name="metatag_geoposition" value="{$metatag_geoposition}" size="50" /></td></tr>
        <tr><td class="form">{tr}geo.region{/tr}:</td><td><input type="text" name="metatag_georegion" value="{$metatag_georegion}" size="50" /></td></tr>
        <tr><td class="form">{tr}geo.placename{/tr}:</td><td><input type="text" name="metatag_geoplacename" value="{$metatag_geoplacename}" size="50" /></td></tr>
        <tr><td class="heading" colspan="3" align="center">{tr}Robots{/tr}</td></tr>
        <tr><td class="form">{tr}meta robots{/tr}:</td><td><input type="text" name="metatag_robots" value="{$metatag_robots}" size="50" /></td></tr>
        <tr><td class="form">{tr}revisit after{/tr}:</td><td><input type="text" name="metatag_revisitafter" value="{$metatag_revisitafter}" size="50" /></td></tr>
        <tr><td class="form" colspan="3">&nbsp;</td></tr>
        <tr><td colspan="3" class="button"><input type="submit" name="metatags" value="{tr}Change settings{/tr}" /></td></tr>
        </table>
        </form>
</div>
</div>
