    <div class="simplebox">
    {tr}Wiki History{/tr}
    <form action="tiki-admin.php" method="post">
    <table>
    <tr><td class="form">{tr}Maximum number of versions for history{/tr}: </td><td><input size="5" type="text" name="maxVersions" value="{$maxVersions}" /></td></tr>
    <tr><td class="form">{tr}Never delete versions younger than days{/tr}: </td><td><input size="5" type="text" name="keep_versions" value="{$keep_versions}" /></td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="wikisetprefs" value="{tr}Set{/tr}" /></td></tr>    
    </table>
    </form>
    </div>
