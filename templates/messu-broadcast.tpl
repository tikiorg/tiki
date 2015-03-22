{title help='Inter-User Messages' url='messu-broadcast.php' admpage="messages"}{tr}Broadcast message{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
{include file='messu-nav.tpl'}

{if $message}
	<div class="alert alert-warning">
		{if $preview eq '1'}
			{icon name='help' style="vertical-align:middle" alt="{tr}Confirmation{/tr}"}
		{elseif $sent eq '1'}
			{icon name='ok' alt="{tr}OK{/tr}" style="vertical-align:middle;"}
		{else}
			{icon name='error' style="vertical-align:middle" alt="{tr}Error{/tr}"}
		{/if}
		{$message}
		{if $preview eq '1'}
			<br>
			<form method="post">
				<input type="hidden" name="groupbr" value="{$groupbr|escape}">
				<input type="hidden" name="priority" value="{$priority|escape}">
				<input type="hidden" name="replyto_hash" value="{$replyto_hash|escape}">
				<input type="hidden" name="subject" value="{$subject|escape}">
				<input type="hidden" name="body" value="{$body|escape}">
				<input type="submit" class="btn btn-default btn-sm" name="send" value="{tr}Please Confirm{/tr}">
			</form>
		{/if}
	</div>
{/if}

{if $sent ne '1' and $preview ne '1'}
	<form class="form-horizontal" role="form" action="messu-broadcast.php" method="post">
		<div class="form-group">
			<label class="col-sm-2 control-label" for="broadcast-group">{tr}Group{/tr}</label>
			<div class="col-sm-10">
				<select name="groupbr" id="broadcast-group" class="form-control">
					<option value=""{if $groupbr eq ''} selected="selected"{/if} />
					{if $tiki_p_broadcast_all eq 'y'}
						<option value="all"{if $groupbr eq 'All'} selected="selected"{/if}>{tr}All users{/tr}</option>
					{/if}
					{foreach item=groupName from=$groups}
						<option value="{$groupName|escape}"{if $groupbr eq $groupName} selected="selected"{/if}>{$groupName|escape}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="broadcast-priority">{tr}Priority{/tr}</label>
			<div class="col-sm-10">
				<select name="priority" id="broadcast-priority" class="form-control">
					<option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
					<option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
					<option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
					<option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
					<option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
				</select>
				<input type="hidden" name="replyto_hash" value="{$replyto_hash|escape}">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="broadcast-subject">{tr}Subject{/tr}</label>
			<div class="col-sm-10">
				<input type="text" name="subject" class="form-control" id="broadcast-subject" value="{$subject|escape}" maxlength="255">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label" for="broadcast-body">{tr}Body{/tr}</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="20" id="broadcast-body" name="body">{$body|escape}</textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-10 col-sm-push-2">
				<input type="submit" class="btn btn-primary" name="preview" value="{tr}Send{/tr}">
			</div>
		</div>
	</form>
{/if}
