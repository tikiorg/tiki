{if $feature_galleries eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Created blogs{/tr}
</div>
<div class="box-data">
{section name=ix loop=$modLastCreatedBlogs}
<div class="button">{$smarty.section.ix.index_next})&nbsp;<a class="linkbut" href="tiki-view_blog.php?blogId={$modLastCreatedBlogs[ix].blogId}">{$modLastCreatedBlogs[ix].title}</a></div>
{/section}
</div>
</div>
{/if}