<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/box.table.section.php,v 1.1 2008-01-15 09:20:28 mose Exp $

class TableSectionBox extends GenericContainerBox {
  function &create(&$root) {
    $box =& new TableSectionBox($root);
    return $box;
  }
  
  function TableSectionBox(&$root) {
    $this->GenericContainerBox();

    // Automatically create at least one table row
    if (count($this->content) == 0) {
      $this->content[] =& new TableRowBox($root);
    }

    // Parse table contents
    $child = $root->first_child();
    while ($child) {
      $child_box =& create_pdf_box($child);
      $this->add_child($child_box);
      $child = $child->next_sibling();
    };
  }

  // Overrides default 'add_child' in GenericBox
  function add_child(&$item) {
    // Check if we're trying to add table cell to current table directly, without any table-rows
    if (!is_a($item,"TableRowBox")) {
      // Add cell to the last row
      $last_row =& $this->content[count($this->content)-1];
      $last_row->add_child($item);
    } else {
      // If previous row is empty, remove it (get rid of automatically generated table row in constructor)
      if (count($this->content[count($this->content)-1]->content) == 0) {
        array_pop($this->content);
      }
      
      // Just add passed row 
      $this->content[] =& $item;
    };
  }
}
?>