<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Communication\Controller;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrationFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
    /**
     * @var string
     */
    public const VALIDATION_MESSAGE = 'Merchant email and Company name must be unique!';

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function registerMerchantAction(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantResponseTransfer = $this->getFacade()->validateMerchant($merchantTransfer);

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setName($merchantTransfer->getName());

        if (count($merchantResponseTransfer->getErrors())) {
            return $merchantResponseTransfer;
        }

        return $this->getFacade()->registerMerchant($merchantTransfer);
    }
}
