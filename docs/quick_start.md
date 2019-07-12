# Getting started

I'm working on develop a scaffold tool to bring up the Slimer initiation simpler than clone the source code from [cw1427/Slimer](https://github.com/cw1427/Slimer). But right now please follow below guidance to have a tour.

## Start tour from here

I'm assuging you have a total PHP5.6.* interpreter env ready.

- Step 1
    
  ` git clone git@github.com:cw1427/Slimer.git`

- Step 2

 Cd to the project root folder, run below command to install the dependencies via Composer

 `php composer.phar install`

 In case of the composer.lock been out-of-date, recommend to run composer update to force upgrade

 `php composer.phar update`

- Step 3 (optional)

  If you have your local composer registry server, please add below code in the composer.json file
        "repositories": [
        {
            "type": "composer",
            "url": "https://<your private composer registry>/packagist"
        },
        {
            "type": "composer",
            "url": "https://<your private composer registry>/packagistremote/"
        },
		{
			"packagist": false
        }
        ],

  If your composer registry server need authentication, please update the account info in the auth.json file under the project path.
        {
        "http-basic": {
            "<your private composer url>": {
            "username": "composer",
            "password": "<the account password>"
            }
        } 
        }

- Step 4

  Run the command to check if evething gos well
  `php index.php cmd --list`




> Basick project structure

