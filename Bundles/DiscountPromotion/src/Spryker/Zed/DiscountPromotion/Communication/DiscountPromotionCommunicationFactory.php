<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\DiscountPromotion\Communication;

use Spryker\Zed\DiscountPromotion\Communication\Form\DiscountPromotionFormType;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;

/**
 * @method \Spryker\Zed\DiscountPromotion\DiscountPromotionConfig getConfig()
 * @method \Spryker\Zed\DiscountPromotion\Persistence\DiscountPromotionQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\DiscountPromotion\Business\DiscountPromotionFacadeInterface getFacade()
 */
class DiscountPromotionCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Spryker\Zed\DiscountPromotion\Communication\Form\DiscountPromotionFormType|\Symfony\Component\Form\FormTypeInterface
     */
    public function createDiscountFormPromotionType()
    {
        return new DiscountPromotionFormType();
    }
}