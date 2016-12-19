{* $Id$ *}

{if $tiki_p_admin eq 'y' and $prefs.feature_debug_console eq 'y'}
	<div class="debugconsole" id="debugconsole" style="{$debugconsole_style}">

		{* Command prompt form *}
		<form method="post" action="{$console_father|escape}">
			<b>{tr}Debugger console{/tr}</b>
			<span style="float: right">
				<a href='#' onclick="toggle('debugconsole');" title=":{tr}Close{/tr}" class="tips">
					{icon name='delete'}
				</a>
			</span>
			<table class="table">
				<tr>
					<td><small>{tr}Current URL:{/tr}</small></td>
					<td>{$console_father|escape}</td>
				</tr>
				<tr>
					<td>{tr}Command:{/tr}</td>
					<td><input type="text" name="command" class="form-control" value='{$command|escape:"html"}'></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" class="btn btn-default btn-sm" name="exec" value="{tr}exec{/tr}"> &nbsp;&nbsp;&nbsp;&nbsp;
						<small>{tr}Type <code>help</code> to get list of available commands{/tr}</small>
					</td>
				</tr>
			</table>
		</form>

		{* Generate tabs code if more than one tab, else make one div w/o button *}

		{* 1) Buttons bar *}
		{if count($tabs) > 1}
			<table>
				<tr>
					{section name=i loop=$tabs}
						<td>
							{assign var=thistabshref value=$tabs[i].button_href}
							{assign var=thistabscaption value=$tabs[i].button_caption}
							{button _onclick=$thistabshref _text=$thistabscaption _ajax="n"}
						</td>
					{/section}
				</tr>
			</table>
		{/if}

		{* 2) Divs with tabs *}
		{section name=i loop=$tabs}
			<div class="debugger-tab" id="{$tabs[i].tab_id}" style="display:{if $tabs[i].button_caption == 'console'}block{else}none{/if};">
				{$tabs[i].tab_code}
			</div><!-- Tab: {$tabs[i].tab_id} -->
		{/section}

	</div><!-- debug console -->
{/if}
