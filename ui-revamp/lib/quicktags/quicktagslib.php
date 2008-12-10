<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

abstract class Quicktag
{
	protected $wysiwyg;
	protected $icon;
	protected $label;

	abstract function isAccessible();
	abstract function getWikiHtml();

	function getWysiwygToken() // {{{
	{
		return $this->wysiwyg;
	} // }}}

	protected function setIcon( $icon ) // {{{
	{
		$this->icon = $icon;

		return $this;
	} // }}}

	protected function setLabel( $label ) // {{{
	{
		$this->label = $label;

		return $this;
	} // }}}

	protected function setWysiwygToken( $token ) // {{{
	{
		$this->wysiwyg = $token;

		return $this;
	} // }}}

	protected function getIconHtml() // {{{
	{
		return '<img src="' . htmlentities($this->icon, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" class="icon"/>';
	} // }}}
}

class QuicktagSeparator extends Quicktag
{
	function __construct() // {{{
	{
		$this->setWysiwygToken('-');
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}

	function getWikiHtml() // {{{
	{
		return '|';
	} // }}}
}

class QuicktagInline extends Quicktag
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'bold':
			$label = tra('Bold');
			$icon = tra('pics/icons/text_bold.png');
			$wysiwyg = 'Bold';
			$syntax = '__text__';
			break;
		case 'italic':
			$label = tra('Italic');
			$icon = tra('pics/icons/text_italic.png');
			$wysiwyg = 'Italic';
			$syntax = "''text''";
			break;
		case 'strike':
			$label = tra('Strikethrough');
			$icon = tra('pics/icons/text_strikethrough.png');
			$wysiwyg = 'StrikeThrough';
			$syntax = '--text--';
			break;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
			->setIcon( $icon )
			->setSyntax( $syntax );
		
		return $tag;
	} // }}}

	protected function setSyntax( $syntax ) // {{{
	{
		$this->syntax = $syntax;

		return $this;
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}

	function getWikiHtml() // {{{
	{
		return '<a href="javascript:insertAt(\'editwiki\', \'' . htmlentities($this->syntax, ENT_QUOTES, 'UTF-8') . '\')" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '">' . $this->getIconHtml() . '</a>';
	} // }}}
}

class QuicktagBlock extends QuicktagInline // Will change in the future
{
	protected $syntax;

	public static function fromName( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'center':
			$label = tra('Align Center');
			$icon = tra('pics/icons/text_align_center.png');
			$wysiwyg = 'JustifyCenter';
			$syntax = "::text::";
			break;
		}

		$tag = new self;
		$tag->setLabel( $label )
			->setWysiwygToken( $wysiwyg )
			->setIcon( $icon )
			->setSyntax( $syntax );
		
		return $tag;
	} // }}}
}

class QuicktagFullscreen extends Quicktag
{
	function __construct() // {{{
	{
		$this->setLabel( tra('Full Screen Edit') )
			->setIcon( 'pics/icons/application_get.png' )
			->setWysiwygToken( 'FitWindow' );
	} // }}}

	function isAccessible() // {{{
	{
		return true;
	} // }}}

	function getWikiHtml() // {{{
	{
		$name = 'zoom';
		if( isset($_REQUEST['zoom']) )
			$name = 'preview';
		return '<input type="image" name="'.$name.'" alt="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->label, ENT_QUOTES, 'UTF-8') . '" value="wiki_edit" onclick="needToConfirm=false;" title="" class="icon" src="' . htmlentities($this->icon, ENT_QUOTES, 'UTF-8') . '"/>';
	} // }}}
}

class QuicktagsList
{
	private $lines = array();

	private function __construct() {}
	
	public static function fromPreference( $section ) // {{{
	{
		global $tikilib;

		$global = $tikilib->get_preference( 'toolbar_global' );
		$local = $tikilib->get_preference( 'toolbar_'.$section, $global );

		return self::fromPreferenceString( $local );
	} // }}}

	public static function fromPreferenceString( $string ) // {{{
	{
		$list = new self;

		$string = preg_replace( '/\s+/', '', $string );

		foreach( explode( '/', $string ) as $line ) {
			$list->addLine( explode( ',', $line ) );
		}

		return $list;
	} // }}}

	private function addLine( array $tags ) // {{{
	{
		$elements = array();

		foreach( $tags as $tagName ) {
			if( ( $tag = $this->getTag( $tagName ) ) 
				&& $tag->isAccessible() ) {
				$elements[] = $tag;
			}
		}

		if( count( $elements ) )
			$this->lines[] = $elements;
	} // }}}

	private function getTag( $tagName ) // {{{
	{
		switch( $tagName ) {
		case 'bold':
		case 'italic':
		case 'strike':
			return QuicktagInline::fromName( $tagName );
		case 'center':
			return QuicktagBlock::fromName( $tagName );
		case 'fullscreen':
			return new QuicktagFullscreen;
		case '-':
			return new QuicktagSeparator;
		}
	} // }}}

	function getWysiwygArray() // {{{
	{
		$lines = array();
		foreach( $this->lines as $line ) {
			$out = array();
			foreach( $line as $tag ) {
				if( $token = $tag->getWysiwygToken() )
					$out[] = $token;
			}

			if( count($out) ) 
				$lines[] = array($out);
		}

		return $lines;
	} // }}}

	function getWikiHtml() // {{{
	{
		$html = '';

		foreach( $this->lines as $line ) {
			$lineHtml = '';

			foreach( $line as $tag ) {
				$lineHtml .= $tag->getWikiHtml();
			}

			if( ! empty($lineHtml) ) {
				$html .= "<div>$lineHtml</div>";
			}
		}

		return $html;
	} // }}}
}

// NOTE : Everything beyond this line is in the process of becoming obsolete

class QuickTagsLib extends TikiLib {
	function QuickTagsLib($db) {
		$this->TikiLib($db);
	}

	function list_quicktags($offset, $maxRecords, $sort_mode, $find, $category=null) {
		
		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`taglabel` like ?)";
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		if ( !empty($category) ) {
			if ( is_array($category) ) {
				$mid .= ( $mid ? ' and ' : ' where ' ) . '(';
				foreach ( $category as $k => $v ) {
					if ( $k > 0 ) $mid .= ' OR';
					$mid .= ' `tagcategory` like ?';
					$bindvars[] = $v;
				}
				$mid .= ')';
			} else {
				$mid .= ( $mid ? ' and ' : ' where ' ) . '(`tagcategory` like ?)';
				$bindvars[] = $category;
			}
		}

		$query = "select * from `tiki_quicktags` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_quicktags` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['iconpath'] = $res['tagicon'];
			if (!is_file($res['tagicon'])) 
                            $res['tagicon'] = 'pics/icons/page_white_code.png';
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_quicktag($tagId, $taglabel, $taginsert, $tagicon, $tagcategory) {
		if ($tagId) {
			$bindvars=array($taglabel, $taginsert, $tagicon, $tagcategory, $tagId);
			$query = "update `tiki_quicktags` set `taglabel`=?,`taginsert`=?,`tagicon`=?,`tagcategory`=? where `tagId`=?";
			$result = $this->query($query,$bindvars);
		} else {
			$bindvars=array($taglabel, $taginsert, $tagicon, $tagcategory);
			$query = "delete from `tiki_quicktags` where `taglabel`=? and `taginsert`=? and `tagicon`=? and `tagcategory`=? ";
			$result = $this->query($query,$bindvars);
			$query = "insert into `tiki_quicktags`(`taglabel`,`taginsert`,`tagicon`,`tagcategory`) values(?,?,?,?)";
			$result = $this->query($query,$bindvars);
		}
		return true;
	}

	function remove_quicktag($tagId) {
		$query = "delete from `tiki_quicktags` where `tagId`=?";
		$this->query($query,array($tagId));
		return true;
	}

	function get_quicktag($tagId) {
		$query = "select * from `tiki_quicktags` where `tagId`=?";
		$result = $this->query($query,array($tagId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function list_icons($p) {
          $back = array();
		foreach($p as $path) {
			$handle = opendir($path);
			while ($file = readdir($handle)) {
				if (((strtolower(substr($file, -4, 4)) == ".gif") 
                                      or (strtolower(substr($file, -4, 4)) == ".png")) 
                                  and (ereg("^[-_a-zA-Z0-9\.]*$", $file))) 
                                {
				  $back[] = $path .'/'  .$file;
				}
			}
		}
          return $back;
	}

}
global $dbTiki;
$quicktagslib = new QuickTagsLib($dbTiki);
?>
