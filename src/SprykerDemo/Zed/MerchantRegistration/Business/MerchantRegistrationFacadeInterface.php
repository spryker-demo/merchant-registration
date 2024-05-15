<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantRegistrationFacadeInterface
{
    /**
     * Specification:
     * - Registers a new merchant.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function registerMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer;

    /**
     * Specification:
     * - Validates merchant data before creating new merchant.
     * - Returns `MerchantResponseTransfer` with error messages if exists.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function validateMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer;
}
