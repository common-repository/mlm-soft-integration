<?php


namespace MLMSoft\integrations\woocommerce\modules;


use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\core\models\user\MLMSoftReferral;
use MLMSoft\integrations\woocommerce\models\order\MLMSoftV2WCOrder;
use MLMSoft\integrations\woocommerce\models\order\MLMSoftWCOrder;
use MLMSoft\integrations\woocommerce\models\user\MLMSoftRemoteWCUser;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;

class WCOrderModule
{
    public const UPDATE_PROFILE_FROM_ORDER_DATA_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'update_profile_from_order_data';

    public const ORDER_SENT_FLAG = MLMSoftPlugin::PLUGIN_PREFIX . 'order_sent';

    /**
     * @var MLMSoftPlugin
     */
    private $mlmsoftPlugin;

    /**
     * @var WCIntegrationOptions
     */
    private $options;

    /**
     * WCAccountModule constructor.
     * @param MLMSoftPlugin $mlmsoftPlugin
     * @param WCIntegrationOptions $options
     */
    public function __construct($mlmsoftPlugin, $options)
    {
        $this->mlmsoftPlugin = $mlmsoftPlugin;
        $this->options = $options;

        add_action('woocommerce_checkout_update_order_meta', array($this, 'checkoutUpdateOrderMeta'));
        add_action('woocommerce_checkout_order_created', array($this, 'checkoutOrderCreated'), 10, 1);
        add_action('woocommerce_order_status_changed', array($this, 'orderStatusChanged'), 10, 4);
    }

    public function checkoutUpdateOrderMeta($orderId)
    {
        $order = new MLMSoftWCOrder($orderId);
        if (!$order->get_user_id()) {
            $referral = MLMSoftReferral::loadFromSession();
            if ($referral) {
                $order->setReferral($referral);
            }
        }
    }

    /**
     * @param \WC_Order $order
     * @throws \Exception
     */
    public function checkoutOrderCreated($order)
    {
        $orderSent = $order->get_meta(self::ORDER_SENT_FLAG, true);
        if ($orderSent) {
            return;
        }

        $syncUsers = $this->mlmsoftPlugin->options->syncUsers;
        $updateProfile = apply_filters(self::UPDATE_PROFILE_FROM_ORDER_DATA_FILTER, false);
        if ($syncUsers && $updateProfile) {
            $orderData = $order->get_data();
            $billingData = $orderData['billing'];
            $billingData['account_first_name'] = $billingData['first_name'];
            $billingData['account_last_name'] = $billingData['last_name'];
            $billingData['account_email'] = $billingData['email'];

            $localUser = new MLMSoftLocalUser($order->get_customer_id());
            $user = MLMSoftRemoteWCUser::loadByWpUser($localUser);
            if ($user) {
                $user->updateWCProfile($localUser, $billingData);
            }
        }

        if ($this->options->useDocuments) {
            $wcOrder = new MLMSoftWCOrder($order->get_id());
            $accountId = $wcOrder->getOwnedAccountId();
            if (!$accountId) {
                $accountId = $wcOrder->getReferralId();
                if (!$accountId) {
                    return;
                }
            }
            if ($this->options->createOrderDocument) {
                $wcOrder->sendInvoice($this->options->createOrderDocument);
                $order->add_meta_data(self::ORDER_SENT_FLAG, true);
            }
        }
    }

    public function orderStatusChanged($orderId, $statusFrom, $statusTo, $order)
    {
        $orderStatus = $this->options->sendVolumesOrderStatus;

        if ($this->options->useDocuments) {
            $wcOrder = new MLMSoftWCOrder($orderId);
        } else {
            $wcOrder = new MLMSoftV2WCOrder($orderId);
        }

        if ('wc-' . $statusTo == $orderStatus) {
            $volumeChangeDocument = $this->options->volumeChangeDocument;
            $wcOrder->sendVolumes($volumeChangeDocument);
            $remoteUser = $wcOrder->getRemoteUser();
            $localUser = $wcOrder->getLocalUser();
            if (!$remoteUser || !$localUser) {
                return;
            }
            $localUser->updateUserRole($remoteUser);
        }
        if ($this->options->useDocuments) {
            $orderUpdatedDocument = $this->options->updateOrderDocument;
            if ($orderUpdatedDocument) {
                $wcOrder->sendInvoice($orderUpdatedDocument);
            }
        }
    }
}
