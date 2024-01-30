# Important Note!

Currently this plugin can be used only at your own risk. Vitec Memorix is not actively maintaining this product or providing support with installation etc. If you want to use our mediabank it's quite easy to implement it into a website. Please see the documentation. Ask for documentation when you obtain an apikey. 

We apologise for any inconvenience this may cause.


# mediabank-wordpress-plugin

A WordPress plugin to easily embed the Mediabank client into your WordPress installation.

# Requirements

- PHP 5.5 or above
- Mediabank API key

## Description

This plugin can be used to bootstrap the Webkitchen Mediabank inside a WordPress website. The Mediabank is used to enable Memorix Maior users to publish their collections on the internet. Website visitors can then search through the collections, filter them and view them in several display modes.

* The plugin offers the ability to configure several display modes
* The plugin requires a valid API key to the Mediabank Webkitchen API


## Installation

* Download the latest version of the plugin archive from [here](https://github.com/vitec-memorix/mediabank-wordpress-plugin/archive/v1.3.zip)
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

For any questions please visit [our website]([http://picturae.com](https://www.vitec-memorix.com/en/))
