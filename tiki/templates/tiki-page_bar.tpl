<hr/>
<div id="page-bar">
<table>
<tr>
{if !$lock}
{if $tiki_p_edit eq 'y' or $page eq 'SandBox'}
{if $beingEdited eq 'y'}
<td><div class="button2" ><a title="{$semUser}" style="background: #FFAAAA;" href="tiki-editpage.php?page={$page}" class="linkbut">{tr}edit{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-editpage.php?page={$page}" class="linkbut">{tr}edit{/tr}</a></div></td>
{/if}
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_remove eq 'y'}
<td><div class="button2"><a href="tiki-removepage.php?page={$page}&amp;version=last" class="linkbut">{tr}remove{/tr}</a></div></td>
{/if}
{if $tiki_p_rename eq 'y'}
<td><div class="button2"><a href="tiki-rename_page.php?page={$page}" class="linkbut">{tr}rename{/tr}</a></div></td>
{/if}
{/if}
{if $page ne 'SandBox'}
{if $tiki_p_admin_wiki eq 'y' or ($user and ($user eq $page_user) and ($tiki_p_lock eq 'y') and ($feature_wiki_usrlock eq 'y'))}
{if $lock}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;action=unlock" class="linkbut">{tr}unlock{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;action=lock" class="linkbut">{tr}lock{/tr}</a></div></td>
{/if}
{/if}
{if $tiki_p_admin_wiki eq 'y'}
<td><div class="button2"><a href="tiki-pagepermissions.php?page={$page}" class="linkbut">{tr}perms{/tr}</a></div></td>
{/if}
{/if}

{if $page ne 'SandBox'}
{if $feature_history eq 'y'}
<td><div class="button2"><a href="tiki-pagehistory.php?page={$page}" class="linkbut">{tr}history{/tr}</a></div></td>
{/if}
{/if}

{if $feature_likePages eq 'y'}
<td><div class="button2"><a href="tiki-likepages.php?page={$page}" class="linkbut">{tr}similar{/tr}</a></div></td>
{/if}
{if $feature_wiki_undo eq 'y' and $canundo eq 'y'}
<td><div class="button2"><a href="tiki-index.php?page={$page}&amp;undo=1" class="linkbut">{tr}undo{/tr}</a></div></td>
{/if}
{if $show_slideshow eq 'y'}
<td><div class="button2"><a href="tiki-slideshow.php?page={$page}" class="linkbut">{tr}slides{/tr}</a></div></td>
{elseif $structure eq 'y'}
<td><div class="button2"><a href="tiki-slideshow2.php?page={$page}" class="linkbut">{tr}slides{/tr}</a></div></td>
{/if}
<td><div class="button2"><a href="tiki-export_wiki_pages.php?page={$page}" class="linkbut">{tr}export{/tr}</a></div></td>
{if $feature_wiki_discuss eq 'y'}
<td><div class="button2"><a href="tiki-view_forum.php?forumId={$wiki_forum_id}&comments_postComment=post&comments_title={$page}&comments_data={"Use this thread to discuss the [tiki-index.php?page="}{$page}{"|"}{$page}{"] page."}&comment_topictype=n" class="linkbut">{tr}discuss{/tr}</a></div></td>
{/if}

{if $edit_page eq 'y'}
<td><div class="button2"><a href="javascript:flip('edithelpzone');" class="linkbut">{tr}Wiki quick help{/tr}</a></div>
</td>
{/if}

{if $show_page eq 'y'}
{if $comments_show eq 'y'}
<td><div class="button2"><a href="javascript:flip('comzoneopen');" class="linkbut">{tr}comments{/tr}</a></div></td>
{else}
<td><div class="button2"><a href="javascript:flip('comzone');" class="linkbut">{tr}comments{/tr}</a></div></td>
{/if}
{/if}

{if $feature_wiki_attachments eq 'y' and $show_page eq 'y'}
<td><div class="button2"><a href="javascript:flip('attzone');" class="linkbut">{tr}attachments{/tr}</a></div></td>
{/if}

</tr>
</table>
</div>
