<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder;

use ArrayObject;

interface MerchantUrlBuilderInterface
{
    /**
     * @param string $url
     *
     * @return \ArrayObject<\Generated\Shared\Transfer\UrlTransfer>
     */
    public function buildUrlCollection(string $url): ArrayObject;
}
