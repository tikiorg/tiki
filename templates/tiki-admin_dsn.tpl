{title help="Admin+DSN"}{tr}Admin Content Sources{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Use Admin DSN to define the database to be used by the SQL plugin.{/tr}
{/remarksbox}

<h2>{tr}Create/edit DSN{/tr}</h2>
<form action="tiki-admin_dsn.php" method="post">
	<input type="hidden" name="dsnId" value="{$dsnId|escape}" />
	<table class="formcolor">
		<tr>
		<td>{tr}Name:{/tr}</td>
		 <td>
			<input type="text" maxlength="255" size="10" name="name" value="{$info.name|escape}" />
			</td>
		</tr>
		<tr>
			<td>{tr}DSN:{/tr}</td>
			<td>
				<input type="text" maxlength="255" size="40" name="dsn" value="{$info.dsn|escape}" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>

<h2>{tr}DSN{/tr}</h2>
<table class="normal">
	<tr>
		<th>
			<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'dsn_desc'}dsn_asc{else}dsn_desc{/if}">{tr}DSN{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	<tr class="{cycle}">
		<td class="text">{tr}Local (Tiki database){/tr}</td>
		<td class="text">{tr}See db/local.php{/tr}</td>
		<td class="action">
			&nbsp;&nbsp;
			<a class="link" href="tiki-objectpermissions.php?objectName=local&amp;objectType=dsn&amp;permType=dsn&amp;objectId=local">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
		</td>
	</tr>
	{section name=user loop=$channels}
		<tr class="{cycle}">
			<td class="text">{$channels[user].name}</td>
			<td class="text">{$channels[user].dsn}</td>
			<td class="action">
				&nbsp;&nbsp;
				<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;dsnId={$channels[user].dsnId}">{icon _id='page_edit'}</a> &nbsp;
				<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].dsnId}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
				<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=dsn&amp;permType=dsn&amp;objectId={$channels[user].name|escape:"url"}">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
			</td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

<h2>{tr}Content Authentication{/tr}</h2>
<form id="source-form" method="post" action="">
	<fieldset>
		<legend>{tr}Identification{/tr}</legend>
		<label>
			{tr}Identifier:{/tr}
			<select name="existing">
				<option value="">{tr}New{/tr}</option>
			</select>
		</label>
		<input type="text" name="identifier"/>
		<label>{tr}URL:{/tr} <input type="url" name="url"/></label>
		<label>
			{tr}Type:{/tr}
			<select name="method">
				<option value="basic">{tr}HTTP Basic{/tr}</option>
			</select>
		</label>
	</fieldset>
	<fieldset class="method basic">
		<legend>{tr}HTTP Basic{/tr}</legend>
		<label>{tr}Username:{/tr} <input type="text" name="basic_username"/></label>
		<label>{tr}Password:{/tr} <input type="password" name="basic_password"/></label>
	</fieldset>
	<fieldset>
		<input type="submit" name="save" value="{tr}Save{/tr}"/>
		<input type="submit" name="delete" value="{tr}Delete{/tr}"/>
	</fieldset>
</form>
{jq}
$('#source-form').each(function () {
	var form = this, reload = function () {
		$('option.added', form).remove();
		$.getJSON('tiki-ajax_services.php', {
			controller: 'auth_source',
			action: 'list',
		}, function (entries) {
			$.each(entries, function (k, v) {
				$(form.existing).append($('<option class="added"/>').text(v));
			});
		});
	};

	$(form).submit(function () {
		return false;
	});

	$(form.existing).change(function () {
		var val = $(this).val();

		if (val.length) {
			$(form.identifier).hide().val(val);

			$.getJSON('tiki-ajax_services.php', {
				controller: 'auth_source',
				action: 'fetch',
				identifier: $(form.existing).val()
			}, function (data) {
				$(form.method).val(data.method).change();
				$(form.url).val(data.url);

				switch (data.method) {
				case 'basic':
					$(form.basic_username).val(data.arguments.username);
					$(form.basic_password).val(data.arguments.password);
					break;
				}
			});
		} else {
			$(form.identifier).show().val('').focus();
			$('input:not(:submit)', form).val('');
		}
	});

	$(form.type).change(function () {
		$('fieldset.method', form).hide();
		$('fieldset.method.' + $(this).val(), form).show();
	});


	reload();

	$(form.save).click(function () {
		var data = {
			controller: 'auth_source',
			action: 'save',
			identifier: $(form.identifier).val(),
			url: $(form.url).val(),
			method: $(form.method).val()
		}, isNew = $(form.existing).val() === '';

		switch (data.method) {
		case 'basic':
			data['arguments~username'] = $(form.basic_username).val();
			data['arguments~password'] = $(form.basic_password).val();
			break;
		}

		$(form.existing).val('').change();

		$.post('tiki-ajax_services.php', data, function () {
			if (isNew) {
				$(form.existing).append($('<option/>').text(data.identifier));
			}

			$(form.existing).val(data.identifier).change();
		});
		return false;
	});

	$(form.delete).click(function () {
		$.post('tiki-ajax_services.php', {
			controller: 'auth_source',
			action: 'delete',
			identifier: $(form.existing).val()
		}, function () {
			$(form.existing).val('').change();
			reload();
		});
		return false;
	});

	$(form.type).change();
});
{/jq}
