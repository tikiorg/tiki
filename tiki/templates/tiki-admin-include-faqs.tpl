<a name="faqs"></a>
{include file="tiki-admin-include-anchors-empty.tpl"}
<div class="cbox">
<div class="cbox-title">{tr}FAQs settings{/tr}</div>
<div class="cbox-data">



<div class="simplebox">
{tr}FAQ comments{/tr}<br/>
<form action="tiki-admin.php" method="post">
    <table>
    <tr><td class="form">{tr}Comments{/tr}:</td><td><input type="checkbox" name="feature_faq_comments" {if $feature_faq_comments eq 'y'}checked="checked"{/if}/></td></tr>
    <tr><td class="form">{tr}Default number of comments per page{/tr}: </td><td><input size="5" type="text" name="faq_comments_per_page" value="{$faq_comments_per_page}" /></td></tr>
    <tr><td class="form">{tr}Comments default ordering{/tr}
    </td><td>
    <select name="faq_comments_default_ordering">
    <option value="commentDate_desc" {if $faq_comments_default_ordering eq 'commentDate_dec'}selected="selected"{/if}>{tr}Date{/tr}</option>
    <option value="points_desc" {if $faq_comments_default_ordering eq 'points_desc'}selected="selected"{/if}>{tr}Points{/tr}</option>
    </select>
    </td></tr>
    <tr><td align="center" colspan="2"><input type="submit" name="faqcomprefs" value="{tr}Change preferences{/tr}" /></td></tr>
    </table>
</form>
</div>
</div>
</div>


