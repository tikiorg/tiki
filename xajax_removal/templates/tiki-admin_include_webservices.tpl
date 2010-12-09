<form method="post" action="tiki-admin.php?page=webservices">
	<div class="navbar">
		{foreach from=$webservices item=name}
			{button href="tiki-admin.php?page=webservices&amp;name=$name" _text=$name}
		{/foreach}
		{if $storedName}
			{button href="tiki-admin.php?page=webservices" _text="{tr}Create New{/tr}"}
		{/if}
	</div>

	{if $storedName}
		<p><strong>{$storedName|escape}</strong>: {$url|escape}<input type="hidden" name="name" value="{$storedName|escape}"/> <a href="tiki-admin.php?page=webservices&amp;name={$storedName|escape}&amp;delete">{icon _id='cross'}</a></p>
	{else}
		<p>
			{tr}Enter the URL of a web services returning either JSON or YAML. Parameters can be specified by enclosing a name between percentage signs. For example: %name%. %service% and %template% are reserved keywords and cannot be used.{/tr}
		</p>
		<p>{tr}URL:{/tr}<input type="text" name="url" size="75" value="{$url|escape}" /></p>
		<p>{tr}Type:{/tr}<select name="wstype">
		{foreach from=$webservicesTypes item=_type}
			<option value="{$_type}"{if $wstype eq $_type} selected="selected"{/if}>{$_type}</option>
		{/foreach}
		</select></p>
		<p id="ws_postbody">{tr}Parameters (%name%):{/tr}<textarea name="postbody">{$postbody|escape}</textarea></p>
		<p id="ws_operation" style="display: none;">{tr}Operation:{/tr}<input type="text" name="operation" size="30" value="{$operation|escape}" /></p>
 		<p><input type="submit" name="parse" value="{tr}Lookup{/tr}"/></p>
	{/if}
	{if $url and $params|@count}
		<h3>{tr}Parameters{/tr}</h3>
		<table class="formcolor">
			{foreach from=$params key=name item=value}
				<tr>
					<td>{$name|escape}</td>
					<td><input type="text" name="params[{$name|escape}]" value="{$value|escape}" /></td>
				</tr>
			{/foreach}
			<tr>
				<td colspan="2">
					<input type="submit" name="test" value="{tr}Test Input{/tr}" />
				</td>
			</tr>
		</table>
	{/if}
	{if $data}
		<h3>{tr}Response Information{/tr}</h3>
		<table class="normal">
			<tr>
				<th>{tr}OIntegrate Version{/tr}</th>
				<td>{if $response->version}{$response->version|escape}{else}<em>{tr}Not supported{/tr}</em>{/if}
			</tr>
			<tr>
				<th>{tr}Schema Version{/tr}</th>
				<td>{if $response->schemaVersion}{$response->schemaVersion|escape}{else}<em>{tr}Not supported{/tr}</em>{/if}
			</tr>
			<tr>
				<th>{tr}Schema Documentation{/tr}</th>
				<td>{if $response->schemaDocumentation}<a href="{$response->schemaDocumentation|escape}">{tr}Available{/tr}</a>{else}<em>{tr}Not supported{/tr}</em>{/if}
			</tr>
			<tr>
				<th>{tr}Cache{/tr}</th>
				<td>{if $response->cacheControl}{$response->cacheControl|escape}{else}<em>{tr}Not specified, default used{/tr}</em>{/if}
			</tr>
			<tr>
				<th>{tr}Content Type{/tr}</th>
				<td>{if $response->contentType}{$response->contentType|escape}{else}<strong>{tr}Not specified{/tr}</strong>{/if}
			</tr>
			<tr>
				<th colspan="2">{tr}Returned Data{/tr}</th>
			</tr>
			<tr>
				<td colspan="2"><pre>{$data|escape}</pre></td>
			</tr>
			<tr>
				<th colspan="2">{tr}Proposed Templates{/tr}</th>
			</tr>
			{foreach from=$templates item=template key=number}
				<tr>
					<th>
						{$template.engine|escape}/{$template.output|escape}
						<input type="submit" name="add[{$number}]" value="{tr}Add{/tr}"/>
					</th>
					<td><pre>{$template.content|escape}</pre></td>
				</tr>
			{foreachelse}
				<tr>
					<th>{tr}None{/tr}</th>
				</tr>
			{/foreach}
		</table>
		{if ! $storedName}
			<p>{tr}Register this web service. It will be possible to register the templates afterwards. Service name must only contain letters.{/tr}</p>
			<p>
				<input type="text" name="new_name" />
				<input type="submit" name="register" value="{tr}Register Service{/tr}" />
			</p>
		{else}
			<h3>{tr}Registered Templates{/tr}</h3>
			<table class="formcolor">
				<tr>
					<th>{tr}Name{/tr}</th>
					<th>{tr}Engine{/tr}</th>
					<th>{tr}Output{/tr}</th>
					<th>{tr}Preview{/tr}</th>
				</tr>
				{foreach from=$storedTemplates item=template}
					<tr>
						<td>
							<input type="submit" name="loadtemplate" value="{$template->name|escape}"/>
							<a href="tiki-admin.php?page=webservices&amp;name={$storedName|escape}&amp;delete={$template->name|escape}">{icon _id='cross'}</a>
						</td>
						<td>{$template->engine|escape}</td>
						<td>{$template->output|escape}</td>
						<td><input type="submit" name="preview" value="{$template->name|escape}"/></td>
					</tr>
					<tr><td colspan="4"><pre>{$template->content|escape}</pre></td></tr>
					{if $preview eq $template->name}
						<tr><td colspan="4">{$preview_output}</td></tr>
					{/if}
				{/foreach}
				<tr>
					<td><input type="text" name="nt_name" value="{$nt_name|escape}"/></td>
					<td><input type="text" name="nt_engine" value="{$nt_engine|escape}"/></td>
					<td><input type="text" name="nt_output" value="{$nt_output|escape}"/></td>
				</tr>
				<tr><td colspan="4"><textarea name="nt_content" rows="10">{$nt_content|escape}</textarea></td></tr>
				<tr><td colspan="4"><input type="submit" name="create_template" value="{tr}Register Template{/tr}"/></td></tr>
			</table>
		{/if}
	{/if}
</form>
