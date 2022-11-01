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
                        'affiliation' => $this->_scopeConfig->getValue(\Magento\Store\Model\Information::XML_PATH_STORE_INFO_NAME),
                        'value' => $order->getBaseGrandTotal() - ($order->getBaseTaxAmount() + $order->getBaseShippingAmount()), 
                        'tax' => round($order->getBaseTaxAmount(),2), 
                        'shipping' => round($order->getBaseShippingAmount(),2), 
                        'coupon' => strval($order->getCouponCode() ?? ''),
                    ],
        ];

        $products = [];
        $product_index = 0;
        foreach ($order->getAllItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            // Only process simples
            if ($item->getProductType() === \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                continue;
            }
            $presentingItem = $item->getParentItem() ?? $item;

            $products[] = [
                'item_id' => $presentingItem->getSku(),
                'item_name' => $presentingItem->getName(),
                'affiliation' => "Magento Store",
                'coupon' => "",
                'discount' => 0,
                'index' => $product_index,
                'item_brand' => $item->getData('brand'),
                'item_category' => '',
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
