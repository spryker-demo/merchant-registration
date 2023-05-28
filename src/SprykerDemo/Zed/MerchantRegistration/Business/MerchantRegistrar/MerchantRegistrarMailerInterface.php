<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use Generated\Shared\Transfer\MerchantTransfer;

interface MerchantRegistrarMailerInterface
{
    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return void
     */
    public function sendRegistrationEmail(MerchantTransfer $merchantTransfer): void;
}
