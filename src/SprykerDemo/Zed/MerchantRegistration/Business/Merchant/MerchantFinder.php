<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\Merchant;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;

class MerchantFinder implements MerchantFinderInterface
{
 /**
  * @var \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
  */
    protected SpyMerchantQuery $spyMerchantQuery;

    /**
     * @param \Orm\Zed\Merchant\Persistence\SpyMerchantQuery $spyMerchantQuery
     */
    public function __construct(SpyMerchantQuery $spyMerchantQuery)
    {
        $this->spyMerchantQuery = $spyMerchantQuery;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer|null
     */
    public function find(MerchantCriteriaTransfer $merchantCriteriaTransfer): ?MerchantTransfer
    {
        $merchantQuery = $this->spyMerchantQuery;

        if ($merchantCriteriaTransfer->getEmail()) {
            $merchantQuery->filterByEmail($merchantCriteriaTransfer->getEmail());
        }
        if ($merchantCriteriaTransfer->getName()) {
            $merchantQuery->_or()
                ->filterByName_Like($merchantCriteriaTransfer->getName());
        }
        $merchantEntity = $merchantQuery->findOne();
        if ($merchantEntity) {
            $merchantTransfer = new MerchantTransfer();
            $merchantTransfer->setEmail($merchantCriteriaTransfer->getEmail());
            $merchantTransfer->setName('%' . $merchantCriteriaTransfer->getName() . '%');

            return $merchantTransfer;
        }

        return null;
    }
}
