{* $Id$ *}

<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Upgrade Wizard{/tr}" title="Upgrade Wizard">
		<i class="fa fa-arrow-circle-up fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}New permissions and action log settings{/tr}.
	<br/><br/><br/>
	<div class="media-body">
		{icon name="key" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}Permissions{/tr}</legend>
			<b>{tr}Wiki{/tr}</b>:
			<ul>
				<li>
					{tr}wiki{/tr} > {tr}Can inline-edit pages{/tr} <em>(tiki_p_edit_inline)</em>
					<a href="http://doc.tiki.org/Wiki+Inline+Editing" target="tikihelp" class="tikihelp" title="{tr}Wiki Inline Editing:{/tr}
						{tr}Starting in Tiki12, Tiki offers the option to edit inline a wiki page in wysiwyg mode with a simplified editor, which is based on Ckeditor4{/tr}
						<br/><br/>
						{tr}The editor can be quickly turned on/off. All processing is done client side{/tr}
					">
						{icon name="help" size=1}
					</a>
				</li>
			</ul>
			<b>{tr}Ratings{/tr}</b>:
			<ul>
				<li>
					{tr}tiki{/tr} > {tr}Can view results from user ratings{/tr} <em>(tiki_p_ratings_view_results)</em>
					<a href="http://doc.tiki.org/Ratings" target="tikihelp" class="tikihelp" title="{tr}Ratings:{/tr}
						{tr}Starting in Tiki12, Rating results can be selectively shown to just some user groups, as well as a few other new settings were introduced to fine tune the information shown{/tr}.
					">
						{icon name="help" size=1}
					</a>
				</li>
			</ul>
			<b>{tr}BigBlueButton{/tr}</b>:
			<ul>
				<li>
					{tr}bigbluebutton{/tr} > {tr}Can view recordings from past meetings{/tr} <em>(tiki_p_bigbluebutton_view_rec)</em>
					<a href="http://doc.tiki.org/BigBlueButton" target="tikihelp" class="tikihelp" title="{tr}BigBlueButton:{/tr}
						{tr}New explicit permission tiki_p_bigbluebutton_view_rec needed to view recordings{/tr}
						<br/><br/>
						{tr}tiki_p_bigbluebutton_view_rec is no longer implicit if tiki_p_bigbluebutton_join is granted{/tr}
					">
						{icon name="help" size=1}
					</a>
				</li>
			</ul>
			<b>{tr}Tiki{/tr}</b>:
			<ul>
				<li>
					{tr}tiki{/tr} > {tr}Can switch between wiki and WYSIWYG modes while editing{/tr} <em>(tiki_p_edit_switch_mode)</em>
					<a href="http://doc.tiki.org/Wysiwyg" target="tikihelp" class="tikihelp" title="{tr}Switch editor:{/tr}
						{tr}Starting in Tiki7, Tiki offers the option to allow users to switch the editor from plain text to wysiwyg and viceversa, provided that the user belongs to a group with this required permission granted{/tr}
					">
						{icon name="help" size=1}
					</a>
				</li>
			</ul>
		</fieldset>
		<br/>
		{icon name="book" size=3 iclass="pull-right"}
		<fieldset>
			<legend>{tr}Action log settings{/tr}</legend>
			<b>{tr}BigBlueButton{/tr}</b>:
			<ul>
				<li>{tr}Joined Room{/tr}</li>
				<li>{tr}Left Room{/tr}</li>
			</ul>
		</fieldset>
	</div>
</div>
