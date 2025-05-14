# ManiyaTech Crud module for Magento 2

This Magento 2 module enables full CRUD (Create, Read, Update, Delete) operations for managing student data. It includes a custom frontend registration form, accessible via a “Student Form” link in the store footer. The form is designed to work seamlessly with both the Luma and Hyvä themes.

On the backend, the module features a dedicated admin grid to display, filter, and manage all student records. This allows store administrators to easily monitor and control student submissions with built-in sorting, editing, and status options. The module is ideal for educational websites or stores needing structured student data collection and management.

## How to install ManiyaTech_Crud module

### Composer Installation

Run the following command in Magento 2 root directory to install ManiyaTech_Crud module via composer.

#### Install

```
composer require maniyatech/magento2-crud
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```

#### Update

```
composer update maniyatech/magento2-crud
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```

Run below command if your store is in the production mode:

```
php bin/magento setup:di:compile
```

### Manual Installation

If you prefer to install this module manually, kindly follow the steps described below - 

- Download the latest version [here](https://github.com/maniyatech/magento2-crud/archive/refs/heads/main.zip) 
- Create a folder path like this `app/code/ManiyaTech/Crud` and extract the `main.zip` file into it.
- Navigate to Magento root directory and execute the below commands.

```
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy -f
```
