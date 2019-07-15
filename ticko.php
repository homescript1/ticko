<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://homescriptone.com
 * @since             1.0.0
 * @package           Sedo
 *
 * @wordpress-plugin
 * Plugin Name:       Ticko
 * Plugin URI:        https://homescriptone.com/
 * Description:       This plugin allows to sell basically subscription from two types of products
 * Version:           1.0.0
 * Author:            HomeScript
 * Author URI:        https://homescriptone.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ticko
 * Domain Path:       /languages
 */

if (!defined('ABSPATH'))
    die();


add_action('add_meta_boxes', 'ticko_global_metabox');
function ticko_global_metabox()
{
    add_meta_box('ticko', 'Ticko Ticket Générateur', 'ticko_ticket_generator', 'page', 'normal', 'high');
}

function ticko_ticket_generator()
{
    global $post;
    $post_id = $post->ID;
    $ticko_event_day = get_post_meta($post_id,'ticko_day_of_event',true);
    $ticko_phone_number = get_post_meta($post_id,'ticko_phone_number',true);
    $ticko_quantity = get_post_meta($post_id,'ticko_quantity',true);
    $ticko_ticket_price = get_post_meta($post_id,'ticko_ticket_price',true);
    $ticko_email = get_post_meta($post_id,'ticko_email',true);
    $ticko_disable_ticket = get_post_meta($post_id,'ticko_disable_ticket',true);
    $disabled = "disabled";
    
 
    ?>
    <div id="ticko_ticket_div">
        <label for="ticko_ticket_event_day">Date de l'évenement : </label>
        <input type="date" id="ticko_ticket_event_day" name="ticko_ticket_event_day" value="<?php echo $ticko_event_day; ?>"><br/>
        <label for="ticko_phone_number">Numéro de téléphone officiel de l'évenement : </label>
        <input type="number" id="ticko_phone_number" name="ticko_phone_number" value="<?php echo $ticko_phone_number; ?>"><br/>
        <label for="ticko_quantity">Quantité de ticket disponible : </label>
        <input type="number" id="ticko_quantity" name="ticko_quantity" value="<?php echo $ticko_quantity; ?>"><br/>
        <label for="ticko_ticket_price">Quantité de ticket disponible : </label>
        <input type="number" id="ticko_ticket_price" name="ticko_ticket_price" value="<?php echo $ticko_ticket_price; ?>"><br/>
        <label for="ticko_email">Mail officiel de l'évenement : </label>
        <input type="email" id="ticko_email" name="ticko_email" value="<?php echo $ticko_email; ?>"><br/>
        <button id="ticko_ticket_submit" class="button-primary button-large" <?php if ($ticko_disable_ticket != 0){ echo $disabled; }?>>Sauvegarder ticket</button> 
    </div>
    <span id="ticko_output_message" style="display:none;"></span>
   
    <?php
}


add_action('admin_enqueue_scripts', 'ticko_enqueue_assets');
function ticko_enqueue_assets()
{
    wp_enqueue_style('ticko-css', plugin_dir_url(__FILE__) . "assets/css/ticko.css", array(), '1.0.0', false);
    wp_enqueue_script('ticko-js',  plugin_dir_url(__FILE__) . "assets/js/ticko.js", array("jquery"), '1.0.0', false);
    wp_localize_script(
        'ticko-js',
        'ticko_ajax_object',
        [
            'ticko_ajax_url'      => admin_url('admin-ajax.php'),
            'ticko_ajax_security' => wp_create_nonce('ticko-ajax-security-nonce'),
        ]
    );
}

add_action('wp_ajax_ticko_save_ticket_by_ajax', 'ticko_save_ticket_by_ajax');
function ticko_save_ticket_by_ajax()
{
    $successfully = 0;
     if(isset( $_POST['data']) && wp_verify_nonce($_POST['security'], 'ticko-ajax-security-nonce')) {
        $data =  $_POST['data'];
        $post_id = $data['ticko_id'];
        //saving values into the cpt.
        $ticko_event_day = $data['ticko_event_day'];
        $ticko_phone_number = $data['ticko_phone_number'];
        $ticko_quantity = $data['ticko_quantity'];
        $ticko_price =$data['ticko_ticket_price'];
        $ticko_email = $data['ticko_email'];
        $ticko_name=$data['ticko_name'];
        update_post_meta($post_id,'ticko_day_of_event',$ticko_event_day);
        update_post_meta($post_id,'ticko_phone_number',$ticko_phone_number);
        update_post_meta($post_id,'ticko_quantity',$ticko_quantity);
        update_post_meta($post_id,'ticko_ticket_price',$ticko_price);
        update_post_meta($post_id,'ticko_email',$ticko_email);
        update_post_meta($post_id,'ticko_post_id',$post_id);
        //creating the ticket product.
        $objProduct = new WC_Product();
        $objProduct->set_name($ticko_name);
        $objProduct->set_status("publish");  // can be publish,draft or any wordpress post status
        $objProduct->set_catalog_visibility('visible'); // add the product visibility status
        $objProduct->set_price($ticko_price); // set product price
        $objProduct->set_regular_price($ticko_price); // set product price
        $objProduct->set_manage_stock(true); // true or false
        $objProduct->set_stock_quantity($ticko_quantity);
        $objProduct->set_stock_status('instock'); // in stock or out of stock value
        $objProduct->set_backorders('no');
        $objProduct->set_reviews_allowed(false);
        $objProduct->set_sold_individually(true);
        $objProduct->set_category_ids(array(1,2,3));// array of category ids, You can get category id from WooCommerce Product Category Section of Wordpress Admin
        $product_id = $objProduct->save();
        update_post_meta($post_id,'ticko_product_id',$product_id);
        $successfully = 1;
        update_post_meta($post_id,'ticko_disable_ticket',$successfully);
    }
    echo $successfully;
    wp_die();
}
