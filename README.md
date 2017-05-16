# mediabank-wordpress-plugin


![Picturae](img/picturae-logo.png)


A WordPress plugin to easily embed the Mediabank client into your WordPress installation.

# Requirements

- PHP 5.5 or above
- Mediabank API key

## Description

This plugin can be used to bootstrap the [Picturae Mediabank](http://demo.webservices.picturae.pro/v2mediabank) inside a WordPress website. The Picturae Mediabank is used to enable Memorix Maior users to publish their collections on the internet. Website visitors can then search through the collections, filter them and view them in several display modes.

* The plugin offers the ability to configure several display modes
* The plugin requires a valid API key to the Picturae Webkitchen API


## Installation

* Download the latest version of the plugin archive from [here](https://github.com/picturae/mediabank-wordpress-plugin/releases/download/1.0.0/mediabank.zip)
* Upload the Mediabank plugin to your blog and activate it.
* Go to the Mediabanks's settings page and enter your API key and media entity code(s).
* Make sure the your theme's header.php has a base tag located in the head as follwed:

```html
<base href="<?php echo get_permalink(); ?>/">
```

* Set the correct link structure in de admin at **Settings -> Permalinks** to **Custom Structure** and *remove the trailing slash* at the end like **/%postname%**

* Create a page where the mediabank will be displayed
    * Put [mediabank] in the content of the page
    * Check the checkbox "Display mediabank"

## Contact

For any questions please visit us at [picturae.com](http://picturae.com) or contact us at [contact@picturae.com](mailto://contact@picturae.com)