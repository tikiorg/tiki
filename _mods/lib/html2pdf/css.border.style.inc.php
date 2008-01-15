<?php

class CSSBorderStyle {
  function value2ps($value) {
    switch ($value) {
    case BS_SOLID:
      return "/solid";
    case BS_DASHED:
      return "/dashed";
    case BS_DOTTED:
      return "/dotted";
    case BS_DOUBLE:
      return "/double";
    case BS_INSET:
      return "/inset";
    case BS_OUTSET:
      return "/outset";
    case BS_GROOVE:
      return "/groove";
    case BS_RIDGE:
      return "/ridge";
    case BS_NONE:
    default:
      return "/none";
    };
  }
}
?>