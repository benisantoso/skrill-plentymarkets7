<?php

namespace Skrill\Services;

use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Basket\Models\BasketItem;
use Plenty\Modules\Item\Item\Models\Item;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Basket\Contracts\BasketItemRepositoryContract;
use Plenty\Plugin\Log\Loggable;

class BasketService implements BasketServiceContract
{
    use Loggable;
    
    /** @var LibService */
    private $libService;

    /** @var AuthHelper */
    private $authHelper;

    /** @var AddressRepositoryContract */
    private $addressRepo;

    /** @var BasketRepositoryContract */
    private $basketRepo;

    /** @var CountryRepositoryContract */
    private $countryRepository;

    /** @var BasketItemRepositoryContract */
    private $basketItemRepo;

    /**
     * BasketService constructor.
     *
     * @param CountryRepositoryContract $countryRepository
     * @param AddressRepositoryContract $addressRepository
     * @param BasketRepositoryContract $basketRepo
     * @param LibService $libraryService
     * @param AuthHelper $authHelper
     */
    public function __construct(
        CountryRepositoryContract $countryRepository,
        AddressRepositoryContract $addressRepository,
        BasketRepositoryContract $basketRepo,
        AuthHelper $authHelper,
        BasketItemRepositoryContract $basketItemRepo
    ) {
        $this->authHelper          = $authHelper;
        $this->addressRepo         = $addressRepository;
        $this->basketRepo          = $basketRepo;
        $this->countryRepository   = $countryRepository;
        $this->basketItemRepo      = $basketItemRepo;
    }

    /**
     * {@inheritDoc}
     */
    public function shippingMatchesBillingAddress(): bool
    {
        $basket = $this->getBasket();
        if ($basket->customerShippingAddressId === null || $basket->customerShippingAddressId === -99) {
            return true;
        }

        $addresses = $this->getCustomerAddressData();
        $billingAddress = $addresses['billing']->toArray();
        $shippingAddress = $addresses['shipping']->toArray();

        return  $billingAddress['gender'] === $shippingAddress['gender'] &&
                $this->strCompare($billingAddress['address1'], $shippingAddress['address1']) &&
                $this->strCompare($billingAddress['address2'], $shippingAddress['address2']) &&
                $billingAddress['postalCode'] === $shippingAddress['postalCode'] &&
                $this->strCompare($billingAddress['town'], $shippingAddress['town']) &&
                $this->strCompare($billingAddress['countryId'], $shippingAddress['countryId']) &&
                (
                    ($this->isBasketB2B()  && $this->strCompare($billingAddress['name1'], $shippingAddress['name1'])) ||
                    (!$this->isBasketB2B() && $this->strCompare($billingAddress['name2'], $shippingAddress['name2'])
                                           && $this->strCompare($billingAddress['name3'], $shippingAddress['name3']))
                );
    }
        /**
     * Gathers address data (billing/invoice and shipping) and returns them as an array.
     *
     * @return Address[]
     */
    public function getCustomerAddressData(): array
    {
        $basket = $this->getBasket();

        $addresses            = [];
        $invoiceAddressId     = $basket->customerInvoiceAddressId;
        $addresses['billing'] = empty($invoiceAddressId) ? null : $this->getAddressById($invoiceAddressId);

        // if the shipping address is -99 or null, it is matching the billing address.
        $shippingAddressId = $basket->customerShippingAddressId;
        if (empty($shippingAddressId) || $shippingAddressId === -99) {
            $addresses['shipping'] = $addresses['billing'];
        } else {
            $addresses['shipping'] = $this->getAddressById($shippingAddressId);
        }

        return $addresses;
    }

    /**
     * Returns true if the billing address is B2B.
     *
     * @return bool
     */
    public function isBasketB2B(): bool
    {
        $billingAddress = $this->getCustomerAddressData()['billing'];
        return $billingAddress ? $billingAddress->gender === null : false;
    }

    /**
     * Fetches current basket and returns it.
     *
     * @return Basket
     */
    public function getBasket(): Basket
    {
        return $this->basketRepo->load();
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingCountryCode(): string
    {
        $billingAddress = $this->getCustomerAddressData()['billing'];
        return $billingAddress ?
            $this->countryRepository->findIsoCode($billingAddress->countryId, 'isoCode2') : '';
    }

    /**
     * Returns true if the strings match case insensitive.
     *
     * @param string $string1
     * @param string $string2
     * @return bool
     */
    private function strCompare($string1, $string2): bool
    {
        $symbols = [' ', '-', '.', '(', ')'];
        $normalizedString1 = str_replace($symbols, '', strtolower(trim($string1)));
        $normalizedString2 = str_replace($symbols, '', strtolower(trim($string2)));

        $specialChars = ['ä', 'ü', 'ö', 'ß'];
        $specialCharReplacements = ['ae', 'ue', 'oe', 'ss'];
        $normalizedString1 = str_replace($specialChars, $specialCharReplacements, $normalizedString1);
        $normalizedString2 = str_replace($specialChars, $specialCharReplacements, $normalizedString2);

        $normalizedString1 = str_replace('strasse', 'str', $normalizedString1);
        $normalizedString2 = str_replace('strasse', 'str', $normalizedString2);

        return $normalizedString1 === $normalizedString2;
    }

    /**
     * @param $addressId
     * @return Address|null
     */
    private function getAddressById($addressId)
    {
        /** @var AuthHelper $authHelper */
        $authHelper = pluginApp(AuthHelper::class);
        $address = $authHelper->processUnguarded(
            function () use ($addressId) {
                return $this->addressRepo->findAddressById($addressId);
            }
        );
        return $address;
    }

    /**
     * Get a basket item
     * @param int $basketItemId
     * @return array
     */
    public function getBasketItem(int $basketItemId): array
    {
        $basketItem = $this->basketItemRepo->findOneById($basketItemId);
        if ($basketItem === null) {
            return array();
        }
        $this->getLogger(__METHOD__)->error('Skrill:basketItem', $basketItem->toArray());
        return $basketItem->toArray();
    }
}
