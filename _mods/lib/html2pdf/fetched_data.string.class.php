<?php
class FetcherString extends FetchedDataURL {
  var $content;
  var $headers;
  var $url;
	
  function FetchedDataURL($content) {
    $this->content = $content;
  }
  
  function get_data($dumb) {
    $obj = new FetchedDataURL($dumb);
    return $obj;
  }
	
  function get_additional_data($key) {
    return null;
  }

  function get_content() {
    return $this->content;
  }

  function set_content($data) {
    $this->content = $data;
  }
  
  function get_base_url(){
  	return true;
  }
}
?>