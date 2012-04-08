{* $Id$ *}
<div class="help" id="tikihelp">
	<div class="help_sections" id="help_sections" style="display:none">
		<ul>
			{foreach item=help from=$help_sections}
				<li>
					<a href="#{$help.id}">
						{$help.title}
					</a>
				</li>
			{/foreach}
		</ul>
		{foreach item=help from=$help_sections}
			<div id="{$help.id}" class="">
				{$help.content}
			</div>
		{/foreach}
	</div>
	{if $prefs.feature_jquery_ui eq "y"}{jq} $(function() {$("#help_sections").tabs({});}); {/jq}{/if}
	{self_link _onclick='openEditHelp(0);return false'}{tr}Show Help{/tr}{icon _id='help'}{/self_link}
{jq notonready=true}
{literal}
function openEditHelp(num) {
  var opts, edithelp_pos = getCookie("edithelp_position");
  opts = { width: 460, height: 500, title: "{/literal}{tr}Help{/tr}{literal}", autoOpen: false, beforeclose: function(event, ui) {
    var off = $(this).offsetParent().offset();
      setCookie("edithelp_position", parseInt(off.left,10) + "," + parseInt(off.top,10) + "," + $(this).offsetParent().width() + "," + $(this).offsetParent().height());
  }};
  if (edithelp_pos) {edithelp_pos = edithelp_pos.split(",");}
  if (edithelp_pos && edithelp_pos.length) {
    opts["position"] = [parseInt(edithelp_pos[0],10), parseInt(edithelp_pos[1],10)];
    opts["width"] = parseInt(edithelp_pos[2],10);
    opts["height"] = parseInt(edithelp_pos[3],10);
  }
  try {
    if ($("#help_sections").dialog) {
      $("#help_sections").dialog("destroy");
    }
  } catch( e ) {
    // IE throws errors destroying a non-existant dialog
  }
  $("#help_sections").dialog(opts).dialog("open");
  $("#help_sections").tabs("select", num);
  
};
{/literal}
{/jq}
</div>
