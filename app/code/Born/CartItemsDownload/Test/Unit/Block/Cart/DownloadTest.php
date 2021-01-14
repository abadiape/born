<?php
/**
 * Copyright 2020 © Born, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Born\CartItemsDownload\Test\Unit\Block\Cart;

/**
 * Class DownloadTest
 * @package Born\CartItemsDownload\Test\Unit\Block\Cart
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DownloadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Born\CartItemsDownload\Block\Cart\download
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    protected function setUp()
    {
	$objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();	

	$this->block = $objectManager->getObject(
            \Born\CartItemsDownload\Block\Cart\Download::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }


    /**
     * @return void
     */
    public function testIsCartDownloadAllowed()
    {
	$configValue = true;        

        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->with(
                $this->stringContains(
                    'checkout/cart/enable_csv_download'
                ),
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
            ->will($this->returnValue($configValue));

	$this->assertEquals($configValue, $this->block->isCartDownloadAllowed());
    }
}
