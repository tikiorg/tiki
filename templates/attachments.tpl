<div id=attzone>
{if $tiki_p_wiki_view_attachments eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}

{* It is allow to attach files or current user have admin rights *}
{if $tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y'}
<form enctype="multipart/form-data" action="tiki-index.php?page={$page}" method="post">
<table class="normal">
<tr>
 <td class="formcolor">
   {tr}Upload file{/tr}:<input type="hidden" name="MAX_FILE_SIZE" value="1000000000">&nbsp;
                        <input style="font-size:9px;" size="16 " name="userfile1" type="file">
   {tr}comment{/tr}:    <input style="font-size:9px;" type="text" name="attach_comment" maxlength="250"/>
                        <input style="font-size:9px;" type="submit" name="attach" value="{tr}attach{/tr}"/>
 </td>
</tr>
</table>
</form>
{/if}{* $tiki_p_wiki_attach_files eq 'y' or $tiki_p_wiki_admin_attachments eq 'y' *}

{/if}{* $tiki_p_wiki_view_attachments eq 'y' or $tiki_p_wiki_admin_attachments eq 'y' *}
</div>
