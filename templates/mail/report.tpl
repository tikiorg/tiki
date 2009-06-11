Report for {$report_user}.<!--{$report_interval}-Report from {$report_date};-->
Last Report sent on {$report_last_report_date}.
<!--Total changes: {$report_total_changes};-->
<u>Changes in detail:</u>
{$report_body}

{if $mail_contributions}{tr}Contribution{/tr}: {$mail_contributions}{/if}