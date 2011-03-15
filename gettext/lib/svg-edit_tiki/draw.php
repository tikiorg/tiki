<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

ini_set( 'include_path', ini_get( 'include_path' ) . ":lib/svg-edit_tiki" );

/** TikiDraw Class {{{1
 * Class containing the function helpers for svg-edit
 */
class TikiDraw
{
	function setup_draw() {
		global $headerlib;
		if (!$this->setup_draw_files) {
			$p = "lib/svg-edit/";
			
			$headerlib->add_cssfile( $p. 'jgraduate/css/jPicker.css' );
			$headerlib->add_cssfile( $p. 'jgraduate/css/jgraduate.css' );
			$headerlib->add_cssfile( $p. 'svg-editor.css' );
			$headerlib->add_cssfile( $p. 'spinbtn/JQuerySpinBtn.css' );
			
			$headerlib->add_jsfile( $p. 'js-hotkeys/jquery.hotkeys.min.js', true );
			$headerlib->add_jsfile( $p. 'jgraduate/jquery.jgraduate.js' );
			$headerlib->add_jsfile( $p. 'svgicons/jquery.svgicons.js' );
			$headerlib->add_jsfile( $p. 'jquerybbq/jquery.bbq.min.js' );
			$headerlib->add_jsfile( $p. 'spinbtn/JQuerySpinBtn.js' );
			$headerlib->add_jsfile( $p. 'contextmenu/jquery.contextMenu.js' );
			$headerlib->add_jsfile( $p. 'svgcanvas.js' );
			
			$headerlib->add_jsfile( $p. 'browser.js' );
			$headerlib->add_jsfile( $p. 'svgtransformlist.js' ); 
			$headerlib->add_jsfile( $p. 'math.js' );
			$headerlib->add_jsfile( $p. 'units.js' );
			$headerlib->add_jsfile( $p. 'svgutils.js' );
			$headerlib->add_jsfile( $p. 'sanitize.js' );
			$headerlib->add_jsfile( $p. 'history.js' );
			$headerlib->add_jsfile( $p. 'select.js' );
			$headerlib->add_jsfile( $p. 'draw.js' );
			$headerlib->add_jsfile( $p. 'path.js' );
			$headerlib->add_jsfile( $p. 'svgcanvas.js' );
			//$headerlib->add_jsfile( $p. 'svg-editor.js' );
			//$headerlib->add_jsfile( $p. 'locale/locale.js' );
			//Work around for svg-editor's configs, they are note editable with externals
			//$headerlib->add_jsfile( 'lib/svg-edit/svg-editor.js' );
			
			$svgEditor = file_get_contents( $p. 'svg-editor.js' ) . '';
			
			$svgEditor = str_replace("imgPath: 'images", 					"imgPath: 'lib/svg-edit/images", 					$svgEditor);
			$svgEditor = str_replace("langPath: 'locale", 					"langPath: 'lib/svg-edit/locale", 					$svgEditor);
			$svgEditor = str_replace("extPath: 'extensions", 				"extPath: 'lib/svg-edit/extensions", 				$svgEditor);
			$svgEditor = str_replace("jGraduatePath: 'jgraduate/images", 	"jGraduatePath: 'lib/svg-edit/jgraduate/images", 	$svgEditor);
			
			$svgEditor = str_replace("'new_image':'clear.png'", "'new_image':'".$p."clear.png'", $svgEditor);
			$svgEditor = str_replace("'save':'save.png'", "'save':'".$p."save.png'", $svgEditor);
			$svgEditor = str_replace("'open':'open.png'","'open':'".$p."open.png'", $svgEditor);
			$svgEditor = str_replace("'source':'source.png'","'source':'".$p."source.png'", $svgEditor);
			$svgEditor = str_replace("'docprops':'document-properties.png'","'docprops':'".$p."document-properties.png'", $svgEditor);
			$svgEditor = str_replace("'wireframe':'wireframe.png'","'wireframe':'".$p."wireframe.png'", $svgEditor);
					
			$svgEditor = str_replace("'undo':'undo.png'","'undo':'".$p."undo.png'", $svgEditor);
			$svgEditor = str_replace("'redo':'redo.png'","'redo':'".$p."redo.png'", $svgEditor);
					
			$svgEditor = str_replace("'select':'select.png'","'select':'".$p."select.png'", $svgEditor);
			$svgEditor = str_replace("'select_node':'select_node.png'","'select_node':'".$p."select_node.png'", $svgEditor);
			$svgEditor = str_replace("'pencil':'fhpath.png'","'pencil':'".$p."fhpath.png'", $svgEditor);
			$svgEditor = str_replace("'pen':'line.png'","'pen':'".$p."line.png'", $svgEditor);
			$svgEditor = str_replace("'square':'square.png'","'square':'".$p."square.png'", $svgEditor);
			$svgEditor = str_replace("'rect':'rect.png'","'rect':'".$p."rect.png'", $svgEditor);
			$svgEditor = str_replace("'fh_rect':'freehand-square.png'","'fh_rect':'".$p."freehand-square.png'", $svgEditor);
			$svgEditor = str_replace("'circle':'circle.png'","'circle':'".$p."circle.png'", $svgEditor);
			$svgEditor = str_replace("'ellipse':'ellipse.png'","'ellipse':'".$p."ellipse.png'", $svgEditor);
			$svgEditor = str_replace("'fh_ellipse':'freehand-circle.png'","'fh_ellipse':'".$p."freehand-circle.png'", $svgEditor);
			$svgEditor = str_replace("'path':'path.png'","'path':'".$p."path.png'", $svgEditor);
			$svgEditor = str_replace("'text':'text.png'","'text':'".$p."text.png'", $svgEditor);
			$svgEditor = str_replace("'image':'image.png'","'image':'".$p."image.png'", $svgEditor);
			$svgEditor = str_replace("'zoom':'zoom.png'","'zoom':'".$p."zoom.png'", $svgEditor);
		
			$svgEditor = str_replace("'clone':'clone.png'","'clone':'".$p."clone.png'", $svgEditor);
			$svgEditor = str_replace("'node_clone':'node_clone.png'","'node_clone':'".$p."node_clone.png'", $svgEditor);
			$svgEditor = str_replace("'delete':'delete.png'","'delete':'".$p."delete.png'", $svgEditor);
			$svgEditor = str_replace("'node_delete':'node_delete.png'","'node_delete':'".$p."node_delete.png'", $svgEditor);
			$svgEditor = str_replace("'group':'shape_group.png'","'group':'".$p."shape_group.png'", $svgEditor);
			$svgEditor = str_replace("'ungroup':'shape_ungroup.png'","'ungroup':'".$p."shape_ungroup.png'", $svgEditor);
			$svgEditor = str_replace("'move_top':'move_top.png'","'move_top':'".$p."move_top.png'", $svgEditor);
			$svgEditor = str_replace("'move_bottom':'move_bottom.png'","'move_bottom':'".$p."move_bottom.png'", $svgEditor);
			$svgEditor = str_replace("'to_path':'to_path.png'","'to_path':'".$p."to_path.png'", $svgEditor);
			$svgEditor = str_replace("'link_controls':'link_controls.png'","'link_controls':'".$p."link_controls.png'", $svgEditor);
			$svgEditor = str_replace("'reorient':'reorient.png'","'reorient':'".$p."reorient.png'", $svgEditor);
					
			$svgEditor = str_replace("'align_left':'align-left.png'","'align_left':'".$p."align-left.png'", $svgEditor);
		
			$svgEditor = str_replace("'go_up':'go-up.png'","'go_up':'".$p."go-up.png'", $svgEditor);
			$svgEditor = str_replace("'go_down':'go-down.png'","'go_down':'".$p."go-down.png'", $svgEditor);
		
			$svgEditor = str_replace("'ok':'save.png'","'ok':'".$p."save.png'", $svgEditor);
			$svgEditor = str_replace("'cancel':'cancel.png'","'cancel':'".$p."cancel.png'", $svgEditor);
					
			$svgEditor = str_replace("'arrow_right':'flyouth.png'","'arrow_right':'".$p."flyouth.png'", $svgEditor);
			$svgEditor = str_replace("'arrow_down':'dropdown.gif'","'arrow_down':'".$p."dropdown.gif'", $svgEditor);
			
			$svgEditor .= file_get_contents( $p. 'locale/locale.js' );
			
			$headerlib->add_js($svgEditor);
			
			$headerlib->add_jsfile( 'lib/svg-edit/jquery-ui/jquery-ui-1.8.custom.min.js', true );
			$headerlib->add_jsfile( 'lib/svg-edit/jgraduate/jpicker.min.js', true ); 
			
			$this->setup_draw_files = true;
		}
	}
	
	function get_file() {}
	
	function save_file() {}
}
// }}}1
