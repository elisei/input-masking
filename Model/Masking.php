<?php
/**
 * Copyright © O2TI. All rights reserved.
 *
 * @author    Bruno Elisei <brunoelisei@o2ti.com>
 * See COPYING.txt for license details.
 */

namespace O2TI\InputMasking\Model;

use O2TI\InputMasking\Helper\Config;

/**
 *  Masking - Add Masking Compoments for Inputs.
 */
class Masking
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Change Components Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function changeComponentFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ($key === 'postcode') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];
                $fields[$key]['sortOrder'] = $defaultPosition;
                $fields[$key]['component'] = 'O2TI_InputMasking/js/view/form/element/postcode';
            } elseif ($key === 'vat_id' || $key === 'taxvat') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];
                $fields[$key]['sortOrder'] = $defaultPosition;
                $fields[$key]['component'] = 'O2TI_InputMasking/js/view/form/element/fiscal_document';
            } elseif ($key === 'telephone') {
                $defaultPosition = (int) $fields[$key]['sortOrder'];
                $fields[$key]['sortOrder'] = $defaultPosition;
                $fields[$key]['component'] = 'O2TI_InputMasking/js/view/form/element/telephone';
            }
            continue;
        }

        return $fields;
    }

    /**
     * Change Components at Create Mask to Address Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function createMaskToAddressFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ((!isset($data['mask']) || !$data['mask'])) {
                $useMask = $this->config->getConfigAddressInput($key, 'enable_mask');

                if ($useMask) {
                    $mask = $this->config->getConfigAddressInput($key, 'mask');
                    $cleanIfNotMatch = $this->config->getConfigAddressInput($key, 'clean_if_not_match');
                    $fields = $this->insertElementsMask($fields, $data, $key, $mask, $cleanIfNotMatch);
                }
            }
        }

        return $fields;
    }

    /**
     * Change Components at Create Mask to Account Fields.
     *
     * @param array $fields
     *
     * @return array
     */
    public function createMaskToAccountFields(array $fields): array
    {
        foreach ($fields as $key => $data) {
            if ((!isset($data['mask']) || !$data['mask'])) {
                $useMask = $this->config->getConfigCustomerInput($key, 'enable_mask');

                if ($useMask) {
                    $mask = $this->config->getConfigCustomerInput($key, 'mask');
                    $cleanIfNotMatch = $this->config->getConfigCustomerInput($key, 'clean_if_not_match');
                    $fields = $this->insertElementsMask($fields, $data, $key, $mask, $cleanIfNotMatch);
                }
            }
        }

        return $fields;
    }

    /**
     * Insert Mask to Field.
     *
     * @param array  $fields
     * @param array  $data
     * @param string $field
     * @param string $mask
     * @param string $cleanIfNotMatch
     *
     * @return array
     */
    public function insertElementsMask(
        array $fields,
        array $data,
        string $field,
        string $mask,
        string $cleanIfNotMatch
    ): array {
        if (isset($data['type']) && $data['type'] === 'group'
            && isset($data['children']) && !empty($data['children'])
        ) {
            foreach ($data['children'] as $childrenKey => $childrenData) {
                if (!isset($data['mask']) || !$data['mask']) {
                    $fields[$field]['children'][$childrenKey]['mask'] = $mask;
                    $fields[$field]['children'][$childrenKey]['maskEnable'] = 1;
                    $fields[$field]['children'][$childrenKey]['maskClearIfNotMatch'] = $cleanIfNotMatch;
                }
                $childrenData = $childrenData;
            }
        } else {
            $fields[$field]['mask'] = $mask;
            $fields[$field]['maskEnable'] = 1;
            $fields[$field]['maskClearIfNotMatch'] = $cleanIfNotMatch;
        }

        return $fields;
    }
}
