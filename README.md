# wp-social-nav

WordPress plugin to create social media navigation. The plugin registers new nav location for the social media nav.

## Usage

1. Install and activate the plugin
2. Create new menu to our site
3. Add **Custom Links** items to the menu you created. The CSS automatically detects most of the social media links and shows the Font Awesome icon instead of the given link text.
4. Create new navigation to desired location in your theme:

```php
<?php
wp_nav_menu(array(
  'container'       => false,
  'menu'            => 'wp-social-nav',
  'menu_id'         => 'wp-social-nav',
  'theme_location'  => 'wp-social-nav',
  'fallback_cb'     => false
));
```
5. Give additional styles (optional)

## Thanks

This small plugin is inspired by:

- [Font Awesome](https://fontawesome.com/)
- [WooCommerce social icon menu](https://docs.woocommerce.com/document/create-a-social-icon-menu/)