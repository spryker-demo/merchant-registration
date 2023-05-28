<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantCreatorInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserCreatorInterface;

/**
 * @method \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig getConfig
 */
class MerchantRegistrar implements MerchantRegistrarInterface
{
    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_ACTIVE
     *
     * @var string
     */
    protected const USER_STATUS_ACTIVE = 'active';

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantCreatorInterface
     */
    protected MerchantCreatorInterface $merchantCreator;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserCreatorInterface
     */
    protected MerchantUserCreatorInterface $merchantUserCreator;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarMailerInterface
     */
    protected MerchantRegistrarMailerInterface $merchantRegistrarMailer;

    /**
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantCreatorInterface $merchantCreator
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserCreatorInterface $merchantUserCreator
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarMailerInterface $merchantRegistrarMailer
     */
    public function __construct(
        MerchantCreatorInterface $merchantCreator,
        MerchantUserCreatorInterface $merchantUserCreator,
        MerchantRegistrarMailerInterface $merchantRegistrarMailer
    ) {
        $this->merchantCreator = $merchantCreator;
        $this->merchantUserCreator = $merchantUserCreator;
        $this->merchantRegistrarMailer = $merchantRegistrarMailer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function registerMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantResponseTransfer = $this->merchantCreator->create($merchantTransfer);
        $this->merchantUserCreator->create($merchantTransfer, $merchantResponseTransfer);
        $this->merchantRegistrarMailer->sendRegistrationEmail($merchantTransfer);

        return $merchantResponseTransfer;
    }
}
