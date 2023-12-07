<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\Merchant;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Service\UtilText\UtilTextServiceInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig;

class MerchantCreator implements MerchantCreatorInterface
{
    /**
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    protected StoreFacadeInterface $storeFacade;

    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected LocaleFacadeInterface $localeFacade;

    /**
     * @var \Spryker\Service\UtilText\UtilTextServiceInterface
     */
    protected UtilTextServiceInterface $utilTextService;

    /**
     * @var \Spryker\Zed\Merchant\Business\MerchantFacadeInterface
     */
    protected MerchantFacadeInterface $merchantFacade;

    /**
     * @var \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface
     */
    protected StateMachineFacadeInterface $stateMachineFacade;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig
     */
    protected MerchantRegistrationConfig $config;

    /**
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     * @param \Spryker\Service\UtilText\UtilTextServiceInterface $utilTextService
     * @param \Spryker\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     * @param \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface $stateMachineFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig $config
     */
    public function __construct(
        StoreFacadeInterface $storeFacade,
        LocaleFacadeInterface $localeFacade,
        UtilTextServiceInterface $utilTextService,
        MerchantFacadeInterface $merchantFacade,
        StateMachineFacadeInterface $stateMachineFacade,
        MerchantRegistrationConfig $config
    ) {
        $this->storeFacade = $storeFacade;
        $this->localeFacade = $localeFacade;
        $this->utilTextService = $utilTextService;
        $this->merchantFacade = $merchantFacade;
        $this->stateMachineFacade = $stateMachineFacade;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function create(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantTransfer = $this->setStoreIdsByStoreName($merchantTransfer);
        $merchantTransfer = $this->expandMerchantWithUrls($merchantTransfer);
        $merchantTransfer = $this->setFkStateMachineProcess($merchantTransfer);

        return $this->merchantFacade->createMerchant($merchantTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantTransfer
     */
    protected function setFkStateMachineProcess(MerchantTransfer $merchantTransfer): MerchantTransfer
    {
        $merchantTransfer->setFkStateMachineProcess(
            $this->stateMachineFacade->getStateMachineProcessId(
                (new StateMachineProcessTransfer())->setProcessName($this->config->getMerchantOmsDefaultProcessName()),
            ),
        );

        return $merchantTransfer;
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

        if ($idStore && $merchantTransfer->getStoreRelation()) {
            $merchantTransfer->getStoreRelation()->setIdStores([$idStore]);
        }

        return $merchantTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\StoreRelationTransfer $storeRelationTransfer
     *
     * @return int|null
     */
    protected function getIdStore(StoreRelationTransfer $storeRelationTransfer): ?int
    {
        return $this->storeFacade->getStoreByName($storeRelationTransfer->getStores()[0]->getName())->getIdStore();
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

        if ($merchantTransfer->getName()) {
            foreach ($this->localeFacade->getLocaleCollection() as $locale) {
                $urlPrefix = $this->getLocalizedUrlPrefix($locale);
                $merchantTransfer->addUrl(
                    (new UrlTransfer())
                        ->setUrl($urlPrefix . $utilTextService->generateSlug($merchantTransfer->getName()))
                        ->setFkLocale($locale->getIdLocale()),
                );
            }
        }

        return $merchantTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return string
     */
    protected function getLocalizedUrlPrefix(LocaleTransfer $localeTransfer): string
    {
        if (!$localeTransfer->getLocaleName()) {
            return '/';
        }

        $localeNameParts = explode('_', $localeTransfer->getLocaleName());
        $languageCode = $localeNameParts[0];

        return '/' . $languageCode . '/' . $this->config->getMerchantUrlPrefix() . '/';
    }
}
