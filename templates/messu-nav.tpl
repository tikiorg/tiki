{* $Id$ *}
<div class="navbar">
  <span class="button2">
    <a class="linkbut" href="messu-mailbox.php">{tr}Mailbox{/tr}</a>
  </span>
  <span class="button2">
    <a class="linkbut" href="messu-compose.php">{tr}Compose{/tr}</a>
  </span>

  {if $tiki_p_broadcast eq 'y'}
    <span class="button2">
      <a class="linkbut" href="messu-broadcast.php">{tr}Broadcast{/tr}</a>
    </span>
  {/if}

    <span class="button2">
      <a class="linkbut" href="messu-sent.php">{tr}Sent{/tr}</a>
    </span>
    <span class="button2">
      <a class="linkbut" href="messu-archive.php">{tr}Archive{/tr}</a>
    </span>

    {if $mess_archiveAfter>0}
      ({tr}Auto-archive age for read messages:{/tr} {$mess_archiveAfter} {tr}days{/tr})
    {/if}
</div>
