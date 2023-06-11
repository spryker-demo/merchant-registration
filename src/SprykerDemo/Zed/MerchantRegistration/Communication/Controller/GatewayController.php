<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Communication\Controller;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantErrorTransfer;
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
        $merchantResponseTransfer = $this->checkMerchantUniqueConstraint($merchantTransfer);

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setName($merchantTransfer->getName());

        if (count($merchantResponseTransfer->getErrors())) {
            return $merchantResponseTransfer;
        }

        return $this->getFacade()->registerMerchant($merchantTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function checkMerchantUniqueConstraint(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setEmail($merchantTransfer->getEmail());
        $merchantCriteriaTransfer->setName($merchantTransfer->getName());

        $merchant = $this->getFacade()->findMerchant($merchantCriteriaTransfer);
        $merchantResponseTransfer = new MerchantResponseTransfer();
        $merchantResponseTransfer->setMerchant($merchantTransfer);

        if ($merchant) {
            $merchantErrorTransfer = new MerchantErrorTransfer();
            $merchantErrorTransfer->setMessage(static::VALIDATION_MESSAGE);
            $merchantResponseTransfer->addError($merchantErrorTransfer);
        }

        return $merchantResponseTransfer;
    }
}
