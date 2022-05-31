<?php

declare(strict_types = 1);

namespace SamSolutions\Artwork\Model;

use Magento\Sales\Model\Order;
use SamSolutions\Artwork\Block\Email\ArtworkNotification;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\ProductAlert\Helper\Data;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class EmailSender
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Email extends AbstractHelper
{
    private const XML_PATH_EMAIL_SENDER = 'trans_email/ident_general/email';
    private const XML_PATH_NAME_SENDER  = 'trans_email/ident_general/name';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Emulation
     */
    private $appEmulation;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Data
     */
    private $productAlertData;

    /**
     * @var State
     */
    private $state;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CustomerRepositoryInterface|null
     */
    private $customerRepository;

    /**
     * EmailSender constructor.
     *
     * @param Context                          $context
     * @param TransportBuilder                 $transportBuilder
     * @param Emulation                        $appEmulation
     * @param StoreManagerInterface            $storeManager
     * @param Data                             $productAlertData
     * @param State                            $state
     * @param ProductRepository                $productRepository
     * @param CustomerRepositoryInterface|null $customerRepository
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        Emulation $appEmulation,
        StoreManagerInterface $storeManager,
        Data $productAlertData,
        State $state,
        ProductRepository $productRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->appEmulation = $appEmulation;
        $this->storeManager = $storeManager;
        $this->productAlertData = $productAlertData;
        $this->state = $state;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return mixed
     */
    public function getEmailSender()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER);
    }

    /**
     * @return mixed
     */
    public function getNameSender()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_NAME_SENDER);
    }

    /**
     * @param int $storeId
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    private function getStore(int $storeId): StoreInterface
    {
        return $this->storeManager->getStore($storeId);
    }

    /**
     * @return AbstractBlock
     * @throws LocalizedException
     */
    protected function getBlock()
    {
        return $this->productAlertData->createBlock(ArtworkNotification::class);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function send(Order $order)
    {
        $storeId = $order->getStoreId();
        $customerId = $order->getCustomerId() ?? null;

        if ($customerId) {
            try {
                $customer = $this->customerRepository->getById($customerId);
            } catch (NoSuchEntityException $e) {
                return false;
            } catch (LocalizedException $e) {
                return false;
            }
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $customerEmail = $customer->getEmail();
        } else {
            $customerName = __('Customer');
            $customerEmail = $order->getCustomerEmail();
        }

        $from = [
            'name'  => $this->getNameSender(),
            'email' => $this->getEmailSender(),
        ];
        $templateId = 'artwork_notification_template';
        $this->appEmulation->startEnvironmentEmulation($storeId);

        $block = $this->getBlock();
        $store = $this->getStore((int)$storeId);
        $block->setStore($order->getStore())->reset();
        $block->setOrderId($order->getId());
        foreach ($order->getAllVisibleItems() as $productItem) {
            if (isset($productItem->getProductOptions()['additional_options'])) {
                if (is_array($productItem->getProductOptions()['additional_options'])) {
                    foreach ($productItem->getProductOptions()['additional_options'] as $option) {
                        if (is_array($option)) {
                            if ($option['value'] == 'Yes' || $option['value'] == '1') {
                                $block->addItem($productItem);
                            }
                        }
                    }
                }
            }
        }
        $alertGrid = $this->state->emulateAreaCode(
            Area::AREA_FRONTEND,
            [$block, 'toHtml']
        );
        $this->appEmulation->stopEnvironmentEmulation();

        $this->transportBuilder->setTemplateIdentifier(
            $templateId
        )->setTemplateOptions(
            ['area' => Area::AREA_FRONTEND, 'store' => $storeId]
        )->setTemplateVars(
            [
                'customerName' => $customerName,
                'alertGrid'    => $alertGrid,
            ]
        )->setFromByScope(
            $from
        )->addTo(
            $customerEmail,
            $customerName
        );

        return $this->sendMessage($this->transportBuilder);
    }

    /**
     * @param TransportBuilder $transportBuilder
     *
     * @return bool
     */
    private function sendMessage(TransportBuilder $transportBuilder): bool
    {
        try {
            $transportBuilder->getTransport()->sendMessage();
        } catch (MailException $e) {
            return false;
        } catch (LocalizedException $e) {
            return false;
        }

        return true;
    }
}
