<?php

namespace Zero1\Ga4Gtm\Block;

use \Magento\Framework\View\Element\Template;
use Zero1\Ga4Gtm\Helper\Data as Helper;
use \Magento\Customer\Model\Session as CustomerSession;

class Ga4Gtm extends Template
{
    protected $helper;
    protected $_customerSession;
    protected $httpContext;
    protected $groupRepository;

    public function __construct (
        Helper $helper,
        CustomerSession $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        Template\Context $context,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->_customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->groupRepository = $groupRepository;
        parent::__construct($context, $data);
    }

    public function getAccountId()
    {
        return $this->helper->getAnalyticsId();
    }
    public function getGtmId()
    {
        return $this->helper->getGtmId();
    }

    public function getGroupId(){

    }

        /**
     * @return array
     */
    public function getCustomerObject()
    {
        if($this->_customerSession->isLoggedIn()):
            $group = $this->groupRepository->getById($this->_customerSession->getCustomer()->getGroupId());
        else:
            $group = $this->groupRepository->getById(0);
        endif;

        $data = [
            /**
             * DT managed GTM Tag will trigger affiliate window if an order is NOT paid with v12 finance.
             * Offering payment method information to enable this to happen
             *
             * dimension2 = payment_method
             */
            'event' => 'userData',
            'UserAccountType' => $group->getCode(),
        ];

        return $data;
    }

        /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
    public function customerGroup()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }


}