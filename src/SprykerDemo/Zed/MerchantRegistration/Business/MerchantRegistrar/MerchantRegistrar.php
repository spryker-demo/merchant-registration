<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use Generated\Shared\Transfer\MailTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\MerchantUserResponseTransfer;
use Generated\Shared\Transfer\MerchantUserTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Service\UtilText\UtilTextServiceInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use Spryker\Zed\User\Business\UserFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Communication\Plugin\Mail\MerchantRegistrationMailTypePlugin;

class MerchantRegistrar implements MerchantRegistrarInterface
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
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    protected StoreFacadeInterface $storeFacade;

    /**
     * @var \Spryker\Zed\User\Business\UserFacadeInterface
     */
    protected UserFacadeInterface $userFacade;

    /**
     * @var \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    protected MailFacadeInterface $mailFacade;

    /**
     * @var \Spryker\Service\UtilText\UtilTextServiceInterface
     */
    protected UtilTextServiceInterface $utilTextService;

    /**
     * @var \Spryker\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected MerchantFacadeInterface $merchantFacade;

    /**
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     * @param \Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface $merchantUserFacade
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\User\Business\UserFacadeInterface $userFacade
     * @param \Spryker\Zed\Mail\Business\MailFacadeInterface $mailFacade
     * @param \Spryker\Service\UtilText\UtilTextServiceInterface $utilTextService
     * @param \Spryker\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        LocaleFacadeInterface $localeFacade,
        MerchantUserFacadeInterface $merchantUserFacade,
        StoreFacadeInterface $storeFacade,
        UserFacadeInterface $userFacade,
        MailFacadeInterface $mailFacade,
        UtilTextServiceInterface $utilTextService,
        MerchantFacadeInterface $merchantFacade
    ) {
        $this->localeFacade = $localeFacade;
        $this->merchantUserFacade = $merchantUserFacade;
        $this->storeFacade = $storeFacade;
        $this->userFacade = $userFacade;
        $this->mailFacade = $mailFacade;
        $this->utilTextService = $utilTextService;
        $this->merchantFacade = $merchantFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function registerMerchant(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantTransfer = $this->setStoreIdsByStoreName($merchantTransfer);
        $merchantTransfer = $this->expandMerchantWithUrls($merchantTransfer);

        $merchantResponseTransfer = $this->merchantFacade->createMerchant($merchantTransfer);
        $merchantUserResponseTransfer = $this->createMerchantUserTransfer($merchantResponseTransfer, $merchantTransfer);
        if ($merchantUserResponseTransfer->getMerchantUser()) {
            $userTransfer = $merchantUserResponseTransfer->getMerchantUser()->getUser();
            if ($userTransfer) {
                $userTransfer->setPassword($merchantTransfer->getPassword());
                $userTransfer->setStatus(static::USER_STATUS_ACTIVE);
                $this->userFacade->updateUser($userTransfer);
            }
        }
        $this->sendRegistrationEmail($merchantTransfer);

        return $merchantResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    protected function setStoreIdsByStoreName(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        /** @var \Generated\Shared\Transfer\StoreRelationTransfer $storeRelationTransfer */
        $storeRelationTransfer = $merchantTransfer->getStoreRelation();

        $idStore = $this->getIdStore($storeRelationTransfer);

        if ($merchantTransfer->getStoreRelation()) {
            $merchantTransfer->getStoreRelation()->setIdStores([$idStore]);
        }

        return $merchantTransfer;
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

    /**
     * @param \Generated\Shared\Transfer\StoreRelationTransfer $storeRelationTransfer
     *
     * @return int
     */
    protected function getIdStore(StoreRelationTransfer $storeRelationTransfer): int
    {
        return $this->storeFacade->getStoreByName($storeRelationTransfer->getStores()[0]->getName())->getIdStore();
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return void
     */
    public function sendRegistrationEmail(MerchantTransfer $merchantTransfer): void
    {
        $mailTransfer = new MailTransfer();
        $mailTransfer->setType(MerchantRegistrationMailTypePlugin::MAIL_TYPE);
        $mailTransfer->setMerchant($merchantTransfer);
        $mailTransfer->setLocale($this->localeFacade->getCurrentLocale());

        $this->mailFacade->handleMail($mailTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    protected function expandMerchantWithUrls(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        $utilTextService = $this->utilTextService;
        $localeFacade = $this->localeFacade;

        $merchantTransfer->addUrl(
            (new UrlTransfer())
                ->setUrl('/en/merchant/' . $utilTextService->generateSlug($merchantTransfer->getName()))
                ->setFkLocale($localeFacade->getLocale('en_US')->getIdLocale()),
        );

        $merchantTransfer->addUrl(
            (new UrlTransfer())
                ->setUrl('/de/merchant/' . $utilTextService->generateSlug($merchantTransfer->getName()))
                ->setFkLocale($localeFacade->getLocale('de_DE')->getIdLocale()),
        );

        return $merchantTransfer;
    }
}
