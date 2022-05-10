<?php
/* Subscription Button widget */

class rzp_subscription_button_widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
        // Base ID of your widget
            'rzp_subscription_button_widget',

            // Widget name will appear in UI
            __('Subscription Button', 'wp_subscription_widget_domain'),

            // Widget description
            array('description' => __('A simple subscription button to collect online payments or donations', 'wp_subscription_widget_domain'), 'panels_groups' => array('razorpay'))
        );
    }

    /**
     * Widget Backend
     */
    public function form($instance)
    {
        if (isset($instance['subscription_button_id']))
        {
            $subscription_button = $instance['subscription_button_id'];
        }
        else
        {
            $subscription_button ='';
        }

        // Widget admin form
        $buttons = $this->get_subscription_buttons();
        ?>

        <p class="default-form">
            <label for="<?php echo $this->get_field_id('subscription_button_id'); ?>"><?php _e('Subscription Button:'); ?></label>
            <select class="widefat product_category" name="<?php echo $this->get_field_name('subscription_button_id'); ?>" id="<?php echo $this->get_field_id('subscription_button_id'); ?>">

                <option value="">select</option>
                <?php
                if ($buttons)
                {
                    foreach ($buttons['items'] as $item)
                    {
                        ?>
                        <option value="<?php echo $item['id']; ?>" <?php if($subscription_button==$item['title']){echo 'selected';}?>><?php echo $item['title']; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </p>
        <?php
    }

    /**
     * Fetch all active razorpay subscription buttons
     */
    public function get_subscription_buttons()
    {
        $rzp_payment_button_loader = new RZP_Payment_Button_SiteOrigin_Loader();

        $api = $rzp_payment_button_loader->get_razorpay_api_instance();

        try
        {
            return $items = $api->paymentPage->all(['view_type' => 'subscription_button', "status" => 'active','count'=> 100]);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();

            wp_die('<div class="error notice">
                <p>RAZORPAY ERROR: Subscription button fetch failed with the following message: '.$message.'</p>
             </div>');
        }
    }

    /** Creating widget front-end
     * This is where the button action happens
     */
    public function widget($args, $instance)
    {
        if ($instance['subscription_button_id'])
        {
            $subscription_button = $instance['subscription_button_id'];

            if (!function_exists('get_plugin_data'))
            {
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }

            $mod_version = get_plugin_data(plugin_dir_path(__DIR__) . '../razorpay-payment-buttons.php')['Version'];

            $dataPlugin = "wordpress-subscription-button-siteorigin-" . $mod_version;
            ?>
            <form>
                <?php
                wp_print_script_tag(
                    array(
                        'src' => esc_url('https://cdn.razorpay.com/static/widget/subscription-button.js'),
                        'data-plugin' => esc_attr($dataPlugin),
                        'data-subscription_button_id' => esc_attr(!empty($subscription_button) ? $subscription_button : ''),
                    )
                ); ?>
            </form>

            <?php
        }
    }
}

/**
 * Register and load the widget
 */
function rzp_subscription_button_load_widget()
{
    register_widget('rzp_subscription_button_widget');
}

add_action('widgets_init', 'rzp_subscription_button_load_widget');
