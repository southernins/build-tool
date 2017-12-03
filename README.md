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

## Usage

once installed a build can be created by running the following.

### Production 

    php artisan build:prod
    
    php artisan build --env="production"
    
    
### Development/Staging

    php artisan build:dev
    php artisan build --env="dev"
    
    
additionally you can build to any env file found in /environments 
folder with the following

    php artisan build --env="file-name"
    
where `.env.file-name` is found in /environments


