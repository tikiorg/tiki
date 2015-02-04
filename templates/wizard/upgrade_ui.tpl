{* $Id$ *}

<img class="pull-right" src="img/icons/large/ui48x48.png" alt="{tr}User Interface{/tr}" title="{tr}User Interface{/tr}"/>
<div class="media">
	<img class="pull-left" src="img/icons/large/wizard_upgrade48x48.png" alt="{tr}Upgrade Wizard{/tr}" title="{tr}Upgrade Wizard{/tr}"/>
	<div class="media-body">
		{tr}Some User Interface (UI) improvements which usually come disabled in new Tiki installations{/tr}.
		<a href="http://doc.tiki.org/Interface" target="tikihelp" class="tikihelp" title="{tr}User Interface:{/tr}
			{tr}They are proven to be useful enhancements in some production environments{/tr}.
			<br/><br/>
			{tr}The ones still tagged as <em>experimental</em> <img src=img/icons/error.png> might have failed to work under some environments, but they are very likely to work as-is in your environment also, so you might like to give them a try{/tr}.
		">
			<img src="img/icons/help.png" alt="" width="16" height="16" class="icon" />
		</a>
		<fieldset class="table clearfix featurelist">
			<legend> {tr}Icons and Profile Pictures{/tr} </legend>
			{preference name=menus_items_icons}
			{preference name=user_use_gravatar}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend> {tr}File Galleries{/tr} </legend>
			{preference name=fgal_elfinder_feature}
			<div class="adminoptionboxchild" id="fgal_elfinder_feature_childcontainer">
				<input type="checkbox" name="useElFinderAsDefault" {if !isset($useElFinderAsDefault) or $useElFinderAsDefault eq true}checked='checked'{/if} /> {tr}Set elFinder as the default file gallery viewer{/tr}.
				<div class="adminoptionboxchild">
					{tr}See also{/tr} <a href="http://doc.tiki.org/elFinder" target="_blank">{tr}elFinder{/tr} @ doc.tiki.org</a>
				</div>
			</div>
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend> {tr}Text Areas{/tr} </legend>
			{preference name=wiki_auto_toc}
			<div class="adminoptionboxchild" id="wiki_auto_toc_childcontainer">
				{preference name=wiki_inline_auto_toc}
				{preference name=wiki_inline_toc_pos}
			</div>
			{preference name=wysiwyg_inline_editing}
		</fieldset>
		<fieldset class="table clearfix featurelist">
			<legend> {tr}jQuery plugins and add-ons{/tr} </legend>
			{preference name=feature_jquery_media}
			{preference name=feature_jquery_reflection}
			{preference name=feature_jquery_zoom}
			{preference name=feature_jquery_carousel} <img src="img/icons/bug_error.png" alt="{tr}Experimental{/tr}" title="{tr}Experimental{/tr}">
			{preference name=feature_jquery_tablesorter} <img src="img/icons/bug_error.png" alt="{tr}Experimental{/tr}" title="{tr}Experimental{/tr}">
			{preference name=jquery_ui_chosen} <img src="img/icons/bug_error.png" alt="{tr}Experimental{/tr}" title="{tr}Experimental{/tr}">
		</fieldset>
	</div>
</div>
