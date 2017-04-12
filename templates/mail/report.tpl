{* $Id$ *}{tr _0=$report_site _1=$report_user}Report on %0 for %1{/tr}.{if $report_preferences.type eq 'html'}<br><br>{/if}

{tr _0=$report_last_report_date}Last {$prefs.mail_template_custom_text}Report sent on %0.{/tr}{if $report_preferences.type eq 'html'}<br><br>{/if}

{if $report_preferences.type eq 'html'}<u>{/if}{tr}Changes in detail:{/tr}{if $report_preferences.type eq 'html'}</u><br><br>{else}

-----------------------
{/if}

{$report_body}{if $report_preferences.type eq 'html'}<br><br>{/if}


{tr _0=$userWatchesUrl}You are receiving notification emails grouped in a periodic digest. To receive them individually when posted instead, change your preferences at %0{/tr}