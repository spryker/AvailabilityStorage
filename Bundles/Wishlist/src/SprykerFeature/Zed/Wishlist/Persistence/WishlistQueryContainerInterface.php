<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */
namespace SprykerFeature\Zed\Wishlist\Persistence;

use SprykerFeature\Zed\Wishlist\Persistence\Propel\SpyWishlistItemQuery;
use SprykerFeature\Zed\Wishlist\Persistence\Propel\SpyWishlistQuery;

interface WishlistQueryContainerInterface
{
    /**
     * @param integer $idWishlist
     * @param integer $idProduct
     *
     * @return SpyWishlistItemQuery
     */
    public function queryCustomerWishlistByProductId($idWishlist, $idProduct);

    /**
     * @param integer $idWishlist
     * @param string  $groupKey
     *
     * @return SpyWishlistItemQuery
     */
    public function queryCustomerWishlistByGroupKey($idWishlist, $groupKey);

    /**
     * @return SpyWishlistItemQuery
     */
    public function queryWishlistItem();

    /**
     * @return SpyWishlistQuery
     */
    public function queryWishlist();

}