<?php
/**
 * Copyright 2020 © Born, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Born\CartItemsDownload\Block\Cart;

use Magento\Quote\Model\Quote;

/**
 * Block on checkout/cart/index page to allow items download as CSV file on the  cart items grid
 * The download will be allowed if there are items in the shopping cart and
 * Store->Configuration->Sales->Checkout->Shopping Cart->Enable Items Download by Customer, is set to Yes.
 *
 * @api
 * @since 100.1.7
 */
class Download extends \Magento\Framework\View\Element\Template
{
    /**
     * Config settings path to determine when items on checkout/cart/index will be downloadable
     */
    const XPATH_CONFIG_ENABLE_CSV_DOWNLOAD = 'checkout/cart/enable_csv_download';

    /**
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.7
     */
    public function isCartDownloadAllowed()
    {
        $downloadable = (int)$this->_scopeConfig->getValue(
                self::XPATH_CONFIG_ENABLE_CSV_DOWNLOAD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        return $downloadable;
    }

}
