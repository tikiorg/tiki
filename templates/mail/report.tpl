Report for {$report_user}.<br><!--{$report_interval}-Report from {$report_date};-->
Last Report sent on {$report_last_report_date}.<br><br>
<!--Total changes: {$report_total_changes};-->
<u>Changes in detail:</u><br>
{$report_body}

{if $mail_contributions}{tr}Contribution{/tr}: {$mail_contributions}{/if}