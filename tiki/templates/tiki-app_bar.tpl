<div  id="tiki-menu">
  <table>
  <tr>
  <td><div class="button"><a href="tiki-index.php" class="linkbut">{tr}home{/tr}</a></div></td>
  {if $feature_lastChanges eq 'y'}
    <td><div class="button"><a href="tiki-lastchanges.php" class="linkbut">{tr}last changes{/tr}</a></div></td>
  {/if}
  {if $feature_dump eq 'y'}
    <td><div class="button"><a href="dump/new.tar" class="linkbut">{tr}dump{/tr}</a></div></td>
  {/if}
  {if $feature_ranking eq 'y'}
    <td><div class="button"><a href="tiki-ranking.php" class="linkbut">{tr}ranking{/tr}</a></div></td>
  {/if}
  {if $feature_listPages eq 'y'}
    <td><div class="button"><a href="tiki-listpages.php" class="linkbut">{tr}list_pages{/tr}</a></div></td>
  {/if}
  {if $feature_galleries eq 'y'}
    <td><div class="button"><a href="tiki-galleries.php" class="linkbut">{tr}my galleries{/tr}</a></div></td>
    {if $tiki_p_upload_images eq 'y'}
      <td><div class="button"><a href="tiki-upload_image.php" class="linkbut">{tr}upload image{/tr}</a></div></td>
    {/if}
  {/if}
  {if $tiki_p_admin eq 'y'}
    <td><div class="button"><a href="tiki-admin.php" class="linkbut">{tr}admin{/tr}</a></span>
    <td><div class="button"><a href="tiki-adminusers.php" class="linkbut">{tr}users{/tr}</a></div></td>
    <td><div class="button"><a href="tiki-admingroups.php" class="linkbut">{tr}groups{/tr}</a></div></td>
    <td><div class="button"><a href="tiki-list_cache.php" class="linkbut">{tr}cache{/tr}</a></div></td>
  {/if}
  </tr>
  </table>
</div>   