%global debug_package %{nil}
%global _missing_build_ids_terminate_build 0
%global _dwz_low_mem_die_limit 0

Name:           semaphore-ogs
Version:        1.0.0
Release:        9%{?dist}
Summary:        Customized Semaphore and configuration

License:        MIT
URL:            https://semaphore
Source0:        %{name}-%{version}.tar.gz

#BuildRequires:  golang
#BuildRequires:  nodejs
#BuildRequires:  nodejs-npm
#BuildRequires:  go-task
#BuildRequires:  git
#BuildRequires:  systemd-rpm-macros

Requires:       ansible
Requires:	nginx
Requires:	mariadb-server
Requires:	php-mysqlnd
Requires:	php-fpm

BuildArch:      x86_64

%description
Semaphore binary and configuration and system service file.

%prep
%setup -q

%build
#not releasing source builds yet, maybe one day

%install
mkdir -p %{buildroot}%{_bindir}
mkdir -p %{buildroot}%{_unitdir}
mkdir -p %{buildroot}%{_sysconfdir}

install -m 755 usr/bin/semaphore %{buildroot}%{_bindir}/semaphore
install -m 755 usr/bin/semaphore-setup %{buildroot}%{_bindir}/semaphore-setup
install -m 644 usr/lib/systemd/system/semaphore.service %{buildroot}%{_unitdir}/semaphore.service
install -m 644 etc/semaphore.json %{buildroot}%{_sysconfdir}/semaphore.json

%files
%attr(755, root, root) %{_bindir}/semaphore
%attr(755, root, root) %{_bindir}/semaphore-setup
%attr(644, root,root) %{_unitdir}/semaphore.service
%attr(644, root,root) %{_sysconfdir}/semaphore.json

%changelog
* Thu Jan 5 2025 Brian Bowen <bbowen@scholz-associates.com> - 1.0.0-1
- Initial package
* Thu Jan 12 2025 Brian Bowen <bbowen@scholz-associates.com> - 1.0.0-2
- Better logic for patch status pages
* Thu Jan 18 2025 Brian Bowen <bbowen@scholz-associates.com> - 1.0.0-3
- Implemented linux host package displays and searchs
- several visual fixes
