The SoftwareAgil_StarkenPro module provides integration with the Starken Pro shipping carrier, of Chile country.

# How to install this extension?
Under you root folder, run the following command lines:

- composer require softwareagil/magento-2-starkenpro
- php bin/magento setup:upgrade
- php bin/magento setup:di:compile
- php bin/magento cache:flush

# Before uninstalling this extension?
Under you root folder, run the following command lines:

- bin/magento  maintenance:enable
- bin/magento module:disable SoftwareAgil_StarkenPro

# How to uninstall this extension?
Under you root folder, run the following command lines:

- php bin/magento module:uninstall SoftwareAgil_StarkenPro

In case the uninstallation does not complete and is stuck, run the following command:
- composer remove composer softwareagil/magento-2-starkenpro