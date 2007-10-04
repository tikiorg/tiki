{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/notheme/tiki-top_bar.tpl,v 1.4 2007-10-04 22:17:49 nyloth Exp $ *}

{if $prefs.feature_calendar eq 'y' and $tiki_p_view_calendar eq 'y'}
  <a href='tiki-calendar.php' class='linkmenu'>
    {$smarty.now|tiki_short_datetime}
  </a>
{else}
  {$smarty.now|tiki_short_datetime}
{/if}
&nbsp;
{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
//&nbsp;
<a class="separator" href="javascript:toggle('debugconsole');">
  <small>Dbg</small>
</a>
{/if}
