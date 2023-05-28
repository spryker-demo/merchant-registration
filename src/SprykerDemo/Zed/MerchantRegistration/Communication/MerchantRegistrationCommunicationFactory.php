<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Communication;

use Spryker\Zed\Glossary\Business\GlossaryFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationDependencyProvider;

class MerchantRegistrationCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Spryker\Zed\Glossary\Business\GlossaryFacadeInterface
     */
    public function getGlossaryFacade(): GlossaryFacadeInterface
    {
        return $this->getProvidedDependency(MerchantRegistrationDependencyProvider::FACADE_GLOSSARY);
    }
}
