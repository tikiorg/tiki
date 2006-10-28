{* 
	$Header: /cvsroot/tikiwiki/_mods/wiki-plugins/mindmap/templates/wikiplugin_mindmap.tpl,v 1.1 2006-10-28 20:04:27 fumphco Exp $
	Smarty template for the mindmap wikiplugin
*}
{if $mode=="window" or $mode=="fullscreen"}
{* uncomment this block to display a link to close this window
{if $mode=="fullscreen"}
{$line_begin}<div align="right"><a href="javascript: self.close()">Close this Window</a></div>{$line_end}
{/if}
*}
{$line_begin}<html>{$line_end}
{$line_begin}<head>{$line_end}
{$line_begin}	<title>{$title}</title>{$line_end}
{$line_begin}</head>{$line_end}
{$line_begin}<body>{$line_end}
{/if}
{if $plugin=="java"}
{* invoke the freemind browser java applet *}
{$line_begin}<APPLET CODE="freemind.main.FreeMindApplet.class" ARCHIVE="lib/mindmap/freemindbrowser.jar" WIDTH="{$width}" HEIGHT="{$height}">{$line_end}
{$line_begin}	<PARAM NAME="type" VALUE="application/x-java-applet;version=1.4">{$line_end}
{$line_begin}	<PARAM NAME="scriptable" VALUE="false">{$line_end}
{$line_begin}	<PARAM NAME="modes" VALUE="freemind.modes.browsemode.BrowseMode">{$line_end}
{$line_begin}	<PARAM NAME="browsemode_initial_map" VALUE="{$src}">{$line_end}
{$line_begin}	<PARAM NAME="initial_mode" VALUE="Browse">{$line_end}
{$line_begin}	<PARAM NAME="selection_method" VALUE="selection_method_direct">{$line_end}
{$line_begin}</APPLET>{$line_end}
{else}
{* per flash technote http://www.adobe.com/cfusion/knowledgebase/index.cfm?id=tn_4150 *}
{* invoke the freemind browser flash using <object> for IE and <embed> for other browsers *}
{$line_begin}<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"{$line_end}
{$line_begin}	codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0"{$line_end}
{$line_begin}	WIDTH="{$width}" HEIGHT="{$height}" id="visorFreeMind">{$line_end}
{$line_begin}	<PARAM NAME="movie" VALUE="lib/mindmap/visorFreemind.swf" />{$line_end}
{$line_begin}	<PARAM NAME="quality" VALUE="high" />{$line_end}
{$line_begin}	<PARAM NAME="bgcolor" VALUE="#FFFFFF" />{$line_end}
{$line_begin}	<PARAM NAME="flashvars" VALUE="{$flash_vars}" />{$line_end}
{$line_begin}	<EMBED src="lib/mindmap/visorFreemind.swf" quality=high bgcolor=#FFFFFF WIDTH="{$width}" HEIGHT="{$height}"{$line_end}
{$line_begin}		NAME="visorFreeMind" ALIGN="" TYPE="application/x-shockwave-flash"{$line_end}
{$line_begin}		flashvars="{$flash_vars}"{$line_end}
{$line_begin}		PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">{$line_end}
{$line_begin}	</EMBED>{$line_end}
{$line_begin}</OBJECT>{$line_end}
{/if}
{if $mode=="window" or $mode=="fullscreen"}
{$line_begin}</body>{$line_end}
{$line_begin}</html>{$line_end}
{/if}

