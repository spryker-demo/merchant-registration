<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use ArrayObject;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantErrorTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Zed\Url\Business\UrlFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface;

class MerchantValidator implements MerchantValidatorInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE_PROVIDED_EMAIL_OR_COMPANY_NAME_IS_ALREADY_TAKEN = 'Merchant email and Company name must be unique!';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PROVIDED_URL_IS_ALREADY_TAKEN = 'Provided URL "%s" is already taken.';

    /**
     * @var \Spryker\Zed\Url\Business\UrlFacadeInterface
     */
    protected UrlFacadeInterface $urlFacade;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface
     */
    protected MerchantFinderInterface $merchantFinder;

    /**
     * @param \Spryker\Zed\Url\Business\UrlFacadeInterface $urlFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface $merchantFinder
     */
    public function __construct(
        UrlFacadeInterface $urlFacade,
        MerchantFinderInterface $merchantFinder
    ) {
        $this->urlFacade = $urlFacade;
        $this->merchantFinder = $merchantFinder;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function validate(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setEmail($merchantTransfer->getEmail());
        $merchantCriteriaTransfer->setName($merchantTransfer->getName());

        $merchantResponseTransfer = new MerchantResponseTransfer();
        $merchantResponseTransfer->setMerchant($merchantTransfer);
        $merchantResponseTransfer = $this->validateMerchantData($merchantCriteriaTransfer, $merchantResponseTransfer);

        return $this->validateUrlCollection($merchantTransfer->getUrlCollection(), $merchantResponseTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function validateMerchantData(
        MerchantCriteriaTransfer $merchantCriteriaTransfer,
        MerchantResponseTransfer $merchantResponseTransfer
    ): MerchantResponseTransfer {
        $merchant = $this->merchantFinder->find($merchantCriteriaTransfer);

        if ($merchant) {
            $merchantErrorTransfer = new MerchantErrorTransfer();
            $merchantErrorTransfer->setMessage(static::ERROR_MESSAGE_PROVIDED_EMAIL_OR_COMPANY_NAME_IS_ALREADY_TAKEN);
            $merchantResponseTransfer->addError($merchantErrorTransfer);
        }

        return $merchantResponseTransfer;
    }

    /**
     * @param \ArrayObject<\Generated\Shared\Transfer\UrlTransfer> $urlCollectionTransfer
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function validateUrlCollection(ArrayObject $urlCollectionTransfer, MerchantResponseTransfer $merchantResponseTransfer): MerchantResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\UrlTransfer $urlTransfer */
        foreach ($urlCollectionTransfer as $urlTransfer) {
            $merchantResponseTransfer = $this->validateUrl($urlTransfer, $merchantResponseTransfer);
        }

        return $merchantResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UrlTransfer $urlTransfer
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function validateUrl(UrlTransfer $urlTransfer, MerchantResponseTransfer $merchantResponseTransfer): MerchantResponseTransfer
    {
        $existingUrlTransfer = $this->urlFacade->findUrlCaseInsensitive($urlTransfer);

        if ($existingUrlTransfer === null) {
            return $merchantResponseTransfer;
        }

        $merchantErrorTransfer = new MerchantErrorTransfer();
        $merchantErrorTransfer->setMessage(sprintf(static::ERROR_MESSAGE_PROVIDED_URL_IS_ALREADY_TAKEN, $urlTransfer->getUrl()));
        $merchantResponseTransfer->addError($merchantErrorTransfer);

        return $merchantResponseTransfer;
    }
}
