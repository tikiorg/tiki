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
<form action="tiki-admin_poll_options.php" method="post" class="form-horizontal">
	<input type="hidden" name="optionId" value="{$optionId|escape}">
	<input type="hidden" name="pollId" value="{$pollId|escape}">

	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Option{/tr}</label>
		<div class="col-sm-7">
			<input type="text" name="title" value="{$title|escape}" maxlength="40" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Position{/tr}</label>
		<div class="col-sm-7">
			<input type="text" name="position" value="{$position|escape}" maxlength="4" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
			<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
		</div>
	</div>
</form>
<br>
{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}
<h2>{tr}Poll options{/tr}</h2>
<div align="center">
	<table class="table table-striped table-hover">
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
							{$libeg}<a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;optionId={$channels[user].optionId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-admin_poll_options.php?pollId={$pollId}&amp;remove={$channels[user].optionId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.poll_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.poll_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=4}
		{/section}
	</table>
</div>
