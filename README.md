# Customer Data Importer Module
This module allows you to import customer data from different Profiles (CSV, JSON, etc.) into your Magento store.

## Installation
1. Unzip the module's zip file into the `app/code/` directory.
2. Enable the module by running the following command:
php bin/magento module:enable Sharika_CustomerImport

3. Apply database updates:
php bin/magento setup:upgrade

4. Flush the cache:
php bin/magento cache:flush

## Usage and Commands

### CLI Command - JSON Profile
1. Place the `sample.json` file inside the `var/import/` directory.

2. Run the import command:
php bin/magento customer:import json var/import/sample.json

### CLI Command - CSV Profile
1. Place the `sample.csv` file inside the `var/import/` directory.

2. Run the import command:
php bin/magento customer:import csv var/import/sample.csv

After running the customer import script, you'll need to reindex the Customer Grid indexer:
php bin/magento indexer:reindex customer_grid

