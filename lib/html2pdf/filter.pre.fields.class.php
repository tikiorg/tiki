<?php
class PreTreeFilterHTML2PSFields extends PreTreeFilter {
  var $filename;
  var $filesize;
  var $timestamp;

  function PreTreeFilterHTML2PSFields($filename, $filesize, $timestamp) {
    $this->filename  = $filename;
    $this->filesize  = $filesize;
    $this->timestamp = $timestamp;
  }

  function process(&$tree) {
    if (is_a($tree, 'TextBox')) {
      switch ($tree->word) {
      case '##PAGE##':
        $parent =& $tree->parent;
        $field  = BoxTextFieldPageNo::from_box($tree);

        $parent->insertBefore($field, $tree);

        $parent->remove($tree);
        break;
      case '##PAGES##':
        $parent =& $tree->parent;
        $field  = BoxTextFieldPages::from_box($tree);
        $parent->insertBefore($field, $tree);
        $parent->remove($tree);
        break;
      case '##FILENAME##':
        $tree->word = $this->filename;
        break;
      case '##FILESIZE##':
        $tree->word = $this->filesize;
        break;
      case '##TIMESTAMP##':
        $tree->word = $this->timestamp;
        break;
      };
    } elseif (is_a($tree, 'GenericContainerBox')) {
      for ($i=0; $i<count($tree->content); $i++) {
        $this->process($tree->content[$i]);
      };
    };
  }
}
?>