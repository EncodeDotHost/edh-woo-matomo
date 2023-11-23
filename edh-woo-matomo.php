<?php
/*
 * Plugin Name:       Encode Woo Matomo
 * Description:       Custom plugin to send product tracking to Matomo analytics
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.2
 * Author:            EncodeDotHost
 * Author URI:        https://encode.host/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       edh-woo-matomo
 * Domain Path:       /languages
 */

 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define the function to track product page views
function track_product_page_view() {
    global $product;

    // Check if it's a single product page
    if (is_product() && isset($product)) {
        // Get product data
        $product_id = $product->get_id();
        $product_sku = $product->get_sku();
        $product_name = $product->get_name();
        // Get the main product category (first category)
        $product_categories = wp_get_post_terms($product_id, 'product_cat');
        $product_category = !empty($product_categories) ? $product_categories[0]->name : '';
        $product_price = $product->get_price();

	// Set the product name if SKU is blank
        $tracked_sku = !empty($product_sku) ? $product_sku : $product_name;

        // Push Product View Data to Matomo - Populate parameters dynamically
        echo "
        <script>
            if (typeof _paq !== 'undefined') {
                _paq.push(['setEcommerceView',
                    '$tracked_sku', // (Required) productSKU
                    '$product_name', // (Optional) productName
                    '$product_category', // (Optional) categoryName
                    $product_price // (Optional) price
                ]);
            }
        </script>
        ";
    }
}

// Hook the function to wp_footer
add_action('wp_footer', 'track_product_page_view', 99);
