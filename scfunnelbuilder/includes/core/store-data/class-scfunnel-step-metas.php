<?php
/**
 * Step metas
 * 
 * @package
 */
namespace SCFunnelbuilder\Metas;

class ScFunnel_Step_Meta_keys
{

    /**
     * List of all meta keys used
     * in different step initializations
     *
     * @param string $type
     * 
     * @return mixed
     */
    public static function get_meta_keys($type = 'landing')
    {
        $meta_keys = [
            'landing' => [],
            'checkout' => [
                '_sc_funnel_checkout_products' => [],
                '_sc_funnel_checkout_discount' => [],
                '_sc_funnel_checkout_coupon' => '',
            ],
            'thankyou' => [
                '_sc_funnel_thankyou_text' => '',
                '_sc_funnel_thankyou_redirect_link' => '',
                '_sc_funnel_thankyou_order_overview' => 'on',
                '_sc_funnel_thankyou_order_details' => 'on',
                '_sc_funnel_thankyou_billing_details' => 'on',
                '_sc_funnel_thankyou_shipping_details' => 'on',
            ],
            'upsell' => [
                '_sc_funnel_upsell_product' => [],
                '_sc_funnel_upsell_discount_type' => '',
                '_sc_funnel_upsell_discount_value' => '',
                '_sc_funnel_upsell_product_price' => '',
                '_sc_funnel_upsell_product_sale_price' => '',
                '_sc_funnel_upsell_hide_image' => 'off',
                '_sc_funnel_upsell_next_step_yes' => '',
                '_sc_funnel_upsell_next_step_no' => '',
            ],
            'downsell' => [
                '_sc_funnel_downsell_product' => [],
                '_sc_funnel_downsell_discount_type' => '',
                '_sc_funnel_downsell_discount_value' => '',
                '_sc_funnel_downsell_product_price' => '',
                '_sc_funnel_downsell_product_sale_price' => '',
                '_sc_funnel_downsell_hide_image' => 'off',
                '_sc_funnel_downsell_next_step_yes' => '',
                '_sc_funnel_downsell_next_step_no' => '',
            ],
            'custom'        => [],
            'trigger'    => [],
        ];
        $meta_keys = apply_filters( 'scfunnels/supported_steps_key', $meta_keys );

        return $meta_keys[$type];
    }
}
