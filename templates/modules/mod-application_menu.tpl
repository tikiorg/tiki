<div class="box">
<div class="box-title">
{tr}Menu{/tr}
</div>
<div class="box-data">
<div class="button">&nbsp;<a href="tiki-index.php" class="linkbut">{tr}home{/tr}</a></div>
{if $feature_lastChanges eq 'y'}
  <div class="button">&nbsp;<a href="tiki-lastchanges.php" class="linkbut">{tr}last changes{/tr}</a></div>
{/if}
{if $feature_dump eq 'y'}
  <div class="button">&nbsp;<a href="dump/new.tar" class="linkbut">{tr}dump{/tr}</a></div>
{/if}
{if $feature_ranking eq 'y'}
  <div class="button">&nbsp;<a href="tiki-ranking.php" class="linkbut">{tr}ranking{/tr}</a></div>
{/if}
{if $feature_listPages eq 'y'}
  <div class="button">&nbsp;<a href="tiki-listpages.php" class="linkbut">{tr}list_pages{/tr}</a></div>
{/if}
{if $feature_galleries eq 'y'}
  <div class="button">&nbsp;<a href="tiki-galleries.php" class="linkbut">{tr}my galleries{/tr}</a></div>
  {if $tiki_p_upload_images eq 'y'}
    <div class="button">&nbsp;<a href="tiki-upload_image.php" class="linkbut">{tr}upload image{/tr}</a></div>
  {/if}
{/if}
</div>
</div>