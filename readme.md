# Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).


# NPM

ATTENTION!
One of the dependencies to install npm is 'libssl-dev', but we use this library also for generate PDF and it required specific version to make it works properly.
So for this reason, please install this version of 'libsll-dev' :
$ sudo apt-get install libssl-dev=1.0.2g-1ubuntu4.10

Before start please make sure you have installed nodejs version 8.9.4 (we will use this same version 
on development and production to minimize conflicts). All action/command is under the root folder of
project (depend on your local copy folder). I suggest you to use NVM To make your life more easier, if you 
decide to install the node using their installer, you can go ahead download it here [https://nodejs.org/en](nodejs)
(use this version 8.9.4). All installation instruction of NVM is on this link [https://github.com/creationix/nvm](NVM).

After finish install NVM please follow commands below to install nodejs.

```
nvm install 8.9.4
nvm ls // to see all list version installed and used by default in local
nvm ls-remote // to see all list version available to install
nvm ls-remote --lte // to see all list lts version available to install
nvm use 8.9.4 // to use specific version but not make it default
nvm alias default 8.9.4 // to make default some version of nodejs
```


## Getting started

Follow commands below to get started. This command is assume that you are not yet install all packages needed by the project

```
cd to/your/root/folder
npm install
npm run dev // to build one time
npm run watch // to watch changes and build automatically (usually use for development)
npm run production // to build production ready (usually on production server)

// in case you use yarn
yarn (initial install)
yarn dev
yarn watch
yarn production
```

All commands and packages listed on package.json.

## Development tree

All js files placed under ```resources/assets/js/```. This project is using VueJS and Vuex, for packages 
detail please open package.json. Vuejs is a framework component based. Vuex is a state management it 
function is to manage all state changes and deliver it to all components. For further please see their 
documentation in here [https://vuejs.org/v2/guide](vuejs) and here [https://vuex.vuejs.org](vuex).

```components``` folder is for new components created. ```helper-library``` is for some function that can be
use by other components. ```store``` is the file of state management (use by vuex).

We use Laravel Mix for builder. For new js file, please add it to webpack.mix.js, follow the existing format.
Like example below

```
// js ( 'source file', 'build file')
js('resources/assets/js/components/marketplace/search.js', 'public/js/marketplace/search.js')
```
