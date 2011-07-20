#!/usr/bin/perl
# (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id$

# finds all the style and class definitions in tpl and php files
#
# calles by findstyledef.sh
#
# ohertel@tw.o

use strict;
use warnings;
use Cwd;

use File::Find;

my $root=$ARGV[0];
my $file_pattern = "^(.*)\.(tpl|php)\$";

find(\&d, $root);

sub d {

  my $file = $File::Find::name;

  $file =~ s#/#\\#g;

  return unless -f $file;
  return unless $file =~ /$file_pattern/;

#  print $file."\n";

  open F, $file or print "couldn't open $file\n" && return;
  while (<F>) {
    if (my ($found) = m/\b((class|style)\s*=\w*[\\]*[\"]{1}.*?[\\]*[\"]{1})/oi) {
#      print "$file: $found\n";
      print "$found\n";
    }
    if (my ($found) = m/\b((class|style)\s*=\w*[\\]*[']{1}.*?[\\]*[']{1})/oi) {
#      print "$file: $found\n";
      print "$found\n";
    }
  }

  close F;
}
