<div class="box">
<div class="box-title">
{tr}Featured links{/tr}
</div>
<div class="box-data">
{section name=ix loop=$featuredLinks}
<div class="button">{$smarty.section.ix.index_next})<a class="linkbut" href="tiki-featured_link.php?url={$featuredLinks[ix].url}">{$featuredLinks[ix].title}</a></div>
{/section}
</div>
</div>