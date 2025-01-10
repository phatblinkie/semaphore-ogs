%global debug_package %{nil}
%global _missing_build_ids_terminate_build 0
%global _dwz_low_mem_die_limit 0

Name:           semaphore-ogs
Version:        1.0.0
Release:        1%{?dist}
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

#Requires:       ansible

BuildArch:      x86_64

%description
Semaphore binary and configuration and system service file.

%prep
%setup -q

%build
#not releasing source builds yet, maybe one day


%install
mkdir -p %{buildroot}%{_sysconfdir}/semaphore/
mkdir -p %{buildroot}%{_bindir}
mkdir -p %{buildroot}%{_unitdir}

install -m 755 usr/bin/semaphore %{buildroot}%{_bindir}/semaphore
install -m 755 usr/bin/semaphore-setup %{buildroot}%{_bindir}/semaphore-setup
install -m 644 usr/lib/systemd/system/semaphore.service %{buildroot}%{_unitdir}/semaphore.service

%files
%attr(755, root, root) %{_bindir}/semaphore
%attr(755, root, root) %{_bindir}/semaphore-setup
%attr(644, root,root) %{_sysconfdir}/semaphore/
%attr(644, root,root) %{_unitdir}/semaphore.service

%changelog
* Thu Oct 12 2023 Brian Bowen <bbowen@scholz-associates.com> - 1.0.0-1
- Initial package
