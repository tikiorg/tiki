{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Most visited blogs{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modTopVisitedBlogs}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-view_blog.php?blogId={$modTopVisitedBlogs[ix].blogId}">{$modTopVisitedBlogs[ix].title}</a></div>
{/section}
</div>
</div>
{/if}