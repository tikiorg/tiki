{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Most Active blogs{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopActiveBlogs}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-view_blog.php?blogId={$modTopActiveBlogs[ix].blogId}">{$modTopActiveBlogs[ix].title}</a></div>
{/section}
</div>
</div>
{/if}