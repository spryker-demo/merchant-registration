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
    public const VALIDATION_MESSAGE_EMAIL = 'Merchant email already exists';

    /**
     * @var string
     */
    public const VALIDATION_MESSAGE_NAME = 'Company name already exists';

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function registerMerchantAction(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantResponseTransfer = $this->checkMerchantEmailUniqueConstraint($merchantTransfer);
        $merchantResponseTransfer = $this->checkMerchantNameUniqueConstraint($merchantResponseTransfer, $merchantTransfer->getName());

        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setName($merchantTransfer->getName());

        if (count($merchantResponseTransfer->getErrors())) {
            return $merchantResponseTransfer;
        }

        return $this->getFacade()->merchantRegister($merchantTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function checkMerchantEmailUniqueConstraint(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setEmail($merchantTransfer->getEmail());
        $merchantWithEmail = $this->getFacade()->merchantExists($merchantCriteriaTransfer);
        $merchantResponseTransfer = new MerchantResponseTransfer();
        $merchantResponseTransfer->setMerchant($merchantWithEmail);

        if ($merchantWithEmail) {
            $merchantErrorTransfer = new MerchantErrorTransfer();
            $merchantErrorTransfer->setMessage(static::VALIDATION_MESSAGE_NAME);
            $merchantResponseTransfer->addError($merchantErrorTransfer);
        }

        return $merchantResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     * @param string $name
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function checkMerchantNameUniqueConstraint(MerchantResponseTransfer $merchantResponseTransfer, string $name): MerchantResponseTransfer
    {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setName($name);

        $merchantWithEmail = $this->getFacade()->merchantExists($merchantCriteriaTransfer);
        if ($merchantWithEmail) {
            $merchantErrorTransfer = new MerchantErrorTransfer();
            $merchantErrorTransfer->setMessage(static::VALIDATION_MESSAGE_NAME);
            $merchantResponseTransfer->addError($merchantErrorTransfer);
        }

        return $merchantResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function merchantExistsAction(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantTransfer
    {
        $merchantTransfer = $this->getFacade()->merchantExists($merchantCriteriaTransfer);

        if ($merchantTransfer === null) {
            return new MerchantTransfer();
        }

        return $merchantTransfer;
    }
}
