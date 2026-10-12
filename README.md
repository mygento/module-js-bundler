# Magento 2 Advanced JS Bundler

[![Build Status](https://travis-ci.com/mygento/module-js-bundler.svg?branch=v2.3)](https://travis-ci.com/mygento/module-js-bundler)
[![Latest Stable Version](https://poser.pugx.org/mygento/module-js-bundler/v/stable)](https://packagist.org/packages/mygento/module-js-bundler)
[![Total Downloads](https://poser.pugx.org/mygento/module-js-bundler/downloads)](https://packagist.org/packages/mygento/module-js-bundler)

## Installation with composer
* Include the repository: `composer require mygento/module-js-bundler`

## Usage

Place a file js_bundler.xml in theme's etc folder.

Every `item` is a RequireJS module id; for a `paths` alias, set the file in `path`, without `.js`.

```
<?xml version="1.0" encoding="utf-8"?>
<bundles>
  <bundle name="catalog">
      <item>Magento_Catalog/js/product/list/toolbar</item>
      <item>Magento_Catalog/js/price-box</item>
      <item>Magento_Catalog/js/catalog-add-to-cart</item>
  </bundle>
  ...
  <bundle name="checkout">
      <item path="requirejs/domReady">domReady</item>
      <item>text!Magento_Checkout/template/minicart/content.html</item>
      <item path="Magento_Ui/templates/modal/modal-popup.html">text!ui/template/modal/modal-popup.html</item>
      <item>...</item>
  </bundle>
</bundles>
```

`setup:static-content:deploy` writes:

* `js/bundle/<name>-bundle.js` for every bundle of two or more items, modules and text resources together;
* `js/bundle/requirejs-bundles.js` with `require.config({bundles: ...})` listing exactly the bundled modules and text resources: RequireJS takes a listed text resource from its bundle instead of requesting the file.

With `mygento_js_bundler/general/enabled` set, `requirejs-bundles.js` is added to `<head>` before `mage/requirejs/mixins.js`, so that mixins of bundled modules apply.

Do not bundle `shim` modules or non-AMD files.

## Compability
The module is tested on magento version 2.4.7
