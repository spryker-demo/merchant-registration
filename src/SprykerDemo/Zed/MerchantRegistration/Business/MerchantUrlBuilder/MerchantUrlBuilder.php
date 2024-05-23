<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerDemo\Zed\MerchantRegistration\Business\MerchantUrlBuilder;

use ArrayObject;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\UrlTransfer;
use Spryker\Service\UtilText\UtilTextServiceInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig;

class MerchantUrlBuilder implements MerchantUrlBuilderInterface
{
    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected LocaleFacadeInterface $localeFacade;

    /**
     * @var \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig
     */
    protected MerchantRegistrationConfig $config;

    /**
     * @var \Spryker\Service\UtilText\UtilTextServiceInterface
     */
    protected UtilTextServiceInterface $utilTextService;

    /**
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     * @param \SprykerDemo\Zed\MerchantRegistration\MerchantRegistrationConfig $config
     * @param \Spryker\Service\UtilText\UtilTextServiceInterface $utilTextService
     */
    public function __construct(
        LocaleFacadeInterface $localeFacade,
        MerchantRegistrationConfig $config,
        UtilTextServiceInterface $utilTextService
    ) {
        $this->localeFacade = $localeFacade;
        $this->config = $config;
        $this->utilTextService = $utilTextService;
    }

    /**
     * @param string $url
     *
     * @return \ArrayObject<\Generated\Shared\Transfer\UrlTransfer>
     */
    public function buildUrlCollection(string $url): ArrayObject
    {
        $urlCollection = new ArrayObject();
        foreach ($this->localeFacade->getLocaleCollection() as $localeTransfer) {
            $urlTransfer = $this->buildUrlTransfer($url, $localeTransfer);
            $urlCollection->append($urlTransfer);
        }

        return $urlCollection;
    }

    /**
     * @param string $url
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\UrlTransfer
     */
    protected function buildUrlTransfer(string $url, LocaleTransfer $localeTransfer): UrlTransfer
    {
        $urlTransfer = new UrlTransfer();
        $urlPrefix = $this->getLocalizedUrlPrefix($localeTransfer->getLocaleName());
        $urlTransfer->setUrl($urlPrefix . $this->utilTextService->generateSlug($url));
        $urlTransfer->setFkLocale($localeTransfer->getIdLocale());

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

        return sprintf('/%s/%s/', $languageCode, $this->config->getMerchantUrlPrefix());
    }
}
