<?php

namespace MLMSoft\integrations\woocommerce\models\order;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\core\models\user\MLMSoftReferral;
use MLMSoft\core\models\user\MLMSoftRemoteUser;
use MLMSoft\integrations\woocommerce\models\product\MLMSoftWCProduct;
use MLMSoft\integrations\woocommerce\WCIntegration;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;
use WC_Order;
use WC_Product;
use WP_User;

if (!class_exists(WC_Order::class)) {
    return;
}

class MLMSoftWCOrder extends WC_Order
{

    public const REFERRAL_ACCOUNT_ID_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'referral_account_id';
    public const VOLUMES_SENT_FLAG_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'volumes_sent';
    public const PAID_IN_BONUSES_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'paid_in_bonuses';
    public const BONUS_WALLET_ID = MLMSoftPlugin::PLUGIN_PREFIX . 'bonus_wallet_id';

    public const PRODUCT_PROPERTIES_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'product_properties_filter';
    public const ADD_PRODUCT_VOLUMES_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'add_product_volumes_filter';
    public const INVOICE_DATA_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'invoice_data';
    public const VOLUMES_DOCUMENT_PAYLOAD_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'volumes_payload';

    public const VOLUMES_SENT_ACTION = MLMSoftPlugin::PLUGIN_PREFIX . 'volumes_sent';

    /**
     * @param MLMSoftRemoteUser $referral
     */
    public function setReferral($referral)
    {
        update_post_meta($this->id, self::REFERRAL_ACCOUNT_ID_META_KEY, $referral->getAccountField('id'));
    }

    /**
     * @return mixed
     */
    public function getReferralId()
    {
        return get_post_meta($this->get_id(), self::REFERRAL_ACCOUNT_ID_META_KEY, true);
    }

    /**
     * @return false|int|string
     */
    public function getOwnedAccountId()
    {
        $localUser = $this->getLocalUser();
        if ($localUser) {
            return $localUser->getAccountId();
        }
        return false;
    }

    /**
     * @return false|MLMSoftReferral
     */
    public function getReferral()
    {
        $id = $this->getReferralId();
        if ($id) {
            return MLMSoftReferral::loadByAccountId($id);
        }
        return false;
    }

    /**
     * @param string $documentName
     * @throws \HttpException
     */
    public function sendInvoice($documentName)
    {
        $payload = $this->createInvoiceData();
        $payload = apply_filters_ref_array(self::INVOICE_DATA_FILTER, [$payload, $this]);
        if (!$payload) {
            return;
        }
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        $mlmsoftPlugin->api3->createDocument($documentName, $payload);
    }

    /**
     * @param string $documentName
     * @throws \HttpException
     */
    public function sendVolumes($documentName)
    {
        $accountId = $this->getOwnedAccountId();
        if (!$accountId) {
            $accountId = $this->getReferralId();
        }

        if ($this->isVolumesSent()) {
            return;
        }

        if (!$accountId) {
            $this->add_order_note('MLMSoft accountId is not found in WP user');
            return;
        }

        $properties = $this->getVolumes();

        $mlmsoftPlugin = MLMSoftPlugin::getInstance();

		/**
		 * Added $domainSignature property to $documentPayload.
		 * @since 3.5.5
		 */
		$domainSignature = $this->getDomainSignature($mlmsoftPlugin->options);
		
        $documentPayload = [
            'accountId' => $accountId,
            'props' => $properties,
            'orderId' => $this->get_id(),
			'domainSignature' => $domainSignature,
        ];

        $documentPayload = apply_filters_ref_array(self::VOLUMES_DOCUMENT_PAYLOAD_FILTER, [$documentPayload, $this]);

        $mlmsoftPlugin->api3->createDocument($documentName, $documentPayload);
		
        /**
         * @since 3.5.4
         */
        update_post_meta($this->get_id(), self::VOLUMES_SENT_FLAG_META_KEY, true);

        do_action(self::VOLUMES_SENT_ACTION, $this, $properties);
    }

    /**
     * @return false|MLMSoftLocalUser
     */
    public function getLocalUser()
    {
        $userId = $this->get_user_id();
        if ($userId) {
            return new MLMSoftLocalUser($userId);
        }
        return false;
    }

    /**
     * @return false|MLMSoftRemoteUser
     */
    public function getRemoteUser()
    {
        $userId = $this->get_user_id();
        if ($userId) {
            return MLMSoftRemoteUser::loadByWpUser(new WP_User($userId));
        }
        return false;
    }

    /**
     * @return array|mixed
     */
    public function getVolumes()
    {
        $properties = [];

        $orderItems = $this->get_items();

        $productVolumeFields = WCIntegrationOptions::getInstance()->volumeProductFields;

        /**
         * @var integer $item_id
         * @var \WC_Order_Item_Product $item
         */
        foreach ($orderItems as $item_id => $item) {
            /** @var WC_Product $product */
            $product = $item->get_product();

            $addProductVolumes = apply_filters(self::ADD_PRODUCT_VOLUMES_FILTER, $item);
            if (!$addProductVolumes) {
                continue;
            }

            foreach ($productVolumeFields as $field) {
                $alias = WCIntegration::PRODUCT_VOLUME_PROPERTY_META_PREFIX . $field['volumeProperty'];
                $volumeProperty = $field['volumeProperty'];
                $value = $product->get_meta($alias);
                switch ($field['type']) {
                    case WCIntegration::PRODUCT_VOLUME_TYPE_STATUS:
                        if (empty($value)) {
                            break;
                        }
                        if (isset($properties[$volumeProperty])) {
                            if ($properties[$volumeProperty] < $value) {
                                $properties[$volumeProperty] = $value;
                            }
                        } else {
                            $properties[$volumeProperty] = $value;
                        }
                        break;
                    case WCIntegration::PRODUCT_VOLUME_TYPE_VOLUME:
                        if (empty($value)) {
                            $value = 0;
                        }
                        if (isset($properties[$volumeProperty])) {
                            $properties[$volumeProperty] += $value * $item->get_quantity();
                        } else {
                            $properties[$volumeProperty] = $value * $item->get_quantity();
                        }
                        break;
                }
            }

            $properties = apply_filters_ref_array(self::PRODUCT_PROPERTIES_FILTER, [$properties, $product]);
        }

        return $properties;
    }

    /**
     * @return bool
     */
    public function isVolumesSent()
    {
        return get_post_meta($this->get_id(), self::VOLUMES_SENT_FLAG_META_KEY, true) === '1';
    }

    /**
     * @return array
     */
    public function getInvoiceData()
    {
        $orderItems = $this->get_items();
        $invoiceItems = [];
        foreach ($orderItems as $orderItem) {
            $orderItemData = $orderItem->get_data();
            /** @var MLMSoftWCProduct $product */
            $product = new MLMSoftWCProduct(wc_get_product($orderItemData['product_id']));
            $quantity = $orderItem->get_quantity();
            $price = $product->get_price(null);
            $currency = $this->get_currency(null);
            $invoiceItem = [
                'g:id' => $product->get_sku(),
                'title' => $orderItem->get_name(null),
                'd:price' => $price,
                'g:price' => $price . ' ' . $currency,
                'currency' => $currency,
                'count' => $quantity,
                'discount' => 0
            ];
            $volumes = $product->getVolumes();
            foreach ($volumes as $property => $value) {
                $invoiceItem['g:' . strtolower($property)] = $value;
            }
            $invoiceItems[] = $invoiceItem;
        }

		/**
		 * Added the walletId in the $couponItems.
		 * @since 3.6.3
		 */
        $coupons = $this->get_coupons();
        $couponItems = [];
        /** @var \WC_Order_Item_Coupon $coupon */
        foreach ($coupons as $coupon) {
            $wcCoupon = new \WC_Coupon($coupon->get_code());
            $couponItems[] = [
                'code' => $wcCoupon->get_code(),
                'discount' => $wcCoupon->get_amount(null),
                'type' => $wcCoupon->get_discount_type(),
				'walletId' => $wcCoupon->get_meta('wallet_id')
            ];
        }

        return [
            'id' => $this->get_id(),
            'status' => WCIntegration::WC_STATUS_TO_MLMSOFT_INVOICE_STATUS[$this->get_status(null)],
            'amount' => $this->get_total(null),
            'subtotalAmount' => $this->get_subtotal(),
            'a' => $this->get_discount_total(null),
            'comment' => '',
            'data' => [
                'sum' => $this->get_total(null) . ' ' . $this->get_currency(null),
                'items' => $invoiceItems
            ],
            'coupons' => $couponItems
        ];
    }


    private function createInvoiceData()
    {
        $localUser = $this->getLocalUser();
        if ($localUser) {
            $accountId = $localUser->getAccountId();
        } else {
            $accountId = $this->getReferralId();
        }

        if (!$accountId) {
            return null;
        }

        return [
            'accountId' => $accountId,
            'props' => [],
            'invoice' => $this->getInvoiceData()
        ];
    }
			
	/**
	 * @since 3.5.5
	 */
    private function getDomainSignature($options)
    {
		if ( defined('MLMSOFT_OPTION_DOMAIN_SIGNATURE') ) {
			return MLMSOFT_OPTION_DOMAIN_SIGNATURE;
		}
		
		$domainSignature = $options->domainSignature;

		if ( empty($domainSignature) ) {
			$home_url = untrailingslashit(get_home_url());
			$ds = str_replace(['http://','https://'], '', $home_url);
			$ds = explode('.', $ds);
			$ds = array_slice($ds, -2, 2);
			$domainSignature = implode('.', $ds);
			$options->__set('domainSignature', $domainSignature);
		}
		
		return $domainSignature;
    }	
}