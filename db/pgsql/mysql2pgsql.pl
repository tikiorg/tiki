#!/usr/bin/perl -w
# $Id: mysql2pgsql.pl,v 1.1 2003-07-15 09:53:28 rossta Exp $

use strict;
$| = 1;

my $oldfh = select(STDOUT);
select($oldfh);

my $outputfile = $ARGV[1] || "output.sql";

my $table_substitutions_re = "";
my %table_substitutions = ();

open(IN, "<$ARGV[0]") || die("couldn't open input: $!");

print "Writing $outputfile ...";
open(OUTPUT, ">$outputfile") || die("coudn't open: $!");

print OUTPUT '--$' . 'Id$' . "\n";
print OUTPUT "-- Dump of $ARGV[0]\n";

my @sequences;
my %sequence_hash;
my %keys;

my $output_count = 0;
my $table_name;
while (my $sql = <IN>) {
    if ($output_count++ % 50 == 0) {
#		print ".";
    }

	if ($sql =~ /^INSERT/) {
	    print OUTPUT $sql;
	    next;
	}
	
    # Convert '#' comments to '--'
    $sql =~ s/^#/--/mg;

    if ($sql =~ /create table (\S+)/i) {
		$table_name = $1;
	}

	# Clean up the numeric types
	$sql =~ s/\b(tiny|medium|big)?int\(\d+\)/integer/;
	$sql =~ s/ZEROFILL//;
	$sql =~ s/double\(\d+(,\d+)?\)/float8/;

	# Clean up the date types
	$sql =~ s/\bdate\b/datetime/;
	$sql =~ s/\btime\b/datetime/;
	$sql =~ s/\btimestamp\(\d+\)/datetime not null default now()/;

	$sql =~ s/\bblob\b/text/;
	$sql =~ s/\blongblob\b/text/;
	$sql =~ s/\btinyblob\b/text/;

	# Convert auto-increment primary keys to use sequences
	if ($sql =~ /\b(\S+) integer .*auto_increment/i) {
	    my $column_name = $1;
	    my $sequence_name = substr($table_name, 0, 27) . '_seq';
	    my $sequence_save = $sequence_name;
	    my $i = 2;
	    while ($sequence_hash{$sequence_name}) {
	    	$sequence_name = $sequence_save . $i++;
	    }
	    $sequence_hash{$sequence_name} = 1;
	    $sql =~ s/auto_increment/default nextval('$sequence_name')/;
	    push(@sequences, [$sequence_name, $table_name, $column_name]);
	}

	# Convert enums
	$sql =~ s/\S+ enum\([^\)]+\)/convert_enums($&)/e;

	if ($sql !~ /PRIMARY/ && $sql =~ /KEY\s+\S+\s+\((.*)\)/) {
		my $key = $1;
		while ($key =~ /(.*)\(\d+\)(.*)/) {
			$key = $1 . $2;
		}
		my @keys = split(',', $key);
		if (@keys) {
			foreach my $key (@keys) {
				$keys{$table_name}{$key} = 1;
			}
		} else {
			$keys{$table_name}{$key} = 1;
		}
	}
  
	if ($sql =~ /(.*KEY.*\)),$/i) {
		$sql = $1."\n";
	}

	if ($sql =~ /KEY/i) {
		while ($sql =~ /(.*)\(\d+\)(.*)/) {
			$sql = $1 . $2;
		}
	}

	# Mysql dumps have: UNIQUE name (name)
	# postgres expects just UNIQUE (name)
	$sql =~ s/UNIQUE KEY \S+ \(/,UNIQUE \(/;

 	$sql =~ s/FULLTEXT//;

	# FIXME: add a --keys-to-indexes option or something
	$sql =~ s/^\s*KEY .*$//mi;
	$sql =~ s/,\s*\);$/\n\);/;

	# Remove default values for timestamps
	$sql =~ s/DEFAULT '0000-00-00( 00:00:00)?'//g;

	# Convert char to varchar (postgres space-pads chars)
	$sql =~ s/\bchar\(/varchar\(/;

 	$sql =~ s/TYPE=MyISAM//i;
 
 	$sql =~ s/\buser\b/"user"/i;
 	$sql =~ s/'"user"'/'user'/i;
 	$sql =~ s/\bpublic\b/"public"/i;
 	$sql =~ s/'"public"'/'public'/i;
 	$sql =~ s/\bend\b/"end"/i;
 	$sql =~ s/'"end"'/'end'/i;
    
    next unless length($sql) > 1;
    
    print OUTPUT $sql;
}

#
# Handle all sequences
#

print OUTPUT "\n-- Sequences\n\n";

foreach my $seqref (@sequences) {
    my ($seq_name, $tbl_name, $col_name) = @$seqref;
	print OUTPUT "select setval('$seq_name', (select max($col_name) from $tbl_name));\n";
}

print OUTPUT "\n-- Indexes\n\n";

foreach $table_name (sort keys %keys) {
	my $hash = $keys{$table_name};
	foreach my $key (sort keys %$hash) {
		my $k = $key;
 		$k = '"user"' if $k eq 'user';
 		$k = '"public"' if $k eq 'public';
 		$k = '"end"' if $k eq 'end';
		print OUTPUT "CREATE INDEX ${table_name}_$key ON $table_name ($k);\n";
	}
}

print OUTPUT "\n-- EOF\n\n";

print "done\n";

sub convert_enums {
    my($sql) = @_;
    
    if ($sql =~ /(\S+) enum\(([^\)]+)\)/) {
	my $column_name = $1;
	my $enum_values_orig = $2;
	my $enum_values = $enum_values_orig;
	$enum_values =~ s/^'(.*)'$/$1/;
	my @enum_values = split("','", $enum_values);
	my $longest_enum = 0;
	map {
	    $longest_enum = length($_) if length($_) > $longest_enum;
	} @enum_values;
	$sql =~ s/$column_name enum\([^\)]+\)/$column_name varchar($longest_enum) check ($column_name in ($enum_values_orig))/;
    }
    
    return $sql;
}
