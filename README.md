# Magento 2 Advanced JS Bundler

[![Build Status](https://travis-ci.com/mygento/module-js-bundler.svg?branch=v2.3)](https://travis-ci.com/mygento/module-js-bundler)
[![Latest Stable Version](https://poser.pugx.org/mygento/module-js-bundler/v/stable)](https://packagist.org/packages/mygento/module-js-bundler)
[![Total Downloads](https://poser.pugx.org/mygento/module-js-bundler/downloads)](https://packagist.org/packages/mygento/module-js-bundler)

## Installation with composer
* Include the repository: `composer require mygento/module-js-bundler`

## Usage

```
<media>
    ...
    <jsbundles module="Mygento_JsBundler">
        <bundle name="catalog1">
            <item>Magento_Catalog/js/product/list/toolbar.js</item>
            <item>Magento_Catalog/js/price-box.js</item>
            <item>Magento_Catalog/js/catalog-add-to-cart.js</item>
        </bundle>
        <bundle name="catalog2">
            <item>Magento_Catalog/js/price-box.js</item>
            <item>Magento_Catalog/js/catalog-add-to-cart.js</item>
        </bundle>
    </jsbundles>
    ...
</media>
```

## Compability
The module is tested on magento version 2.3.x