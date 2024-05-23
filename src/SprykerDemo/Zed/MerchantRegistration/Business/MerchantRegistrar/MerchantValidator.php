<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantErrorTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Zed\Glossary\Business\GlossaryFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Url\Business\UrlFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder\MerchantUrlBuilderInterface;

class MerchantValidator implements MerchantValidatorInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PROVIDED_EMAIL_OR_COMPANY_NAME_IS_ALREADY_TAKEN = 'merchant.register-page.validation.company_name_or_email_not_unique';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_PROVIDED_URL_IS_ALREADY_TAKEN = 'merchant.register-page.validation.url_not_unique';

    /**
     * @var \Spryker\Zed\Url\Business\UrlFacadeInterface
     */
    protected UrlFacadeInterface $urlFacade;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface
     */
    protected MerchantFinderInterface $merchantFinder;

    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected LocaleFacadeInterface $localeFacade;

    /**
     * @var \Spryker\Zed\Glossary\Business\GlossaryFacadeInterface
     */
    protected GlossaryFacadeInterface $glossaryFacade;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder\MerchantUrlBuilderInterface
     */
    protected MerchantUrlBuilderInterface $merchantUrlBuilder;

    /**
     * @param \Spryker\Zed\Url\Business\UrlFacadeInterface $urlFacade
     * @param \Spryker\Zed\Glossary\Business\GlossaryFacadeInterface $glossaryFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface $merchantFinder
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder\MerchantUrlBuilderInterface $merchantUrlBuilder
     */
    public function __construct(
        UrlFacadeInterface $urlFacade,
        GlossaryFacadeInterface $glossaryFacade,
        MerchantFinderInterface $merchantFinder,
        MerchantUrlBuilderInterface $merchantUrlBuilder
    ) {
        $this->urlFacade = $urlFacade;
        $this->glossaryFacade = $glossaryFacade;
        $this->merchantFinder = $merchantFinder;
        $this->merchantUrlBuilder = $merchantUrlBuilder;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    public function validate(MerchantTransfer $merchantTransfer): MerchantResponseTransfer
    {
        $merchantResponseTransfer = new MerchantResponseTransfer();
        $merchantResponseTransfer->setMerchant($merchantTransfer);

        $merchantResponseTransfer = $this->validateMerchant($merchantTransfer, $merchantResponseTransfer);
        $merchantResponseTransfer = $this->validateUrlCollection(
            $merchantTransfer->getUrlOrFail()->getUrlOrFail(),
            $merchantResponseTransfer,
        );

        return $merchantResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\MerchantTransfer $merchantTransfer
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function validateMerchant(
        MerchantTransfer $merchantTransfer,
        MerchantResponseTransfer $merchantResponseTransfer
    ): MerchantResponseTransfer {
        $merchantCriteriaTransfer = new MerchantCriteriaTransfer();
        $merchantCriteriaTransfer->setEmail($merchantTransfer->getEmail());
        $merchantCriteriaTransfer->setName($merchantTransfer->getName());

        $merchantTransfer = $this->merchantFinder->find($merchantCriteriaTransfer);

        if ($merchantTransfer) {
            $merchantErrorTransfer = new MerchantErrorTransfer();
            $merchantErrorTransfer->setMessage(static::ERROR_MESSAGE_PROVIDED_EMAIL_OR_COMPANY_NAME_IS_ALREADY_TAKEN);
            $merchantResponseTransfer->addError($merchantErrorTransfer);
        }

        return $merchantResponseTransfer;
    }

    /**
     * @param string $url
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function validateUrlCollection(string $url, MerchantResponseTransfer $merchantResponseTransfer): MerchantResponseTransfer
    {
        foreach ($this->merchantUrlBuilder->buildUrlCollection($url) as $urlTransfer) {
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
        $merchantErrorTransfer->setMessage(sprintf($this->glossaryFacade->translate(static::ERROR_MESSAGE_PROVIDED_URL_IS_ALREADY_TAKEN), $urlTransfer->getUrl()));
        $merchantResponseTransfer->addError($merchantErrorTransfer);

        return $merchantResponseTransfer;
    }
}
