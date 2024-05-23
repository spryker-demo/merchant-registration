<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\Merchant;

use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder\MerchantUrlBuilderInterface;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig;

class MerchantCreator implements MerchantCreatorInterface
{
    /**
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    protected StoreFacadeInterface $storeFacade;

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
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder\MerchantUrlBuilderInterface
     */
    protected MerchantUrlBuilderInterface $merchantUrlBuilder;

    /**
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\Merchant\Business\MerchantFacadeInterface $merchantFacade
     * @param \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface $stateMachineFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig $config
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder\MerchantUrlBuilderInterface $merchantUrlBuilder
     */
    public function __construct(
        StoreFacadeInterface $storeFacade,
        MerchantFacadeInterface $merchantFacade,
        StateMachineFacadeInterface $stateMachineFacade,
        MerchantRegistrationConfig $config,
        MerchantUrlBuilderInterface $merchantUrlBuilder
    ) {
        $this->storeFacade = $storeFacade;
        $this->merchantFacade = $merchantFacade;
        $this->stateMachineFacade = $stateMachineFacade;
        $this->config = $config;
        $this->merchantUrlBuilder = $merchantUrlBuilder;
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
        $url = $merchantTransfer->getUrlOrFail()->getUrlOrFail();
        $merchantTransfer->setUrlCollection(
            $this->merchantUrlBuilder->buildUrlCollection($url),
        );

        return $merchantTransfer;
    }
}
