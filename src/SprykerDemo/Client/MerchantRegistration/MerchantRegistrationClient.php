<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Client\MerchantRegistration;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \SprykerDemo\Client\MerchantRegistration\MerchantRegistrationFactory getFactory()
 */
class MerchantRegistrationClient extends AbstractClient implements MerchantRegistrationClientInterface
{
 /**
  * {@inheritDoc}
  *
  * @api
  *
  * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
  *
  * @return \Generated\Shared\Transfer\MerchantResponseTransfer
  */
    public function registerMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        return $this->getFactory()
            ->createZedStub()
            ->registerMerchant($merchantTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function getMerchant(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantTransfer
    {
         return $this->getFactory()
            ->createZedStub()
            ->getMerchant($merchantCriteriaTransfer);
    }
}
