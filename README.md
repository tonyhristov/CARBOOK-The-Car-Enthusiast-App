CARBOOK
=========

This is project for car enthusiast that want to share there cars with others.
You can share your vehicle, no matter if it is a bike, 
a supercar or a tractor.
Why I made this, well we all want takes pictures of our cars,
because we know that they are prettier than us 
and we want to share them with other people.

Made with
=========
Framework - [Symfony - 3.4](https://symfony.com/)

ORM - [Doctrine](https://www.doctrine-project.org/) 

Database - [MySql](https://www.mysql.com/)

To Run
=======
To run the app you need to write in the terminal the following command:

```php bin/console server:run```

Architecture
============
```
|   .gitignore
|   Architecture.txt
|   composer.json
|   composer.lock
|   phpunit.xml.dist
|   README.md
|   
+---app
|   |   .htaccess
|   |   AppCache.php
|   |   AppKernel.php
|   |   
|   +---config
|   |       config.yml
|   |       config_dev.yml
|   |       config_prod.yml
|   |       config_test.yml
|   |       parameters.yml
|   |       parameters.yml.dist
|   |       routing.yml
|   |       routing_dev.yml
|   |       security.yml
|   |       services.yml
|   |       
|   \---Resources
|       \---views
|           |   base.html.twig
|           |   
|           +---about
|           |       about.html.twig
|           |       
|           +---contact_me
|           |       contact.html.twig
|           |       
|           +---current_car
|           |       all_current_cars.htm.twig
|           |       create_current_cars.htm.twig
|           |       edit_current_cars.htm.twig
|           |       
|           +---default
|           |       index.html.twig
|           |       
|           +---posts
|           |       create_post.html.twig
|           |       edit_post.html.twig
|           |       my_posts.html.twig
|           |       view_post.html.twig
|           |       
|           +---previous_car
|           |       create.html.twig
|           |       edit.html.twig
|           |       previous_cars.html.twig
|           |       
|           +---security
|           |       login.html.twig
|           |       
|           \---users
|                   edit_my_password.html.twig
|                   edit_my_profile.html.twig
|                   edit_my_username.html.twig
|                   my_profile.html.twig
|                   register.html.twig
|                   view_profile.html.twig
|                   
+---bin
|       console
|       symfony_requirements
|       
+---src
|   |   .htaccess
|   |   
|   \---AppBundle
|       |   AppBundle.php
|       |   
|       +---Controller
|       |       CurrentCarController.php
|       |       DefaultController.php
|       |       PostController.php
|       |       PreviousCarController.php
|       |       SecurityController.php
|       |       UserController.php
|       |       
|       +---Entity
|       |       CurrentCar.php
|       |       Post.php
|       |       PreviousCar.php
|       |       User.php
|       |       
|       +---Form
|       |       CurrentCarType.php
|       |       PostType.php
|       |       PreviousCarType.php
|       |       UserType.php
|       |       
|       +---Repository
|       |       CurrentCarRepository.php
|       |       PostRepository.php
|       |       PreviousCarRepository.php
|       |       UserRepository.php
|       |       
|       \---Service
|           +---CurrentCars
|           |       CurrentCarsService.php
|           |       CurrentCarsServiceInterface.php
|           |       
|           +---Encryption
|           |       ArgonEncryption.php
|           |       BCryptService.php
|           |       EncryptionServiceInterface.php
|           |       
|           +---Posts
|           |       PostService.php
|           |       PostServiceInterface.php
|           |       
|           +---PreviousCars
|           |       PreviousCarService.php
|           |       PreviousCarServiceInterface.php
|           |       
|           \---Users
|                   UserService.php
|                   UserServiceInterface.php
|                   
+---tests
|   \---AppBundle
|       \---Controller
|               DefaultControllerTest.php
|               
\---web
    |   .htaccess
    |   app.php
    |   apple-touch-icon.png
    |   app_dev.php
    |   config.php
    |   currentCars.jpg
    |   favicon.ico
    |   previousCars.jpg
    |   robots.txt
    |   
    +---css
    |       bootstrap-datetimepicker.min.css
    |       style.css
    |       
    +---js
    |       bootstrap-datetimepicker.min.js
    |       bootstrap.js
    |       jquery-3.3.1.min.js
    |       moment.min.js
    |       
    \---uploads
        \---images
            +---current_cars    
            +---posts    
            +---previous_cars 
            \---profiles
```

Made by
=======
[Antonio Hristov](https://github.com/tonyhristov)
