<?php

namespace Synchrony\DigitalBuy\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Psr\Log\LoggerInterface;

class AuthenticationStatusInquiryHandler implements HandlerInterface
{
    /**
     * @var StatusInquiryReader
     */
    private $reader;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AuthenticationStatusInquiryHandler constructor.
     * @param StatusInquiryReader $reader
     * @param AddressRepositoryInterface $addressRepository
     * @param CountryFactory $countryFactory
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param LoggerInterface $logger
     */
    public function __construct(
        StatusInquiryReader $reader,
        AddressRepositoryInterface $addressRepository,
        CountryFactory $countryFactory,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        LoggerInterface $logger
    ) {
        $this->reader = $reader;
        $this->addressRepository = $addressRepository;
        $this->countryFactory = $countryFactory;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $this->reader->setData($response);
        $session = SubjectReader::readSession($handlingSubject);
        if ($session) {
            $session->setAuthToken($this->reader->getTokenId())
                ->setAuthTokenTimestamp(SubjectReader::readTokenTimestamp($handlingSubject))
                ->setAccountNumber($this->reader->getAccountNumber());
        }

        $quote = SubjectReader::readQuote($handlingSubject);
        if ($quote) {
            $billingAddress = $quote->getBillingAddress();
            $shippingAddress = $quote->getShippingAddress();
            if ($quote->getCustomerId() && $quote->getCustomer()->getDefaultBilling()) {
                $customerAddressData = $this->addressRepository->getById($quote->getCustomer()->getDefaultBilling());
                foreach ([$billingAddress, $shippingAddress] as $address) {
                    $address->importCustomerAddressData($customerAddressData)
                        ->setCustomerAddressId(null)
                        ->setSaveInAddressBook(false);
                }
            }

            $countryCode = 'US';

            $addressLine1 = (string) $this->reader->getAddressLine1();
            $addressLine2 = (string) $this->reader->getAddressLine2();

            $regionId = false;
            $regionCode = $this->reader->getAddressState();
            if ($regionCode) {
                $region = $this->countryFactory->create()->loadByCode($countryCode)
                    ->getRegionCollection()
                    ->addRegionCodeFilter($regionCode)
                    ->setPageSize(1)
                    ->getFirstItem();
                if ($region->getId()) {
                    $regionId = $region->getId();
                }
            }

            foreach ([$billingAddress, $shippingAddress] as $address) {
                /** @var \Magento\Quote\Model\Quote\Address $address */
                $address->setFirstname((string) $this->reader->getFirstName())
                    ->setLastname((string) $this->reader->getLastName())
                    ->setStreet([$addressLine1, $addressLine2])
                    ->setCity((string) $this->reader->getAddressCity())
                    ->setCountryId($countryCode)
                    ->setPostcode((string) $this->reader->getAddressZip());
                if ($regionId) {
                    $address->setRegionId($regionId);
                }
            }

            $addressData = $this->extensibleDataObjectConverter->toFlatArray(
                $billingAddress,
                [],
                \Magento\Quote\Api\Data\AddressInterface::class
            );

            $street = $billingAddress->getStreet();
            if (!empty($street) && is_array($street)) {
                // Unset flat street data
                $streetKeys = array_keys($street);
                foreach ($streetKeys as $key) {
                    unset($addressData[$key]);
                }
                //Restore street as an array
                $addressData[\Magento\Quote\Api\Data\AddressInterface::KEY_STREET] = $street;
            }

            $updatedAddressStorage = SubjectReader::readUpdatedAddress($handlingSubject);
            if ($updatedAddressStorage instanceof \Magento\Framework\DataObject) {
                $updatedAddressStorage->setData($addressData);
            }

            try {
                $payment = $quote->getPayment();
                $payment->setMethod(\Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig::METHOD_CODE);
                if ($quote->isVirtual()) {
                    $billingAddress->setPaymentMethod($payment->getMethod());
                } else {
                    $shippingAddress->setPaymentMethod($payment->getMethod());
                }
                $quote->save();
            } catch (\Exception $e) {
                $this->logger->error('Unable to update quote: ' . $e->getMessage());
            }

            return $this;
        }
    }
}
