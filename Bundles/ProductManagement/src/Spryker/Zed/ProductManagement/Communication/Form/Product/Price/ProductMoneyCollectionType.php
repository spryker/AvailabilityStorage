<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductManagement\Communication\Form\Product\Price;

use Generated\Shared\Transfer\MoneyValueTransfer;
use Spryker\Shared\ProductManagement\ProductManagementConstants;
use Spryker\Zed\Kernel\Communication\Form\AbstractCollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @method \Spryker\Zed\ProductManagement\Communication\ProductManagementCommunicationFactory getFactory()
 */
class ProductMoneyCollectionType extends AbstractCollectionType
{
    /**
     * @var string
     */
    protected static $netPriceModeIdentifier;

    /**
     * @var string
     */
    protected static $grossPriceModeIdentifier;

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param string[] $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultOptions = [
            'entry_options' => [
                'data_class' => MoneyValueTransfer::class,
            ],
            'entry_type' => $this->getFactory()->getMoneyFormTypePlugin()->getType(),
        ];

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $this->setInitialMoneyValueData($event);
            }
        );

        parent::buildForm($builder, array_replace_recursive($defaultOptions, $options));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'product_money_collection';
    }

    /**
     * @param \Symfony\Component\Form\FormEvent $event
     *
     * @return void
     */
    protected function setInitialMoneyValueData(FormEvent $event)
    {
        $moneyCollectionInitialDataProvider = $this->getFactory()->createMoneyCollectionMultiStoreDataProvider();

        if (count($event->getData()) === 0) {
            $event->setData($moneyCollectionInitialDataProvider->getInitialData());
            return;
        }

        $event->setData(
            $moneyCollectionInitialDataProvider->mergeMissingMoneyValues($event->getData())
        );
    }

    /**
     * Builds table for view:
     * [
     *    'store1' => [
     *       'EUR' => [
     *          'NET_MODE' => [
     *              'DEFAULT' => FormView,
     *              'ORIGINAL' => FormView
     *          ],
     *          'GROSS_MODE' => [
     *              'DEFAULT' => FormView,
     *              'ORIGINAL' => FormView
     *          ]
     *       ],
     *       'USD' => ...
     *    ],
     *    'store2' => ...
     * ]
     *
     * @param \Symfony\Component\Form\FormView $formViewCollection
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     *
     * @return void
     */
    public function finishView(FormView $formViewCollection, FormInterface $form, array $options)
    {
        $priceTypes = [
            $this->getGrossPriceModeIdentifier() => [],
            $this->getNetPriceModeIdentifier() => [],
        ];

        $priceTable = [];
        foreach ($formViewCollection as $productMoneyTypeFormView) {
            $moneyValueFormView = $productMoneyTypeFormView['moneyValue'];
            $priceTypes = $this->buildPriceTypeList($productMoneyTypeFormView, $priceTypes);
            $priceTable = $this->buildPriceFormViewTable($productMoneyTypeFormView, $moneyValueFormView, $priceTable);
        }

        $formViewCollection->vars['priceTable'] = $priceTable;
        $formViewCollection->vars['priceTypes'] = $priceTypes;
    }

    /**
     * @param \Symfony\Component\Form\FormView $productMoneyTypeFormView
     * @param array $priceTypes
     *
     * @return array
     */
    protected function buildPriceTypeList(FormView $productMoneyTypeFormView, array $priceTypes)
    {
        $grossPriceModeIdentifier = $this->getGrossPriceModeIdentifier();
        $netPriceModeIdentifier = $this->getNetPriceModeIdentifier();

        $priceTypeTransfer = $this->extractPriceTypeTransfer($productMoneyTypeFormView);

        $priceType = $priceTypeTransfer->getName();
        $priceModeConfiguration = $priceTypeTransfer->getPriceModeConfiguration();

        if ($priceModeConfiguration === ProductManagementConstants::PRICE_MODE_BOTH) {
            $priceTypes[$netPriceModeIdentifier][$priceType] = $priceTypeTransfer;
            $priceTypes[$grossPriceModeIdentifier][$priceType] = $priceTypeTransfer;
        }

        if (!isset($priceTypes[$priceModeConfiguration][$priceType])) {
            $priceTypes[$priceModeConfiguration][$priceType] = $priceTypeTransfer;
        }

        return $priceTypes;
    }

    /**
     * @param \Symfony\Component\Form\FormView $productMoneyTypeFormView
     * @param \Symfony\Component\Form\FormView $moneyValueFormView
     * @param array $priceTable
     *
     * @return array
     */
    protected function buildPriceFormViewTable(
        FormView $productMoneyTypeFormView,
        FormView $moneyValueFormView,
        array $priceTable
    ) {
        $priceTypeTransfer = $this->extractPriceTypeTransfer($productMoneyTypeFormView);

        $grossPriceModeIdentifier = $this->getGrossPriceModeIdentifier();
        $netPriceModeIdentifier = $this->getNetPriceModeIdentifier();

        $priceType = $priceTypeTransfer->getName();
        $priceModeConfiguration = $priceTypeTransfer->getPriceModeConfiguration();

        $storeName = $moneyValueFormView->vars['store_name'];
        $currencySymbol = $moneyValueFormView->vars['currency_symbol'];

        if ($priceModeConfiguration === ProductManagementConstants::PRICE_MODE_BOTH) {
            $priceTable[$storeName][$currencySymbol][$netPriceModeIdentifier][$priceType] = $productMoneyTypeFormView;
            $priceTable[$storeName][$currencySymbol][$grossPriceModeIdentifier][$priceType] = $productMoneyTypeFormView;
        } else {
            $priceTable[$storeName][$currencySymbol][$priceModeConfiguration][$priceType] = $productMoneyTypeFormView;
        }

        return $priceTable;
    }

    /**
     * @return string
     */
    protected function getNetPriceModeIdentifier()
    {
        if (!static::$netPriceModeIdentifier) {
            static::$netPriceModeIdentifier = $this->getFactory()->getPriceFacade()->getNetPriceModeIdentifier();
        }

        return static::$netPriceModeIdentifier;
    }

    /**
     * @return string
     */
    protected function getGrossPriceModeIdentifier()
    {
        if (!static::$grossPriceModeIdentifier) {
            static::$grossPriceModeIdentifier = $this->getFactory()->getPriceFacade()->getGrossPriceModeIdentifier();
        }

        return static::$grossPriceModeIdentifier;
    }

    /**
     * @param int $grossPriceModeIdentifier
     * @param int $netPriceModeIdentifier
     *
     * @return array
     */
    protected function createBasePriceType($grossPriceModeIdentifier, $netPriceModeIdentifier)
    {
        return [
            $grossPriceModeIdentifier => [],
            $netPriceModeIdentifier => [],
        ];
    }

    /**
     * @param \Symfony\Component\Form\FormView $productMoneyTypeFormView
     *
     * @return \Generated\Shared\Transfer\PriceTypeTransfer
     */
    protected function extractPriceTypeTransfer(FormView $productMoneyTypeFormView)
    {
        return $productMoneyTypeFormView->vars['price_type'];
    }
}
