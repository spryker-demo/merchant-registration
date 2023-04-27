<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business;

use Spryker\Service\UtilText\UtilTextServiceInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use Spryker\Zed\User\Business\UserFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrar;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarInterface;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationDependencyProvider;

class MerchantRegistrationBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarInterface
     */
    public function createMerchantRegistrar(): MerchantRegistrarInterface
    {
        return new MerchantRegistrar(
            $this->getLocaleFacade(),
            $this->getMerchantUserFacade(),
            $this->getStoreFacade(),
            $this->getUserFacade(),
            $this->getMailFacade(),
            $this->getUtilTextService(),
            $this->getMerchantFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    public function getStoreFacade(): StoreFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    public function getLocaleFacade(): LocaleFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface
     */
    public function getMerchantUserFacade(): MerchantUserFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_MERCHANT_USER);
    }

    /**
     * @return \Spryker\Zed\User\Business\UserFacadeInterface
     */
    public function getUserFacade(): UserFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_USER);
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface
     */
    public function getStateMachineFacade(): StateMachineFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_STATE_MACHINE);
    }

    /**
     * @return \Spryker\Zed\Mail\Business\MailFacadeInterface
     */
    public function getMailFacade(): MailFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_MAIL);
    }

    /**
     * @return \Spryker\Service\UtilText\UtilTextServiceInterface
     */
    public function getUtilTextService(): UtilTextServiceInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::SERVICE_UTIL_TEXT);
    }

    /**
     * @return \Spryker\Zed\Merchant\Business\MerchantFacadeInterface
     */
    public function getMerchantFacade(): MerchantFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_MERCHANT);
    }
}
