<?php
namespace Zero1\Ga4Gtm\Helper;

use \Magento\Framework\App\Config\Storage\WriterInterface as ConfigWriter;
use \Magento\Framework\App\Helper\Context;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_GA4GTM_ACTIVE = 'ga4gtm/general/active';
    const XML_PATH_GA4GTM_ANALYTICS_ID = 'ga4gtm/general/ga_id';
    const XML_PATH_GA4GTM_GTM_ID = 'ga4gtm/general/gtm_id';

    /**
     * @var ConfigWriter
     */
    protected $configWriter;

    /**
     * @param Context $context
     * @param ConfigWriter $configWriter
     */
    public function __construct(
        Context $context,
        ConfigWriter $configWriter
    ) {
        $this->configWriter = $configWriter;

        parent::__construct($context);
    }

    /**
     * Get Active config option value
     *
     * @return bool
     */
    public function getActive() {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GA4GTM_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Process per min config option value
     *
     * @return bool
     */
    public function getAnalyticsId() {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GA4GTM_ANALYTICS_ID,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Process per min config option value
     *
     * @return bool
     */
    public function getGtmId() {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GA4GTM_GTM_ID,
            ScopeInterface::SCOPE_STORE
        );
    }
}
