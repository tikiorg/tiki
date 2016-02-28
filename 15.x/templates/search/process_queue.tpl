{extends "layout_view.tpl"}

{block name="title"}
	{title}{$title}{/title}
{/block}

{block name="content"}
	<form method="post" action="{service controller=search action=process_queue}">
		{if !empty($stat)}
			{remarksbox type='feedback' title="{tr}Indexed{/tr}"}
				<ul>
					{foreach from=$stat key=what item=nb}
						<li>{$what|escape}: {$nb|escape}</li>
					{/foreach}
				</ul>
			{/remarksbox}
		{/if}
		<h5>{tr}Queue size:{/tr} {$queue_count|escape}</h5>

		<div class="form-group">
			<label for="batch" class="control-label">{tr}Batch Size{/tr}</label>
			<select name="batch" id="batch" class="form-control">
				{foreach [10, 20, 50, 100, 250] as $count}
					<option value="{$count|escape}" {if $count eq $batch}selected{/if} >{tr _0=$count}Process %0{/tr}</option>
				{/foreach}
			</select>
		</div>
		<div class="form-group submit">
			<input type="submit" class="btn btn-primary" value="{tr}Process Batch{/tr}">
		</div>
	</form>
{/block}
