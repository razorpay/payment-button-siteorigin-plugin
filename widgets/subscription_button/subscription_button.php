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
            array('description' => __('A simple button to collect online payments or donations', 'wp_subscription_widget_domain'), 'panels_groups' => array('razorpay'))
        );
    }
    

}

// Register and load the widget
function rzp_subscription_button_load_widget() {

    register_widget( 'rzp_subscription_button_widget' );

}

add_action( 'widgets_init', 'rzp_subscription_button_load_widget' );
