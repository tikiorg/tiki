#!/usr/bin/perl -w
# $Id: mysql2pgsql.pl,v 1.2 2003-07-15 20:21:26 rossta Exp $

# see http://www.xach.com/aolserver/mysql-to-postgresql.html
# and http://www.xach.com/aolserver/mysql2psql.pl

use strict;
$| = 1;

my $oldfh = select(STDOUT);
select($oldfh);

my $outputfile = $ARGV[1] || "output.sql";

my $table_substitutions_re = "";
my %table_substitutions = ();

print "Reading '$ARGV[0]'\n";

open(IN, "<$ARGV[0]") || die("Can't open '$ARGV[0]': $!");

print "Creating '$outputfile'\n";
open(OUTPUT, ">$outputfile") || die("Can't create '$outputfile': $!");

print OUTPUT '--$' . 'Id$' . "\n";
print OUTPUT "-- Dump of $ARGV[0]\n";

my @sequences;
my %sequence_hash;
my %keys;
my %fields;

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

	if ($sql =~ /^--/) {
	    print OUTPUT "\n";
	    next;
	}
	
    # Convert '#' comments to '--'
    $sql =~ s/^#/--/mg;

    if ($sql =~ /create table (\S+)/i) {
		$table_name = $1;
		print OUTPUT $sql;
		next;
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
#	    $sql =~ s/auto_increment/serial/;
	    $sql =~ s/auto_increment/default nextval('$sequence_name') unique not null/;
	    push(@sequences, [$sequence_name, $table_name, $column_name]);
	}

	if ($sql =~ /^\s*(\S+)\s+(.*)/i) {
		if ($sql !~ /KEY/ && $sql !~ /^\)/) {
			$fields{$table_name}{$1} = $2;
			$sql = '"' . $1 . '" ' . $2 . "\n";
		}
	}

	# Convert enums
	$sql =~ s/\S+ enum\([^\)]+\)/convert_enums($&)/e;
  
	if ($sql =~ /(.*KEY.*\)),$/i) {
		$sql = $1."\n";
	}

	if ($sql =~ /KEY/i) {
		while ($sql =~ /(.*)\(\d+\)(.*)/) {
			$sql = $1 . $2 . "\n";
		}
	}

	if ($sql !~ /PRIMARY/ && $sql =~ /KEY\s+\S+\s+\((.*)\)/) {
		my $key = $1;
		while ($key =~ /(.*)\(\d+\)(.*)/) {
			$key = $1 . $2;
		}
		my @k = split(',', $key);
		if (@k) {
			foreach my $key (@k) {
				$keys{$table_name}{$key} = '"' . $key . '"';
			}
		} else {
			$keys{$table_name}{$key} = '"' . $key . '"';
		}
	}

	if ($sql =~ /PRIMARY\s+KEY\s+\((.*)\)/) {
		my $key = $1;
		while ($key =~ /(.*)\(\d+\)(.*)/) {
			$key = $1 . $2;
		}
		my %pkeys;
		my @k = split(',', $key);
		
		if (@k) {
			foreach my $key (@k) {
				$pkeys{$key} = '"' . $key . '"';
			}
		} else {
			$pkeys{$key} = '"' . $key . '"';
		}

  		print OUTPUT "PRIMARY KEY (";
		print OUTPUT join ',', values %pkeys;
  		print OUTPUT ")\n";
  		next;
	}

	# Mysql dumps have: UNIQUE name (name)
	# postgres expects just UNIQUE (name)
#	$sql =~ s/UNIQUE KEY \S+ \(/,UNIQUE \(/;

#  UNIQUE KEY catname (calendarId,name)
	if ($sql =~ /UNIQUE\s+KEY\s+\S+\s+\((.*)\)/) {
		my $key = $1;
		while ($key =~ /(.*)\(\d+\)(.*)/) {
			$key = $1 . $2;
		}
		my %pkeys;
		my @k = split(',', $key);
		
		if (@k) {
			foreach my $key (@k) {
				$pkeys{$key} = '"' . $key . '"';
			}
		} else {
			$pkeys{$key} = '"' . $key . '"';
		}

  		print OUTPUT ",UNIQUE (";
		print OUTPUT join ',', values %pkeys;
  		print OUTPUT ")\n";
  		next;
	}

 	$sql =~ s/FULLTEXT//;

	# FIXME: add a --keys-to-indexes option or something
	$sql =~ s/^\s*KEY .*$//mi;
	$sql =~ s/,\s*\);$/\n\);/;

	# Remove default values for timestamps
	$sql =~ s/DEFAULT '0000-00-00( 00:00:00)?'//g;

	# Convert char to varchar (postgres space-pads chars)
	$sql =~ s/\bchar\(/varchar\(/;

 	$sql =~ s/TYPE=MyISAM//i;
    
#    next unless length($sql) > 1;
    
    print OUTPUT $sql;
}

#
# Handle all sequences
#

print OUTPUT "\n-- Create Indexes\n\n";

foreach $table_name (sort keys %keys) {
	my $hash = $keys{$table_name};
	foreach my $key (sort keys %$hash) {
		my $k = '"' . $key . '"';
		print OUTPUT "CREATE INDEX ${table_name}_$key ON $table_name ($k);\n";
	}
}

#=head

print OUTPUT "\n-- Create Sequences\n\n";

foreach my $seqref (@sequences) {
    my ($seq_name, $tbl_name, $col_name) = @$seqref;
	print OUTPUT "CREATE SEQUENCE $seq_name;\n";
}

print OUTPUT "\n-- Populate Sequences\n\n";

foreach my $seqref (@sequences) {
    my ($seq_name, $tbl_name, $col_name) = @$seqref;
	print OUTPUT "SELECT SETVAL('$seq_name', (SELECT MAX(\"$col_name\") FROM $tbl_name));\n";
}

#=cut

print OUTPUT "\n-- EOF\n\n";

close(OUTPUT) || die("Can't close to '$outputfile': $!");

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

0;
