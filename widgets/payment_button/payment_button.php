<?php
/* Payment Button widget */

class rzp_payment_button_widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
        // Base ID of your widget
            'rzp_payment_button_widget',

            // Widget name will appear in UI
            __('Payment Button', 'wp_payment_widget_domain'),

            // Widget description
            array('description' => __('A simple button to collect online payments or donations', 'wp_payment_widget_domain'), 'panels_groups' => array('razorpay'))
        );
    }

    // Widget Backend
    public function form( $instance )
    {

        if ( isset( $instance[ 'payment_button_id' ] ) ) {
            $payment_button = $instance[ 'payment_button_id' ];
        }
        else
        {
            $payment_button ='';
        }


        // Widget admin form

        $buttons = $this->get_buttons();

        ?>

        <p class="default-form">
            <label for="<?php echo $this->get_field_id( 'payment_button_id' ); ?>"><?php _e( 'Payment Button:' ); ?></label>
            <select class="widefat product_category" name="<?php echo $this->get_field_name( 'payment_button_id' ); ?>" id="<?php echo $this->get_field_id( 'payment_button_id' ); ?>">

        <option value="">select</option>
        <?php
        if ($buttons)
        {

            foreach($buttons['items'] as $item)
                {
                    ?>
                    <option value="<?php echo $item['id']; ?>" <?php if($payment_button==$item['title']){echo 'selected';}?>><?php echo $item['title']; ?></option>
                    <?php

                }
        }
                ?>
            </select>
        </p>

        <?php
    }

    public function get_buttons()
    {
        $buttons = array();

        $rzp_payment_button_loader = new RZP_Payment_Button_SiteOrigin_Loader();

        $api = $rzp_payment_button_loader->get_razorpay_api_instance();

        try
        {
           return $items = $api->paymentPage->all(['view_type' => 'button', "status" => 'active','count'=> 100]);
        }
        catch (\Exception $e)
        {
            $message = $e->getMessage();

            wp_die('<div class="error notice">
                <p>RAZORPAY ERROR: Payment button fetch failed with the following message: '.$message.'</p>
             </div>');
        }

    }


}

// Register and load the widget
function rzp_payment_button_load_widget() {

    register_widget( 'rzp_payment_button_widget' );

}

add_action( 'widgets_init', 'rzp_payment_button_load_widget' );
