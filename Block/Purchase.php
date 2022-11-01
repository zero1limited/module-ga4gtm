<?php

namespace Zero1\Ga4Gtm\Block;

class Purchase extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Checkout\Model\Session */
    private $checkoutSession;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array
     */
    public function getDimensionObject()
    {
        $order = $this->checkoutSession->getLastRealOrder();

        $data = [
            /**
             * DT managed GTM Tag will trigger affiliate window if an order is NOT paid with v12 finance.
             * Offering payment method information to enable this to happen
             *
             * dimension2 = payment_method
             */
            'dimension2' => $order->getPayment()->getMethod(),
        ];

        return $data;
    }

    /**
     * @return array
     */
    public function getEcommerceObject()
    {
        $order = $this->checkoutSession->getLastRealOrder();

        $data = [
            'event' => "purchase",
            'ecommerce' => [
                        'currency' => "GBP",
                        'transaction_id' => 'WEB'.$order->getIncrementId(), // order_reference_number
                        'affiliation' => $this->_scopeConfig->getValue(\Magento\Store\Model\Information::XML_PATH_STORE_INFO_NAME), // brand_website_name
                        'value' => $order->getBaseGrandTotal() - ($order->getBaseTaxAmount() + $order->getBaseShippingAmount()), // order_value_minus_tax_and_shipping
                        'tax' => round($order->getBaseTaxAmount(),2), // total_product_tax_no_shipping_tax
                        'shipping' => round($order->getBaseShippingAmount(),2), // shipping_cost_minus_tax
                        'coupon' => strval($order->getCouponCode() ?? ''),
                    ],
        ];

        $products = [];
        $product_index = 0;
        foreach ($order->getAllItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Item $item */

            // Only process simple products
            if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                continue;
            }

            // Configurable variant persists part of the data in parent item
            $presentingItem = $item->getParentItem() ?? $item;

            $products[] = [
                'item_id' => $presentingItem->getSku(),
                'item_name' => $presentingItem->getName(),
                'affiliation' => "Google Merchandise Store",
                'coupon' => "",
                'discount' => 0,
                'index' => $product_index,
                'item_brand' => $item->getData('brand'),
                'item_category' => "Apparel",
                'item_category2' => "Adult",
                'item_variant' => '',
                'price' => round($presentingItem->getBasePrice(),2),
                'quantity' => (integer)$item->getQtyOrdered()
            ];

            $product_index ++;
        }

        $data['ecommerce']['items'] = $products;

        return $data;
    }
}