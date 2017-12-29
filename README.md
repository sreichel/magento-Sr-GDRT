# Magento: Google Dynamic Remarketing Tag [![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://paypal.me/sv3n1)

Google remarketing can help you reach people who have previously visited your website as they visit other sites on the Google Display Network or search on Google. Using remarketing, you can show these customers messages tailored to them based on which sections of your site they visited.

## Features:
- supports multi-store setup
- supports "Google Tag Manager"
- use product SKU or ID as identifier
- set product prices with or without taxes
- use custom URLs for GDRT page type
    - home
    - cart
    - catalog
    - product
    - searchresults
    - purchase

## Compatible with:
Only tested with 1.9.3.x, but it should also work with older versions.


## Magento Marketplace: 
- _maybe later_

## Installation:

### Via modman
```
modman clone https://github.com/sreichel/magento-Sr-GDRT.git
```
### Via composer:
```
{
    "require": {
        "sr/gdrt": "*",
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/sreichel/magento-Sr-GDRT.git"
        }
    ]
}
```

### Via FTP:
Upload content from __src__ directory to your magento installation _(not recommended)_

#### Note:
<sup>_If you have used Anaraky_Gdrt before, all config settings are kept._</sup>