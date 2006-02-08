<?php
class DestinationDownload extends DestinationHTTP {
  function DestinationDownload($filename) {
    $this->DestinationHTTP($filename);
  }

  function headers($content_type) {
    return array(
                 "Content-Disposition: attachment; filename=".$this->filename_escape($this->get_filename()).".".$content_type->default_extension,
                 "Content-Transfer-Encoding: binary",
                 "Cache-Control: private"
                 );
  }
}
?>