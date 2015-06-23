<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Checkout\Business\Model\Workflow\Task\Propel;

use Generated\Shared\Transfer\OrderTransfer;
use SprykerFeature\Zed\Checkout\Business\Model\Workflow\Context;
use SprykerFeature\Zed\Checkout\Business\Model\Workflow\Task\AbstractTask;

class BeginTransaction extends AbstractTask
{
    /**
     * @param OrderTransfer $transferOrder
     * @param Context $context
     * @param array $logContext
     */
    public function __invoke(OrderTransfer $transferOrder, Context $context, array $logContext)
    {
        $connection = \Propel\Runtime\Propel::getConnection();
        $connection->beginTransaction();
    }
}
