#!/bin/bash

# Define variables
PACKAGE_NAME="semaphore-ogs"
VERSION="1.0.0"
RELEASE="7"
TARBALL="${PACKAGE_NAME}-${VERSION}.tar.gz"
SPEC_FILE="semaphore.spec"

# Navigate to the rpmbuilder directory
cd ~/repos/semaphore-ogs/rpmbuilder

# Create a temporary directory for the tarball
mkdir -p ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}
mkdir -p ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/usr/bin/
mkdir -p ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/etc/
mkdir -p ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/usr/lib/systemd/system/

# Copy the necessary files to the temporary directory
cp ~/repos/semaphore-ogs/bin/semaphore ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/usr/bin/
cp ~/repos/semaphore-ogs/bin/semaphore-setup ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/usr/bin/
cp ~/repos/semaphore-ogs/config.json ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/etc/semaphore.json
cp ~/repos/semaphore-ogs/bin/semaphore.service ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/usr/lib/systemd/system/

# Add build-id to the binary using objcopy
#objcopy --build-id ~/repos/semaphore-ogs/rpmbuilder/tmp/${PACKAGE_NAME}-${VERSION}/semaphore

# Create the tarball
tar czvf $TARBALL -C ~/repos/semaphore-ogs/rpmbuilder/tmp ${PACKAGE_NAME}-${VERSION}
ls -r ~/repos/semaphore-ogs/rpmbuilder/tmp

# Clean up the temporary directory
rm -rf ~/repos/semaphore-ogs/rpmbuilder/tmp

# Create the RPM build directories
mkdir -p ~/rpmbuild/{BUILD,RPMS,SOURCES,SPECS,SRPMS}

# Move the tarball and spec file to the SOURCES and SPECS directories
mv $TARBALL ~/rpmbuild/SOURCES/
cp $SPEC_FILE ~/rpmbuild/SPECS/

# Build the RPM
rpmbuild -ba ~/rpmbuild/SPECS/$SPEC_FILE

# Output the location of the built RPM
echo "RPM built successfully. You can find it in ~/rpmbuild/RPMS/x86_64/"
ls ~/rpmbuild/RPMS/x86_64/
