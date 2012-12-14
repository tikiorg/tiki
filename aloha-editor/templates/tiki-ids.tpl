{* $Id$ *}
{title help="PHPIDS" admpage="security#content2"}Intrusion Detection{/title}

{remarksbox type='tip' title='Tip'}To test, if PHPIDS really catches intrusion attempts, you can use some simple XSS like <a href='?test=">XXX '>?test=">XXX </a> and SQL injection like <a href="?test=' OR 1=1--">"?test=' OR 1=1--"</a>. After having done this a couple of times you will be locked out of your tiki - then clear your browser's cookies!!{/remarksbox}
<table class="normal">
		<tr>
			<th>IP</th>
			<th>Date</th>
			<th>Impact</th>
			<th>Tags</th>
			<th>Attacked Parameters</th>
			<th>Request</th>
			<th>Attacked IP</th>
		</tr>
	{foreach $intrusions as $intrusion}
	<tr>
			<td>{$intrusion.ip}</td>
			<td>{$intrusion.date|tiki_short_datetime}</td>
			<td>{$intrusion.impact}</td>
			<td>{$intrusion.tags}</td>
			<td>{$intrusion.attackedParameters}</td>
			<td>{$intrusion.request}</td>
			<td>{$intrusion.serverIp}</td>
	</tr>
	{/foreach}
</table>
