# Command to build: rpmbuild -ba --target noarch tikiwiki.spec
# $Id$

%define name tikiwiki
%define version 1.9.DR4
%define release 1

Summary: A PHP-based CMS/Groupware web application with a full Wiki environment
Name: %{name}
Version: %{version}
Release: %{release}
Copyright: LGPL
URL: http://www.tikiwiki.org
Packager: Damian Parker <damian@damosoft.co.uk>
AutoReqProv: no
Requires: php 
Group: Applications/Internet
Source: http://prdownloads.sourceforge.net/%{name}/%{name}-%{version}.tar.gz
BuildRoot: %{_tmppath}/%{name}-root
Prefix: /var/www/html
Vendor: The Tikiwiki Community

%description
Tikiwiki is an open source CMS/Groupware web application which provides a full Wiki environment, as well as Articles, Sections/Categories, User/Group Management (including optional LDAP), Polls and Quizzes, File and Image Galleries, Forums, Weblogs, Calendars, Chat and much more.

%prep

%build

%install
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/var/www/html
cd $RPM_BUILD_ROOT/var/www/html
tar xvzf $RPM_SOURCE_DIR/%{name}-%{version}.tar.gz
mv %{name}-%{version} tiki-%{version}
# Change file and directory permissions
cd tiki-%{version}
find . -name "*.php" -exec chmod 644 {} \;
find . -name "*.sql" -exec chmod 644 {} \;
chmod 755 setup.sh
./setup.sh apache apache
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 664 robots.txt tiki-install.php
# Remove unneeded files
rm -rf templates_c/*
rm -f modules/cache/*.cache
find . -name "CVS" -type d -print|xargs rm -rf
find . -name ".cvsignore" -exec rm -f {} \;

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

%preun
# Remove unneeded files
rm -rf /var/www/html/tiki-%{version}/templates_c/*
rm -f /var/www/html/tiki-%{version}/modules/cache/*.cache

%files
%defattr(-,root,apache)
%config /var/www/html/tiki-%{version}/db/tiki-db.php
#%doc /var/www/html/tiki-%{version}/README
/var/www/html/tiki-%{version}

%changelog
