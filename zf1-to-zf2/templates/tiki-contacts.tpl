{title}{tr}Contacts{/tr}{/title}

<div class="t_navbar btn-group form-group">
	<div style="float:right;margin:5px;">
		{if $view eq 'list'}
			{button href="?view=group" _class="btn btn-default" _text="{tr}Group View{/tr}"}
		{else}
			{button href="?view=list" _class="btn btn-default" _text="{tr}List View{/tr}"}
		{/if}
	</div>
	<div style="float:left;margin:5px;">
		{button href="#" _onclick="flip('editform');return false;" _class="btn btn-default" _text="{tr}Create/edit contacts{/tr}"}
		{button href="tiki-user_contacts_prefs.php" _class="btn btn-default" _text="{tr}Preferences{/tr}"}
	</div>
</div>

<form action="tiki-contacts.php" method="post" id="editform" name='editform_contact' class="form-horizontal" style="clear:both;margin:5px;display:{if $contactId}block{else}none{/if};">
    <input type="hidden" name="locSection" value="contacts">
    <input type="hidden" name="contactId" value="{$contactId|escape}">

    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}First Name{/tr}</label>
        <div class="col-sm-7">
            <input type="text" maxlength="80" size="20" name="firstName" value="{$info.firstName|escape}" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}Last Name{/tr}</label>
        <div class="col-sm-7">
            <input type="text" maxlength="80" size="20" name="lastName" value="{$info.lastName|escape}" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}Email{/tr}</label>
        <div class="col-sm-7">
            <input type="text" maxlength="80" size="20" name="email" value="{$info.email|escape}" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}Nickname{/tr}</label>
        <div class="col-sm-7">
            <input type="text" maxlength="80" size="20" name="nickname" value="{$info.nickname|escape}" class="form-control">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}Publish this contact to groups{/tr}</label>
        <div class="col-sm-7">
            <select multiple="multiple" name="groups[]" size="6" class="form-control">
                <option value=""></option>
                {foreach item=group from=$groups}
                    <option value="{$group|escape}"{if in_array($group,$info.groups)} selected="selected"{/if}>{$group}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label">{tr}Extra Fields{/tr}</label>
        <div class="col-sm-7">
            <select id='select_exts' onchange='ext_select();' class="form-control">
                <option>{tr}More...{/tr}</option>
            </select>
        </div>
    </div>

    <div id="extra-fields-placeholder">
        <div class="form-group hidden">
            <label class="col-sm-3 control-label"></label>
            <div class="col-sm-7">
                <input value="" name="" size="20" maxlength="80" class="form-control">
            </div>
            <div class="col-sm-1 hidden">

            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-3 control-label"></label>
        <div class="col-sm-7">
            <input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
        </div>
    </div>
</form>

{include file='find.tpl'}

{initials_filter_links}
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
<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
<table class="table table-striped table-hover">
	<tr>
		{assign var=numbercol value=4}
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'firstName_desc'}firstName_asc{else}firstName_desc{/if}">{tr}First Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastName_desc'}lastName_asc{else}lastName_desc{/if}">{tr}Last Name{/tr}</a>
		</th>
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}Email{/tr}</a>
		</th>
		<th>
			<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nickname_desc'}nickname_asc{else}nickname_desc{/if}">{tr}Nickname{/tr}</a>
		</th>
		{foreach from=$exts item=ext key=k}
			{if $ext.show eq 'y'}
				<th>
					{assign var=numbercol value=$numbercol+1}
					<a>{$ext.tra}</a>
				</th>
			{/if}
		{/foreach}

		{if $view eq 'list'}
			{assign var=numbercol value=$numbercol+1}
			<th>{tr}Groups{/tr}</th>
		{/if}

		{assign var=numbercol value=$numbercol+1}
		<th></th>
	</tr>


	{foreach key=k item=channels from=$all}
		{if count($channels)}
			{if $view neq 'list'}
				<tr>
					<td colspan="5" style="font-size:80%;color:#999;">
						{tr}from{/tr} <b>{$k}</b>
					</td>
				</tr>
			{/if}
			{section name=user loop=$channels}
				<tr>
					<td class="text">
						<a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">
							{$channels[user].firstName}
						</a>
					</td>
					<td class="text">
						<a class="link" href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">
							{$channels[user].lastName}
						</a>
					</td>
					<td class="email">
						{if $prefs.feature_webmail eq 'y'}
							{self_link _script='tiki-webmail.php' locSection='compose' to=$channels[user].email}{$channels[user].email}{/self_link}
						{else}
							<a class="link" href="mailto:{$channels[user].email}">{$channels[user].email}</a>
						{/if}
					</td>
					<td class="text">
						{$channels[user].nickname}
					</td>
					{foreach from=$exts item=ext key=e}
						{if $ext.show eq 'y'}
							<td>{$channels[user].ext[$e]}</td>
						{/if}
					{/foreach}
					{if $view eq 'list'}
						<td>
							{if isset($channels[user].groups)}
								{foreach item=it name=gr from=$channels[user].groups}
									{$it}
									{if $smarty.foreach.gr.index+1 ne $smarty.foreach.gr.last}, {/if}
								{/foreach}
							{else}
								&nbsp;
							{/if}
						</td>
					{/if}

					<td class="action">
						{capture name=contact_actions}
							{strip}
								{if $channels[user].user eq $user or $tiki_p_admin eq 'y'}
									{if $channels[user].user eq $user}
										{$libeg}<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;contactId={$channels[user].contactId}">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
									{/if}
									{$libeg}<a href="tiki-contacts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;remove={$channels[user].contactId}">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>{$liend}
								{/if}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.contact_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.contact_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
			{/section}
		{else}
			{norecords _colspan=$numbercol}
		{/if}
	{/foreach}
</table>
</div>

<div class="center-block">
	{if $prev_offset >= 0}
		[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]
		&nbsp;
	{/if}
	{tr}Page:{/tr} {$actual_page}/{$cant_pages}
	{if $next_offset >= 0}
		&nbsp;
		[<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
	{/if}
	{if $prefs.direct_pagination eq 'y'}
		<br>
		{section loop=$cant_pages name=foo}
			{assign var=selector_offset value=$smarty.section.foo.index|times:$prefs.maxRecords}
			<a class="prevnext" href="tiki-contacts.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">{$smarty.section.foo.index_next}</a>
			&nbsp;
		{/section}
	{/if}
</div>

{literal}
<script type="text/javascript">
    function createElementOrFill(type, vals) {
        var elem;

        if(typeof type === 'object') {
            elem = type;
        }else {
            elem = document.createElement(type);
        }

        for (key in vals) {
            elem.setAttribute(key, vals[key]);
        }

        return elem;
    }

    function htmlspecialchars(ch) {
        ch = ch.replace(/&/g,"&amp;");
        ch = ch.replace(/\"/g,"&quot;");
        ch = ch.replace(/\'/g,"&#039;");
        ch = ch.replace(/</g,"&lt;");
        ch = ch.replace(/>/g,"&gt;");
        return ch;
    }

    function ext_add(extid, text, defaultvalue, pub) {
        var newElement = document.querySelector("#extra-fields-placeholder .form-group.hidden").cloneNode(true); //clones nodes too
        newElement = createElementOrFill(newElement, { id : 'tr_ext_'+extid });
        var label = newElement.querySelector('.control-label').innerHTML = text;
        var input = createElementOrFill(newElement.querySelector('input'), { maxlength : 80, name : 'ext_'+extid, value : defaultvalue});
        newElement.classList.remove('hidden');
        document.querySelector('#extra-fields-placeholder').appendChild(newElement);

        if (pub != 'y' || {/literal}{if $tiki_p_admin_group_webmail eq 'y'}1{else}0{/if}{literal}) {	// add button only if not public
            var inputDiv = newElement.querySelector('.col-sm-7');
            var buttonDiv = newElement.querySelector('.col-sm-1');
            var removeButton = createElementOrFill('input', {type:'button', name:'ext_'+extid, value:'-', 'onclick':'ext_remove(\''+extid+'\');' });
            inputDiv.classList.remove('col-sm-7');
            inputDiv.classList.add('col-sm-6');
            buttonDiv.classList.remove('hidden');
            removeButton.classList.add('btn');
            removeButton.classList.add('btn-default');
            buttonDiv.appendChild(removeButton);
        }
    }

    function ext_select() {
        var value = $('#select_exts option:selected').val();
        var text = $('#select_exts option:selected').html();
        $('#select_exts option:nth-child(1)').attr('selected', true);
        ext_add(value, htmlspecialchars(text), '');
    }

    function ext_remove(extid) {
        $('#tr_ext_'+extid).remove();
        $('#ext_option_'+extid).attr('disabled', false);
    }

    function extmenu_add(extid, text, defaultvalue, pub) {
        var selectelem=document.getElementById('select_exts');
        var option=createElementOrFill('option', { 'id':'ext_option_'+extid, 'value':extid });
        option.innerHTML=text;
        selectelem.appendChild(option);
        if (defaultvalue != '')
            ext_add(extid, text, defaultvalue, pub);
    }
{/literal}

{foreach from=$exts item=ext key=k}
	extmenu_add('{$k|escape}', '{$ext.tra|escape}', '{$info.ext[$ext.id]|escape:quotes}', '{$ext.public|escape}');
{/foreach}

{literal}
	</script>
{/literal}
