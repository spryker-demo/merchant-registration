<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Client\MerchantRegistration;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ZedRequest\ZedRequestClient;
use SprykerDemo\Client\MerchantRegistration\Zed\MerchantRegistrationStub;
use SprykerDemo\Client\MerchantRegistration\Zed\MerchantRegistrationStubInterface;

class MerchantRegistrationFactory extends AbstractFactory
{
    /**
     * @return \SprykerDemo\Client\MerchantRegistration\Zed\MerchantRegistrationStubInterface
     */
    public function createZedStub(): MerchantRegistrationStubInterface
    {
        return new MerchantRegistrationStub(
            $this->getZedRequestClient(),
        );
    }

    /**
     * @return \Spryker\Client\ZedRequest\ZedRequestClient
     */
    public function getZedRequestClient(): ZedRequestClient
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::SERVICE_ZED);
    }
}
