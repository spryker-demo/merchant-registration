<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantRegistrar;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantErrorTransfer;
use Generated\Shared\Transfer\MerchantResponseTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Zed\Glossary\Business\GlossaryFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use Spryker\Zed\Url\Business\UrlFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig;

class MerchantValidator implements MerchantValidatorInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE_PROVIDED_EMAIL_OR_COMPANY_NAME_IS_ALREADY_TAKEN = 'merchant.register-page.validation.company_name_or_email_not_unique';

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
     * @var \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig
     */
    protected MerchantRegistrationConfig $config;

    /**
     * @param \Spryker\Zed\Url\Business\UrlFacadeInterface $urlFacade
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     * @param \Spryker\Zed\Glossary\Business\GlossaryFacadeInterface $glossaryFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\Business\Merchant\MerchantFinderInterface $merchantFinder
     * @param \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig $config
     */
    public function __construct(
        UrlFacadeInterface $urlFacade,
        LocaleFacadeInterface $localeFacade,
        GlossaryFacadeInterface $glossaryFacade,
        MerchantFinderInterface $merchantFinder,
        MerchantRegistrationConfig $config
    ) {
        $this->urlFacade = $urlFacade;
        $this->localeFacade = $localeFacade;
        $this->glossaryFacade = $glossaryFacade;
        $this->merchantFinder = $merchantFinder;
        $this->config = $config;
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

        if ($merchantTransfer->getUrl()) {
            $merchantResponseTransfer = $this->validateUrlCollection($merchantTransfer->getUrl()->getUrl(), $merchantResponseTransfer);
        }

        return $merchantResponseTransfer;
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
     * @param string|null $url
     * @param \Generated\Shared\Transfer\MerchantResponseTransfer $merchantResponseTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantResponseTransfer
     */
    protected function validateUrlCollection(?string $url, MerchantResponseTransfer $merchantResponseTransfer): MerchantResponseTransfer
    {
        if (!$url) {
            return $merchantResponseTransfer;
        }
        foreach ($this->localeFacade->getLocaleCollection() as $localeTransfer) {
            $urlTransfer = $this->getUrlTransfer($url, $localeTransfer);
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

    /**
     * @param string $url
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\UrlTransfer
     */
    protected function getUrlTransfer(string $url, LocaleTransfer $localeTransfer): UrlTransfer
    {
        $urlTransfer = new UrlTransfer();
        $urlPrefix = $this->getLocalizedUrlPrefix($localeTransfer->getLocaleName());
        $urlTransfer->setUrl($urlPrefix . $url);

        return $urlTransfer;
    }

    /**
     * @param string|null $locale
     *
     * @return string
     */
    protected function getLocalizedUrlPrefix(?string $locale): string
    {
        if (!$locale) {
            return '';
        }
        $localeNameParts = explode('_', $locale);
        $languageCode = $localeNameParts[0];

        return '/' . $languageCode . '/' . $this->config->getMerchantUrlPrefix() . '/';
    }
}
