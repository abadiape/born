<?php
/**
 * Copyright 2020 © Born, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Born\CartItemsDownload\Controller\Cart;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Quote\Model\Quote\Item;
use Psr\Log\LoggerInterface;

/**
 * DownloadItems ajax request
 *
 * @package Born\CartItemsDownload\Controller\Cart
 */
class DownloadItems extends Action
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DownloadItems constructor
     *
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param Json $json
     * @param \Magento\Framework\Filesystem $filesystem
     * @param LoggerInterface $logger
     */

    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,	
        Json $json,
	\Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        LoggerInterface $logger
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->json = $json;
	$this->fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Controller execute method
     *
     * @return void
     */
    public function execute()
    {
        try {
	    //filename for download
	    $filename = "Shopping_Cart_items_" . "_" . date('Ymd') . ".csv";
	    $fileDir = 'export';
	    $filepath = $fileDir . '/' . $filename;
	    $this->directory->create($fileDir);
	    $streamHandler = $this->directory->openFile($filepath, 'w+');
	    $streamHandler->lock();
            $quote = $this->checkoutSession->getQuote();
	    $items = $quote->getAllItems();	    
	    $n = 0;	    
            foreach ($items as $item) {
	       if ($n === 0)
	       {
		  $columnHeaders = array_keys($item->getData());
		  $headers = [];
	          $itemData = [];	  
	       }

	       foreach ($columnHeaders as $header) 
	       {
		  if (! is_object($item->getData($header)) && ! empty($item->getData($header)))
		  {
			$itemData[] = $item->getData($header);
			$headers[] = $header;
		  }
	       }
	       if ($n === 0)
	       {
	         $streamHandler->writeCsv(array_values($headers)); 
		 $this->logger->info(print_r($headers, true));
	       }	       
	       $streamHandler->writeCsv(array_values($itemData));	       
	       $this->logger->info(print_r($itemData, true));
	       $itemData = [];
               $n++;               
            }
	    $content['type'] = 'filename';
            $content['value'] = $filepath;
            $content['rm'] = 1;
	    $this->fileFactory->create($filename, $content, DirectoryList::VAR_DIR);
	    
	    $this->jsonResponse('Your Shopping Cart Items were successfully downloaded!');
        } catch (LocalizedException $e) {
            $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->jsonResponse('Something went wrong while downloding the Cart items. Please refresh the page and try again.');
        }

    }

    /**
     * JSON response builder.
     *
     * @param string $error
     * @return void
     */
    private function jsonResponse(string $error = '')
    {
        $this->getResponse()->representJson(
            $this->json->serialize($this->getResponseData($error))
        );
    }

    /**
     * Returns response data.
     *
     * @param string $error
     * @return array
     */
    private function getResponseData(string $error = ''): array
    {
        $response = ['success' => true];

        if (!empty($error)) {
            $response = [
                'success' => false,
                'error_message' => $error,
            ];
        }

        return $response;
    }
}
