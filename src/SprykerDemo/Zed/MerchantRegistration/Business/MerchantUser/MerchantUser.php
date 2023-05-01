<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUserResponseTransfer;
use Generated\Shared\Transfer\MerchantUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface;
use Spryker\Zed\User\Business\UserFacadeInterface;

class MerchantUser implements MerchantUserInterface
{
    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_ACTIVE
     *
     * @var string
     */
    protected const USER_STATUS_ACTIVE = 'active';

    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected LocaleFacadeInterface $localeFacade;

    /**
     * @var \Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface
     */
    protected MerchantUserFacadeInterface $merchantUserFacade;

    /**
     * @var \Spryker\Zed\User\Business\UserFacadeInterface
     */
    protected UserFacadeInterface $userFacade;

    /**
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     * @param \Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface $merchantUserFacade
     * @param \Spryker\Zed\User\Business\UserFacadeInterface $userFacade
     */
    public function __construct(
        LocaleFacadeInterface $localeFacade,
        MerchantUserFacadeInterface $merchantUserFacade,
        UserFacadeInterface $userFacade
    ) {
        $this->localeFacade = $localeFacade;
        $this->merchantUserFacade = $merchantUserFacade;
        $this->userFacade = $userFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return void
     */
    public function add(MerchantTransfer $merchantTransfer, MerchantResponseTransfer $merchantResponseTransfer): void
    {
        $merchantUserResponseTransfer = $this->createMerchantUserTransfer($merchantResponseTransfer, $merchantTransfer);
        if ($merchantUserResponseTransfer->getMerchantUser()) {
            $userTransfer = $merchantUserResponseTransfer->getMerchantUser()->getUser();
            if ($userTransfer) {
                $userTransfer->setPassword($merchantTransfer->getPassword());
                $userTransfer->setStatus(static::USER_STATUS_ACTIVE);
                $this->userFacade->updateUser($userTransfer);
            }
        }
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    protected function createUserTransfer(MerchantTransfer $merchantTransfer): UserTransfer
    {
        $userTransfer = new UserTransfer();
        $userTransfer->setFkLocale($this->localeFacade->getCurrentLocale()->getIdLocale());
        $userTransfer->setUsername($merchantTransfer->getEmail());
        if ($merchantTransfer->getMerchantProfile()) {
            $userTransfer->setFirstName($merchantTransfer->getMerchantProfile()->getContactPersonFirstName());
            $userTransfer->setLastName($merchantTransfer->getMerchantProfile()->getContactPersonLastName());
        }
        $userTransfer->setPassword($merchantTransfer->getPassword());
        $userTransfer->setStatus(static::USER_STATUS_ACTIVE);

        return $userTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantUserResponseTransfer
     */
    protected function createMerchantUserTransfer(
        MerchantResponseTransfer $merchantResponseTransfer,
        MerchantTransfer $merchantTransfer
    ): MerchantUserResponseTransfer {
        $merchantUserTransfer = new MerchantUserTransfer();
        $merchantUserTransfer->setMerchant($merchantResponseTransfer->getMerchant());
        $merchantUserTransfer->setUser($this->createUserTransfer($merchantTransfer));
        if ($merchantResponseTransfer->getMerchant()) {
            $merchantUserTransfer->setIdMerchant($merchantResponseTransfer->getMerchant()->getIdMerchant());
        }

        return $this->merchantUserFacade->createMerchantUser($merchantUserTransfer);
    }
}
