#jxAdminLog#

OXID eShop Admin Extension for Displaying Admin Log Table

## Preparation ##

Open config.inc.php and search for 
```
$this->blLogChangesInAdmin = false;
```
and set to True. This enables the logging of admin actions.

## Setup ##

1. Unzip the complete file with all the folder structures and upload the content of the folder copy_this to the root folder of your shop.
2. After this navigate in the admin backend of the shop to _Extensions_ - _Modules_. Select the module _jxAdminLog_ and click on `Activate`.

  
## Screenshots ##

#### Object Log on most of the objects ####
![Object History Log](https://github.com/job963/jxAdminLog/raw/master/docs/img/adminlog_object_history.png)

#### Full Log Report ####
![Full Log Report](https://github.com/job963/jxAdminLog/raw/master/docs/img/adminlog_full_history.png)
