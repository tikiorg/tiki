use strict;
use warnings;

open FILE, "symbol.map";
while (<FILE>) {
  my @data = split(/ /, $_);
  print "0x".(substr($data[1],2))." 0x".(substr($data[0],1))."\n";
};
