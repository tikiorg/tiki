<?php
include_once('lib/fckeditor/fckeditor/fckeditor.php');

class TikiFCK {
  var $InstanceName;
  var $BasePath;
  var $Width;
  var $Height;
  var $ToolbarSet;
  var $Value;
  var $Config;
	var $ConfigString;
	var $Compat;

	function TikiFCK($InstanceName) {
		$this->InstanceName = $InstanceName;
		$this->id           = '42';
		$this->BasePath     = 'lib/fckeditor/';
		$this->Width        = '100%';
		$this->Height       = '500px';
		$this->ToolbarSet   = 'Tiki';
		$this->File         = 'fckeditor.html';
		$this->LinkFile     = '';
		$this->Meat         = '';
		$this->HtmlMeat     = '';
		$this->Config       = array();
		$this->ConfigString = '';
		$this->Compat       = false;
	}
	
	function CreateHtml() {
		global $smarty;
		$this->HtmlMeat = htmlspecialchars($this->Meat);
		if (FCKeditor_IsCompatibleBrowser()) {
			if (isset($_GET['fcksource']) && $_GET['fcksource'] == "true") {
				$this->File = 'fckeditor.original.html';
			}
			$this->ConfigString = $this->GetConfigFieldString();
			$this->id = preg_replace('/[^a-zA-Z0-9]/','',$this->InstanceName);
			$this->LinkFile = $this->BasePath.'editor/'.$this->File.'?InstanceName='.$this->id.'&amp;Toolbar='.$this->ToolbarSet.'&amp;'.$this->ConfigString;
			$this->Compat = true;
		}
		$smarty->assign('fck',$this);
		return $smarty->fetch('fck-edit.tpl');
	}

  function GetConfigFieldString() {
    $sParams = '';
    $bFirst = true;
    foreach ($this->Config as $sKey => $sValue) {
      if ($bFirst == false) {
        $sParams .= '&amp;';
      } else {
        $bFirst = false;
			}
      if ($sValue === true) {
        $sParams .= $this->EncodeConfig($sKey).'=true';
      } elseif ( $sValue === false ) {
        $sParams.= $this->EncodeConfig($sKey).'=false' ;
      } else {
        $sParams.= $this->EncodeConfig($sKey).'='.$this->EncodeConfig($sValue);
			}
    }
    return $sParams;
  }

  function EncodeConfig($valueToEncode) {
    $chars = array('&'=>'%26', '='=>'%3D', '"'=>'%22');
    return strtr($valueToEncode, $chars);
  }
}
?>
