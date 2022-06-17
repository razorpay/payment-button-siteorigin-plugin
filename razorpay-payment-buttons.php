<?php

/**
 * Plugin Name: Razorpay Payment Button for SiteOrigin
 * Plugin URI:  https://github.com/razorpay/payment-button-siteorigin-plugin
 * Description: Razorpay Payment Button for SiteOrigin
 * Version:     1.0.3
 * Author:      Razorpay
 * Author URI:  https://razorpay.com
 */

require_once __DIR__.'/razorpay-sdk/Razorpay.php';
require_once __DIR__.'/includes/rzp-btn-view.php';
require_once __DIR__.'/includes/rzp-btn-action.php';
require_once __DIR__.'/includes/rzp-btn-settings.php';
require_once __DIR__.'/includes/rzp-payment-buttons.php';
require_once __DIR__.'/includes/rzp-subscription-buttons.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors;

add_action('admin_enqueue_scripts', 'bootstrap_scripts_enqueue_siteorigin', 0);
add_action('admin_post_rzp_btn_siteorigin_action', 'razorpay_payment_button_siteorigin_action', 0);

function bootstrap_scripts_enqueue_siteorigin($hook_suffix)
{
    if ($hook_suffix != 'admin_page_rzp_button_view_siteorigin')
    {
        return;
    }
    wp_register_style('bootstrap-css-siteorigin', plugin_dir_url(__FILE__)  . 'public/css/bootstrap.min.css',
                null, null);
    wp_register_style('button-css-siteorigin', plugin_dir_url(__FILE__)  . 'public/css/button.css',
                null, null);
    wp_enqueue_style('bootstrap-css-siteorigin');
    wp_enqueue_style('button-css-siteorigin');

    wp_enqueue_script('jquery');
}

/**
 * This is the RZP Payment button loader class.
 */
if (!class_exists('RZP_Payment_Button_SiteOrigin_Loader'))
{

    // Adding constants
    if (!defined('RZP_PAYMENT_SITEORIGIN_BASE_NAME'))
    {
        define('RZP_PAYMENT_SITEORIGIN_BASE_NAME', plugin_basename(__FILE__));
    }

    if (!defined('RZP_REDIRECT_URL'))
    {
        // admin-post.php is a file that contains methods for us to process HTTP requests
        define('RZP_REDIRECT_URL', esc_url(admin_url('admin-post.php')));
    }

    class RZP_Payment_Button_SiteOrigin_Loader
    {
        /**
         * Start up
         */
        public function __construct()
        {
            add_action('admin_menu', array($this, 'rzp_add_plugin_page'));

            add_filter('plugin_action_links_' . RZP_PAYMENT_SITEORIGIN_BASE_NAME, array($this, 'razorpay_plugin_links'));

            $this->settings = new RZP_Payment_Button_SiteOrigin_Setting();
        }

        /**
         * Creating the menu for plugin after load
        **/

        public function rzp_add_plugin_page()
        {
            /* add pages & menu items */
            add_menu_page(esc_attr__('Razorpay Payment Button', 'textdomain'), esc_html__('Razorpay Buttons SiteOrigin', 'textdomain'),
            'administrator','razorpay_button_siteorigin',array($this, 'rzp_view_buttons_page'), '', 10);

            add_submenu_page(esc_attr__('razorpay_button_siteorigin', 'textdomain'), esc_html__('Payment Buttons', 'textdomain'),
                'Payment Buttons', 'administrator','razorpay_button_siteorigin', array($this, 'rzp_view_buttons_page'),0);

            add_submenu_page(esc_attr__('razorpay_button_siteorigin', 'textdomain'), esc_html__('Razorpay Settings', 'textdomain'),
            'Settings', 'administrator','razorpay_siteorigin_settings', array($this, 'razorpay_siteorigin_settings'));

            add_submenu_page(esc_attr__('', 'textdomain'), esc_html__('Razorpay Buttons SiteOrigin', 'textdomain'),
            'Payment Buttons', 'administrator','rzp_button_view_siteorigin', array($this, 'rzp_button_view_siteorigin'));

            add_submenu_page(esc_attr__('razorpay_button_siteorigin', 'textdomain'), esc_html__('Razorpay Subscription Buttons', 'textdomain'),
                'Subscription Buttons', 'administrator','razorpay_subscription_button_siteorigin', array($this, 'rzp_subscription_buttons_page'),1);

            add_submenu_page(esc_attr__('', 'textdomain'), esc_html__('Razorpay Subscription Button', 'textdomain'),
                'Subscription Buttons', 'administrator','rzp_button_view_siteorigin',array($this, 'rzp_button_view_siteorigin'));
        }

        /**
         * Initialize razorpay api instance
        **/
        public function get_razorpay_api_instance()
        {
            $key = get_option('key_id_field');

            $secret = get_option('key_secret_field');

            if (empty($key) === false and empty($secret) === false)
            {
                return new Api($key, $secret);
            }

            wp_die('<div class="error notice">
                        <p>RAZORPAY ERROR: Payment button fetch failed.</p>
                     </div>');
        }

        /**
         * Creating the settings link from the plug ins page
        **/
        function razorpay_plugin_links($links)
        {
            $pluginLinks = array(
                            'settings' => '<a href="'. esc_url(admin_url('admin.php?page=razorpay_siteorigin_settings')) .'">Settings</a>',
                            'docs'     => '<a href="https://razorpay.com/docs/payments/payment-button/supported-platforms/wordpress/site-origin/">Docs</a>',
                            'support'  => '<a href="https://razorpay.com/contact/">Support</a>'
                        );

            $links = array_merge($links, $pluginLinks);

            return $links;
        }

        /**
         * Razorpay Payment Button Page
         */
        public function rzp_view_buttons_page()
        {
            $rzp_payment_buttons = new RZP_Payment_Buttons_SiteOrigin();

            $rzp_payment_buttons->rzp_buttons();
        }

        /**
         * Razorpay Subscription Button Page
         */
        public function rzp_subscription_buttons_page()
        {
            $rzp_subscription_buttons = new RZP_Subscription_Buttons_SiteOrigin();

            $rzp_subscription_buttons->subscription_buttons();
        }

        /**
         * Razorpay Setting Page
         */
        public function razorpay_siteorigin_settings()
        {
            $this->settings->razorpaySettings();
        }

        /**
         * Razorpay button detail page
         */
        public function rzp_button_view_siteorigin()
        {
            $new_button = new RZP_View_Button_SiteOrigin();

            $new_button->razorpay_view_button();
        }
    }
}

/**
* Instantiate the loader class.
*
* @since     2.0
*/
$RZP_Payment_Button_SiteOrigin_Loader = new RZP_Payment_Button_SiteOrigin_Loader();

function razorpay_payment_button_siteorigin_action()
{
    $btn_action = new RZP_Button_Action_SiteOrigin();

    $btn_action->process();
}

include_once(__DIR__.'/widgets/payment_button/payment_button.php');
include_once(__DIR__.'/widgets/subscription_button/subscription_button.php');

/**
 * Razorpay widgets tab for siteorigin editor
 */
function add_razorpay_widget_tabs($tabs)
{
    $tabs[] = array(
        'title' => __('Razorpay Widgets', 'razorpay'),
        'filter' => array(
            'groups' => array('razorpay')
        )
    );

    return $tabs;
}

add_filter('siteorigin_panels_widget_dialog_tabs', 'add_razorpay_widget_tabs', 20);
