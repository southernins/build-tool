# Build Tool
Laravel Artisan Command to Build deployable packages for 
AWS Elastic Beanstalk Hosted Projects


## Requirements

  - Laravel 7+
  - Git must be installed ( Addd to Vagrant Bootstrap.sh )  
    `sudo apt-get install git`
  - zip must be installed ( Addd to Vagrant Bootstrap.sh )  
    `sudo apt-get install zip`
  - NPM and Node ( version??? )  
    
    
    
    // Install Node manually ( or run /vagrant/ops/nodejs.sh)
    // nodejs.sh includes "n" for node version managemnet.
    curl -sL https://deb.nodesource.com/setup_8.x | sudo -E bash -
    sudo apt-get install -y nodejs



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
should then show the following command
    
    build 
    
## Usage

once installed a use the following based on the required build type.

### Production 
    
    php artisan build --env="production"
    
    
### Development/Staging

    php artisan build --env="dev"
    
    
additionally you can build to any env file 

    php artisan build --env="file-name"
    
where `.env.file-name` is found in the envrionment file locations


