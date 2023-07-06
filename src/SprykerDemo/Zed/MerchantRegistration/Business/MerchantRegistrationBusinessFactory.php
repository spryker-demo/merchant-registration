<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business;

use Orm\Zed\Merchant\Persistence\SpyMerchantQuery;
use Spryker\Service\UtilText\UtilTextServiceInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Mail\Business\MailFacadeInterface;
use Spryker\Zed\Merchant\Business\MerchantFacadeInterface;
use Spryker\Zed\MerchantUser\Business\MerchantUserFacadeInterface;
use Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface;
use Spryker\Zed\Store\Business\StoreFacadeInterface;
use Spryker\Zed\User\Business\UserFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantCreator;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantCreatorInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinder;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrar;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarMailer;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarMailerInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserCreator;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserCreatorInterface;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationDependencyProvider;

/**
 * @method \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig getConfig()
 */
class MerchantRegistrationBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarInterface
     */
    public function createMerchantRegistrar(): MerchantRegistrarInterface
    {
        return new MerchantRegistrar(
            $this->createMerchantCreator(),
            $this->createMerchantUserCreator(),
            $this->createMerchantRegistrarMailer(),
        );
    }

    /**
     * @return \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUser\MerchantUserCreatorInterface
     */
    public function createMerchantUserCreator(): MerchantUserCreatorInterface
    {
        return new MerchantUserCreator(
            $this->getLocaleFacade(),
            $this->getMerchantUserFacade(),
            $this->getUserFacade(),
        );
    }

    /**
     * @return \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantCreatorInterface
     */
    public function createMerchantCreator(): MerchantCreatorInterface
    {
        return new MerchantCreator(
            $this->getStoreFacade(),
            $this->getLocaleFacade(),
            $this->getUtilTextService(),
            $this->getMerchantFacade(),
            $this->getStateMachineFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar\MerchantRegistrarMailerInterface
     */
    public function createMerchantRegistrarMailer(): MerchantRegistrarMailerInterface
    {
        return new MerchantRegistrarMailer(
            $this->getMailFacade(),
            $this->getLocaleFacade(),
            $this->getConfig(),
        );
    }

    /**
     * @return \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface
     */
    public function createMerchantFinder(): MerchantFinderInterface
    {
        return new MerchantFinder(
            $this->getPropelMerchantQuery(),
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

    /**
     * @return \Orm\Zed\Merchant\Persistence\SpyMerchantQuery
     */
    public function getPropelMerchantQuery(): SpyMerchantQuery
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::PROPEL_MERCHANT_QUERY);
    }

    /**
     * @return \Spryker\Zed\StateMachine\Business\StateMachineFacadeInterface
     */
    public function getStateMachineFacade(): StateMachineFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_STATE_MACHINE);
    }
}
