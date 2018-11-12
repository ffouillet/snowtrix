# SnowTrix
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/15b979478e99404a81e2ce07ffa4e565)](https://www.codacy.com/app/ffouillet/snowtrix?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ffouillet/snowtrix&amp;utm_campaign=Badge_Grade)  

Communautary snowboard tricks website  
Created with Symfony 3.4  
[Try the project here](http://snowtrix.ffouillet.fr)

## Installation
1.  Clone (or download) the repository on your local machine. Run this command to clone the repository :  ```git clone https://github.com/ffouillet/snowtrix.git ```

2.  Install project dependencies by running following command in the project directory : ```composer install``` . It will ask you for parameters (which are registered in parameters.yml.dist), leaves at default or set your own.

3.  Create the database and update the database schema by running following commands (always in the project directory) :   
```php bin/console doctrine:database:create```  
```php bin/console doctrine:schema:create```  

4.  Your project is ready, open your browser and go to the server url pointing to your project.

## Demo User and Tricks
You can add a Demo User and some tricks to test the project (beware, tricks come with no photo or videos or comments, you'll have to add it on your own via Trick Edit action).  
Run the following command in the project directory to add 1 User (username : Demo, password : ocdemo), 2 Tricks Groups (Slides and Grabs), and 10 Tricks associated with Tricks Groups :  
``` php bin/console doctrine:fixtures:load ```

## Demo mode
The project have a demo mode, in which a logged in user can only run few action on the project (in order not to break it).  
You can enable it and edit demo parameters in app/cofig/parameters.yml.  
Demo parameters are :  
*   demo_mode_enabled (false/true)
*   demo_mode_disabled_actions (array of actions that cannot be done when demo mode is enabled with associated method (GET, POST, etc.. or ANY))
*   demo_mode_disabled_actions_route_to_redirect_to (route name where user will be redirected to if he tries to run a forbidden action by the demo mode)

## Unit and functional tests
Run unit and functional tests by executing following commands at the project root :  
``` php bin/console doctrine:schema:create --env=test``` (Create test environment DB)  
``` php bin/console doctrine:schema:update --env=test```  
``` php bin/console doctrine:fixtures:load --env=test```  
``` ./vendor/bin/simple-phpunit ```
