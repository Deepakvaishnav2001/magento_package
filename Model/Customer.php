<?php

namespace Sharika\CustomerImport\Model;

use Sharika\CustomerImport\Model\Importer\CustomerImport;

class Customer
{
    /**
     * @var CustomerImport
     */
    private $customerImport;

    /**
     * Customer constructor
     *
     * @param CustomerImport $customerImport
     */
    public function __construct(
        CustomerImport $customerImport
    ) {
        $this->customerImport = $customerImport;
    }

    /**
     * Create customer using input data
     *
     * @param array $data
     * @param int $websiteId
     * @param int $storeId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCustomer(array $data, int $websiteId, int $storeId): void
    {
        if (empty($data['emailaddress'])
            || empty($data['fname'])
            || empty($data['lname'])
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Invalid data provided in import file"));
        }
        try {
            // Get the customer data
            $customerData = [
                'email'         => $data['emailaddress'],
                '_website'      => 'base',
                '_store'        => 'default',
                'firstname'     => $data['fname'],
                'group_id'      => 1, //General Group
                'lastname'      => $data['lname'],
                'store_id'      => $storeId,
                'website_id'    => $websiteId,
            ];

            // Save the customer data
            $this->customerImport->importCustomerData($customerData);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Something went wrong."));
        }
    }
}
