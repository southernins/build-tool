# Build Tool
Laravel Artisan Command to Build deployable packages for 
AWS Elastic Beanstalk Hosted Projects


## Requirements

- Laravel 5.5+
- Git must be installed `sudo apt-get install git`
- zip must be installed `sudo apt-get install zip`
- NPM and Node ( version??? )


## Installation
Add or Modify the `repositories` array of the composer.json file to
have the following.

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/southernins/build-tool"
        }
    ],
    
Add the following to require-dev of composer.json

`"southernins/build-tool": "0.0.*"`

Then run `composer update` or `composer install` as needed.


    php artisan list
should then show the following dcommands
    
    build 
    build:dev
    build:prod
    
## Usage

once installed a use the following based on the required build type.

### Production 

    php artisan build:prod
    
which is a shortcut for
    
    php artisan build --env="production"
    
    
### Development/Staging

    php artisan build:dev
    
which is a shortcut for

    php artisan build --env="dev"
    
    
additionally you can build to any env file found in /environments 
folder with the following

    php artisan build --env="file-name"
    
where `.env.file-name` is found in /environments


