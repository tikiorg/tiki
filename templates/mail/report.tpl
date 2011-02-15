{tr}Report for{/tr} {$report_user}.{if $report_preferences.type eq 'plain'}

{else}<br />{/if}
{tr}Last Report sent on{/tr} {$report_last_report_date}.{if $report_preferences.type eq 'plain'}


{else}<br /><br />{/if}
{if $report_preferences.type eq 'html'}<u>{/if}{tr}Changes in detail:{/tr}{if $report_preferences.type eq 'html'}</u><br />{else}

-----------------------

{/if}
{$report_body}

{if $mail_contributions}{tr}Contribution{/tr}: {$mail_contributions}{/if}