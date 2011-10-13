{tr _0=$report_user}Report for %0{/tr}.

{tr _0=$report_last_report_date}Last Report sent on %0.{/tr}


{if $report_preferences.type eq 'html'}<u>{/if}{tr}Changes in detail:{/tr}{if $report_preferences.type eq 'html'}</u>{else}

-----------------------
{/if}

{$report_body}

{if $mail_contributions}{tr}Contribution:{/tr} {$mail_contributions}{/if}
