<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\Merchant;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;

class MerchantFinder implements MerchantFinderInterface
{
 /**
  * @var \Spryker\Zed\Merchant\Business\MerchantFacadeInterface
  */
    protected MerchantFacadeInterface $merchantFacade;

    /**
     * @param \Spryker\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(MerchantFacadeInterface $merchantFacade)
    {
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    public function find(MerchantCriteriaTransfer $merchantCriteriaTransfer): MerchantTransfer
    {
        $merchantTransfer = $this->merchantFacade->findOne($merchantCriteriaTransfer);

        return $merchantTransfer ?? new MerchantTransfer();
    }
}
