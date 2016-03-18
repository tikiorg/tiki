{title help="User Contacts Prefs"}{tr}User Contacts Preferences{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
<div class="t_navbar">
	{button href="tiki-contacts.php" class="btn btn-default" _text="{tr}Contacts{/tr}"}
</div>

{tabset name="contact_prefs"}
	{tab name="{tr}Options{/tr}"}
		<h2>{tr}Options{/tr}</h2>
		<div class="panel panel-default">
			<div class="panel-body">
				<form method='post' action='tiki-user_contacts_prefs.php'>
					<table class="formcolor">
						<tr>
							<td>{tr}Default View:{/tr}</td>
							<td>
								<input type='radio' name='user_contacts_default_view' value='list' {if $user_contacts_default_view eq 'list'}checked="checked"{/if}>
								{tr}List View{/tr}
								<input type='radio' name='user_contacts_default_view' value='group' {if $user_contacts_default_view neq 'list'}checked="checked"{/if}>
								{tr}Group View{/tr}
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type='submit' name='prefs' value="{tr}Change preferences{/tr}">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	{/tab}

	{tab name="{tr}Manage Fields{/tr}"}
		<h2>{tr}Manage Fields{/tr}</h2>
		<div class="panel panel-default">
			<div class="panel-body">
				<form method='post' action='tiki-user_contacts_prefs.php'>
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th colspan="2">{tr}Order{/tr}</th>
								<th>{tr}Field{/tr}</th>
								<th></th>
							</tr>

							{foreach from=$exts item=ext key=k name=e}
								<tr>
									<td width="2%">
										{if not $smarty.foreach.e.first}
											<a href="?ext_up={$ext.fieldId}" class="tips" title=":{tr}Up{/tr}">
												{icon name='up'}</a>
										{/if}
									</td>
									<td width="2%">
										{if not $smarty.foreach.e.last}
											<a href="?ext_down={$ext.fieldId}" class="tips" title=":{tr}Down{/tr}">
												{icon name='down'}
											</a>
										{/if}
									</td>
									<td>{tr}{$ext.fieldname|escape}{/tr}</td>
									<td class="action">
										{if $ext.flagsPublic eq 'y'}
											<a href="?ext_private={$ext.fieldId}" style="margin-left:20px;" class="tips" title=":{tr}Private{/tr}">
												{icon name='user'}
											</a>
										{else}
											<a href="?ext_public={$ext.fieldId}" style="margin-left:20px;" class="tips" title=":{tr}Public{/tr}">
												{icon name='group'}
											</a>
										{/if}
										{if $ext.show eq 'y'}
											<a href="?ext_hide={$ext.fieldId}" style="margin-left:20px;" class="tips" title=":{tr}Hide{/tr}">
												{icon name='ban'}
											</a>
										{else}
											<a href="?ext_show={$ext.fieldId}" style="margin-left:20px;" class="tips" title=":{tr}Show{/tr}">
												{icon name='view'}
											</a>
										{/if}
										<a href="?ext_remove={$ext.fieldId}" style="margin-left:20px;" class="tips" title=":{tr}Remove{/tr}">
											{icon name='remove'}
										</a>
									</td>
								</tr>
							{/foreach}
						</table>
					</div>
					<div>{tr}Add:{/tr} <input type='text' name='ext_add' /> <input type='submit' name='add_fields' value="{tr}Add{/tr}"></div>
				</form>
			</div>
		</div>
	{/tab}
{/tabset}
