{if $feature_blogs eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Modified blogs{/tr}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastModifiedBlogs}
<tr><td  width="5%" class="module">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-view_blog.php?blogId={$modLastModifiedBlogs[ix].blogId}">{$modLastModifiedBlogs[ix].title}</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}