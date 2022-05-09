<?php

require_once __DIR__.'/../templates/razorpay-button-view-templates.php';

class RZP_View_Button_SiteOrigin
{
    public function __construct()
    {
        $this->view_template = new RZP_View_Button_SiteOrigin_Templates();
    }

    /**
     * Generates button detail view page with template
    **/
    function razorpay_view_button()
    {
        $this->view_template->razorpay_view_button();
    }
}
