{if $feature_blogs eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Modified blogs{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modLastModifiedBlogs}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-view_blog.php?blogId={$modLastModifiedBlogs[ix].blogId}">{$modLastModifiedBlogs[ix].title}</a></div>
{/section}
</div>
</div>
{/if}