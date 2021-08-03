# ILIAS Plugin - CryptPad

ILIAS Repository Object Plugin 

## Table of Contents
* [Requirement](#requirement)
* [Installation](#installation)
* [CryptPad Installation](#cryptpad-installation)


## Requirement

* PHP: ![PHP Version](https://img.shields.io/badge/PHP-7.2.x-blue.svg)

* ILIAS: ![ILIAS Version](https://img.shields.io/badge/ILIAS-6.x-orange.svg)

## Installation

1. Clone this repository to **/Customizing/global/plugins/Services/Repository/RepositoryObject**
```shell
git clone https://gitlab.databay.de/fhelfer/CryptPad.git
```  
2. Enter the repository and do **composer install**
```shell
cd CryptPad
composer install
```  
  
3. Login to ILIAS with an administrator account (e.g. root)
  
4. Select **Administration -> Extending ILIAS -> Plugins**
  
5. Install **CryptPad** Plugin and activate
  
## CryptPad Installation

### Requirements:
* NodeJs
* npm
* bower
    * ```npm install -g bower```

### Installation
1. Clone CrypPad
```shell
git clone https://github.com/xwiki-labs/cryptpad.git
```
2. Install missing dependencies
```shell
cd cryptpad
npm install
bower install
```
### Configuration
* Copy default configuration
```shell
cd $cryptpath/config
cp config.example.js config.js
```
* For Configuration details visit https://docs.cryptpad.fr/en/admin_guide/customization.html
* Run CryptPad
```shell
npm run watch
or
node server.js
```
