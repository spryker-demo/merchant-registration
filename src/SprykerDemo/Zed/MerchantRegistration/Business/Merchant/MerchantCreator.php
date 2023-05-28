<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\Merchant;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Service\UtilText\UtilTextServiceInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;

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
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     * @param \Spryker\Service\UtilText\UtilTextServiceInterface $utilTextService
     * @param \Spryker\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     */
    public function __construct(
        StoreFacadeInterface $storeFacade,
        LocaleFacadeInterface $localeFacade,
        UtilTextServiceInterface $utilTextService,
        MerchantFacadeInterface $merchantFacade
    ) {
        $this->storeFacade = $storeFacade;
        $this->localeFacade = $localeFacade;
        $this->utilTextService = $utilTextService;
        $this->merchantFacade = $merchantFacade;
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

        return $this->merchantFacade->createMerchant($merchantTransfer);
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
        }

        return $merchantTransfer;
    }
}
