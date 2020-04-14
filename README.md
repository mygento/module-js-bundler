# Magento 2 Advanced JS Bundler

[![Build Status](https://travis-ci.com/mygento/module-js-bundler.svg?branch=v2.3)](https://travis-ci.com/mygento/module-js-bundler)
[![Latest Stable Version](https://poser.pugx.org/mygento/module-js-bundler/v/stable)](https://packagist.org/packages/mygento/module-js-bundler)
[![Total Downloads](https://poser.pugx.org/mygento/module-js-bundler/downloads)](https://packagist.org/packages/mygento/module-js-bundler)

## Installation with composer
* Include the repository: `composer require mygento/module-js-bundler`

## Usage

Place a file js_bundler.xml in theme's etc folder

```
<?xml version="1.0" encoding="utf-8"?>
<bundles>
  <bundle name="catalog">
      <item>Magento_Catalog/js/product/list/toolbar.js</item>
      <item>Magento_Catalog/js/price-box.js</item>
      <item>Magento_Catalog/js/catalog-add-to-cart.js</item>
  </bundle>
  ....
  <bundle name="checkout">
      <item>....</item>
      <item>....</item>
      <item>....</item>
  </bundle>
</bundles>
```

## Compability
The module is tested on magento version 2.3.x