{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/matrix/tiki-top_bar.tpl,v 1.1 2003-07-13 00:03:47 zaufi Exp $ *}

{if $feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  <a href='tiki-calendar.php' class='linkmenu'>
    {$smarty.now|tiki_short_datetime}
  </a>
{else}
  {$smarty.now|tiki_short_datetime}
{/if}
&nbsp;
<a class="separator" href="javascript:toggle('debugconsole');">
  <small>Dbg</small>
</a>
