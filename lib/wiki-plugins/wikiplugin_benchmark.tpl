{tabset name="wiki_benchmark"}
{tab name="General Info"}
	<div class="row" style="font-weight: bold;"><div class="col-md-2">Iteration</div>
		<div class="col-md-2">Seconds</div>
		<div class="col-md-2">Microseconds</div>
		<div class="col-md-2">Memory in Bytes</div>
		<div class="col-md-2">Real Memory</div>
	</div>

	<div class="row"><div class="col-md-2">1 - {$times}</div>
		<div class="col-md-2">{$time}</div>
		<div class="col-md-2">{$timeMicro}</div>
		<div class="col-md-2">{$memory}</div>
		<div class="col-md-2">{$memoryReal}</div>
	</div>
	{if isset($iterations)}
	<div class="row"><div class="col-md-2">Average</div>
		<div class="col-md-2">{$timeA}</div>
		<div class="col-md-2">{$timeAMicro}</div>
		<div class="col-md-2">{$memoryA}</div>
		<div class="col-md-2">{$memoryRealA}</div>
	</div>
	{/if}
{/tab}
{if isset($iterations)}
	{tab name="Iteration Details"}
		<div class="row"><div class="col-md-2">Iteration</div><div class="col-md-2">Microsecs</div><div class="col-md-2">Memory</div><div class="col-md-2">Real Memory</div></div>

	{foreach $iterations['mem'] as $times => $mem}
		<div class="row"><div class="col-md-2">{($times +1)}</div><div class="col-md-2">{$iterations['time'][$times]}</div><div class="col-md-2">{$iterations['mem'][$times]}</div><div class="col-md-2">{$iterations['memR'][$times]}</div></div>
	{/foreach}
	{/tab}
{/if}
	<hr>
{/tabset}