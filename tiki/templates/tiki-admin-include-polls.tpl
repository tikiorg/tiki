<div class="cbox">
<div class="cbox-title">{tr}Poll settings{/tr}</div>
<div class="cbox-data">
    <div class="simplebox">
    {tr}Poll comments settings{/tr}
    <form method="post" action="tiki-admin.php?page=polls">
    <table>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_poll_comments" {if $feature_poll_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="poll_comments_per_page" value="{$poll_comments_per_page|escape}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="poll_comments_default_ordering">
    <option value="commentDate_desc" {if $poll_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $poll_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="pollprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
    </form>
    </div>
</div>
</div>


