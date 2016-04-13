{* $Id$ *}
{title help="Token Access"}{tr}Admin Tokens{/tr}{/title}

{tabset name="tabs_admtokens"}
	{tab name="{tr}List tokens{/tr}"}
		<h2>{tr}List tokens{/tr}</h2>
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tr>
					<th></th>
					<th>{tr}Id{/tr}</th>
					<th>{tr}Entry{/tr}</th>
					<th>{tr}Email{/tr}</th>
					<th>{tr}Timeout{/tr}</th>
					<th>{tr}Token{/tr}</th>
					<th>{tr}Creation{/tr}</th>
					<th>{tr}Hits{/tr}</th>
					<th>{tr}Max hits{/tr}</th>
					<th>{tr}Parameters{/tr}</th>
					<th>{tr}Groups{/tr}</th>
					<th>{tr}Create Temp User{/tr}</th>
					<th>{tr}Temp User Prefix{/tr}</th>
				</tr>

				{foreach $tokens as $token}
					<tr>
						<td>
							{self_link tokenId=$token.tokenId action='delete' _menu_text='n' _menu_icon='y' _icon_name='remove' _title='{tr}Delete{/tr}'}
							{/self_link}
						</td>
						<td>{$token.tokenId}</td>
						<td>{$token.entry}</td>
						<td>{$token.email}</td>
						<td>{if $token.expires}{$token.expires|tiki_short_datetime}{else}{tr}none{/tr}{/if}</td>
						<td style="max-width: 6em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{$token.token}">
							{$token.token}
						</td>
						<td>{$token.creation|tiki_short_datetime}</td>
						<td>{$token.hits}</td>
						<td>{$token.maxhits}</td>
						<td>
							{foreach $token.parameters as $key => $value}
								{$key}={$value}<br>
							{/foreach}
						</td>
						<td>{$token.groups}</td>
						<td>{$token.createUser}</td>
						<td>{$token.userPrefix}</td>
					</tr>
				{foreachelse}
					{norecords _colspan=10}
				{/foreach}
			</table>
		</div>
	{/tab}
	{tab name="{tr}Add new token{/tr}"}
		<h2>{tr}Add new token{/tr}</h2>

		{if $tokenCreated}
			{remarksbox type="note" title="{tr}Note{/tr}"}
				{tr}Token successfully created.{/tr}
			{/remarksbox}
		{/if}

		<form action="tiki-admin_tokens.php" method="post" class="form-horizontal">
			<input type="hidden" name="action" value="add">
            <div class="form-group">
                <label class="col-sm-4 control-label">{tr}Full URL{/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="text" id='entry' name='entry' class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{tr}Timeout in seconds (-1 for unlimited){/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="text" id='timeout' name='timeout' class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{tr}Maximum number of hits (-1 for unlimited){/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="text" id='maxhits' name='maxhits' class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{tr}Groups (separated by comma){/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="text" id='groups' name='groups' class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label"></label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="submit" class="btn btn-default btn-sm" value="{tr}Add{/tr}">
                </div>
            </div>
		</form>
	{/tab}
{/tabset}
