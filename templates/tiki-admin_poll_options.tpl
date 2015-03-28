{title url="tiki-admin_poll_options.php?pollId=$pollId"}{tr}Admin Polls:{/tr} {$menu_info.title}{/title}

<div class="t_navbar btn-group form-group">
	{button href="tiki-admin_polls.php" class="btn btn-default" _icon_name="list" _text="{tr}List{/tr}"}
	{button href="tiki-admin_polls.php?pollId=$pollId" class="btn btn-default" _icon_name="edit" _text="{tr}Edit{/tr}"}
</div>

<h2>{tr}Preview poll{/tr}</h2>
<div align="center">
	<div style="text-align:left;width:130px;" class="panel panel-default">
		<div class="panel-heading">{$menu_info.name}</div>
		<div class="panel-body">
			{include file='tiki-poll.tpl'}
		</div>
	</div>
</div>

<br>

<h2>{if $optionId eq ''}{tr}Add poll option{/tr}{else}{tr}Edit poll option{/tr}{/if}</h2>
<form action="tiki-admin_poll_options.php" method="post">
	<input type="hidden" name="optionId" value="{$optionId|escape}">
	<input type="hidden" name="pollId" value="{$pollId|escape}">
	<table class="formcolor">
		<tr>
			<td>{tr}Option:{/tr}</td>
			<td>
				<input type="text" name="title" value="{$title|escape}" size="40">
			</td>
			<td>{tr}Position:{/tr}</td>
			<td>
				<input type="text" name="position" value="{$position|escape}" size="4">
			</td>
			<td colspan="2">
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			</td>
		</tr>
	</table>
</form>

<br>

<h2>{tr}Poll options{/tr}</h2>
<div align="center">
	<table class="table normal table-striped table-hover">
		<tr>
			<th>{tr}Position{/tr}</th>
			<th>{tr}Title{/tr}</th>
			<th>{tr}Votes{/tr}</th>
			<th></th>
		</tr>

		{section name=user loop=$channels}
			<tr>
				<td class="id">{$channels[user].position}</td>
				<td class="text">{$channels[user].title|escape}</td>
				<td class="integer">{$channels[user].votes}</td>
				<td class="action">
					{capture name=poll_actions}
						{strip}
							<a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;optionId={$channels[user].optionId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>
							<a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;remove={$channels[user].optionId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>
						{/strip}
					{/capture}
					<a class="tips"
						title="{tr}Actions{/tr}"
						href="#" {popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.poll_actions|escape:"javascript"|escape:"html"}
						style="padding:0; margin:0; border:0"
							>
						{icon name='wrench'}
					</a>
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=4}
		{/section}
	</table>
</div>
