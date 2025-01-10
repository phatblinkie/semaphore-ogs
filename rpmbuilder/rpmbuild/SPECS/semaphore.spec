Name:           semaphore
Version:        1.0.0
Release:        1%{?dist}
Summary:        Semaphore binary and configuration

License:        MIT
URL:            https://semaphore
Source0:        %{name}-%{version}.tar.gz

BuildArch:      x86_64

%description
Semaphore binary and configuration.

%prep
%setup -q

%build

%install
mkdir -p %{buildroot}/usr/local/bin
mkdir -p %{buildroot}/etc/semaphore
install -m 0755 bin/semaphore %{buildroot}/usr/local/bin/semaphore
install -m 0644 config.json %{buildroot}/etc/semaphore/config.json

%files
/usr/local/bin/semaphore
/etc/semaphore/config.json

%changelog
* Thu Oct 12 2023 Your Name <your.email@example.com> - 1.0.0-1
- Initial package
