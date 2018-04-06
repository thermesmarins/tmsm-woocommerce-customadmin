TMSM WooCommerce Custom Admin
=================

Custom WooCommerce admin for Thermes Marins de Saint-Malo

Features
-----------

* WooCommerce modifications:
    * Statuses:
        * Handle custom status "Processed"
        * Rename WooCommerce statuses: processing to paid
    * Customization:
        * Renames menu item: "WooCommerce" to "Orders"
        * Change default shop icon for WooCommerce
    * Roles/Users:
        * Adds "Customers" menu
        * Adds new role "Shop Orders Manager"
        * Redirect login for role "Shop Manager" and "Shop Orders Manager" to go directly to orders page
    * Checkout:
        * Adds checkout billing fields: birthday and title fields (optional, settings in "Checkout" admin page)
        * Sync user data to Mailchimp (using Mailchimp for WooCommerce plugin) with following merge tags: firstname as PRENOM, lastname as NOM, birthday as DDN, title as CIV

    
* Misc:
    * Moves Mailjet to submenu of Settings
    * Polylang: display languages as post_state with country flag
    * WP Rocket: empty cache on save product


