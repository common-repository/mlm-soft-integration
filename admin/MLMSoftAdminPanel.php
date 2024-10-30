<?php

namespace MLMSoft\admin;

use MLMSoft\core\MLMSoftPlugin;

/**
 * @since 3.6.6
 */
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;

class MLMSoftAdminPanel
{
    /**
     * Nonce.
     * 
     * @since 3.6.6
     *
     * @var string
     */
    const NONCE_ACTION = 'mlmsoft-nonce';
    
    /**
     * @since 3.6.6
     *
     * @var string
     */
    const CHECKOUT_CSS_PAGE_SUFFIX = '_checkout_css';
    
    /**
     * @since 3.6.6
     *
     * @var string
     */
    const USE_CHECKOUT_CSS_ID = 'use-checkout-css';

    /**
     * @since 3.6.6
     *
     * @var string
     */
    const ACE_EDITOR_CONTENT_ID = 'ace-editor-css-content';
    
    /**
     * @var MLMSoftAdminFrontend
     */
    private $frontend;

    public function __construct()
    {
        $this->frontend = MLMSoftAdminFrontend::getInstance();
        $this->registerHooks();
        $this->init();
    }

    private function init()
    {
        MLMSoftAdminApi::getInstance();
    }

    public function registerHooks()
    {
        /**
         * @since 3.6.6
         */
        add_action('mlmsoft_admin_panel_register_pages', [$this, 'registerCheckoutCssPage'], 5);
        /**
         * @since 3.6.6
         */
        add_action('admin_print_scripts', [$this, 'printScripts']); 
        /**
         * @since 3.6.6
         */        
        add_action('admin_init', [$this, 'checkFormSubmit']);
        
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('admin_menu', [$this, 'registerPages']);
    }

    /**
     * @since 3.6.6
     */
    public function printScripts()
    {
        global $pagenow;
        
        if ( 'admin.php' !== $pagenow ) {
            return;
        }
        
        $slug = MLMSoftPlugin::PLUGIN_PREFIX.'options_page'.self::CHECKOUT_CSS_PAGE_SUFFIX;
        
        if ( ! isset($_GET['page']) || $_GET['page'] !== $slug ) {
            return;
        }
        
        $plugin_url = plugin_dir_url(MLMSOFT_V3_PLUGIN_FILE);
        
        // @see https://ace.c9.io/#nav=embedding
        wp_register_script(
            'ace-editor',
            $plugin_url.'admin/assets/js/ace/ace.js',
            array('jquery'),
            '1.35.2',
            true
        );
        wp_enqueue_script('ace-editor');

        wp_register_script(
            'mlmsoft-ace-editor',
            $plugin_url.'admin/assets/js/ace/mlmsoft-ace-editor-admin.js',
            array('ace-editor'),
            '1.0.0',
            true
        );
        wp_enqueue_script('mlmsoft-ace-editor');
        wp_localize_script(
            'mlmsoft-ace-editor', 
            'MLMSoftAceEditorAdmin', 
            array(
                'data' => [
                    'checkoutCssFormSelector' => '#mlmsoft-checkout-css-form',
                    'aceEditorCssID'          => 'ace-editor-css',
                    'aceEditorContentID'      => self::ACE_EDITOR_CONTENT_ID,
                ]
            )
        );
    }
    
    /**
     * @since 3.6.6
     */
    public function registerCheckoutCssPage($args)
    {
        add_submenu_page( 
            $args['menu_slug'], 
            'Checkout CSS', 
            'Checkout CSS', 
            $args['capability'], 
            $args['menu_slug'].self::CHECKOUT_CSS_PAGE_SUFFIX,
            [$this, 'checkoutCssPageContent'],
        );
    }
    
    /**
     * @since 3.6.6
     */
    public function checkFormSubmit()
    {
        // Handle the options form submit.
        // If data posted, the options will be updated, and page reloaded (so no continue to the next line).
        $this->handleSubmit();        
    }

    /**
     * Handle the `Save Changes` form submit.
     *
     * @since 3.6.6
     */
    protected function handleSubmit() 
    {
        // Check if there were any posted data before nonce verification.
        $css_content_id = self::ACE_EDITOR_CONTENT_ID;
        if ( ! isset( $_POST[$css_content_id] ) ) {
            // No data.
            return;
        }

        // WP anti-hacks.
        if ( ! current_user_can('manage_options') ) {
            wp_die( 'Unauthorized user' );
        }

        check_admin_referer(self::NONCE_ACTION);

        $cssContent = $_POST[$css_content_id];
        $cssContent = trim($cssContent);

        WCIntegrationOptions::getInstance()->__set('checkoutCss', $cssContent);
        
        $useCheckoutCss = false;
        if ( isset($_POST[self::USE_CHECKOUT_CSS_ID]) && 'on' === $_POST[self::USE_CHECKOUT_CSS_ID] ) {
            $useCheckoutCss = true;
        }
        WCIntegrationOptions::getInstance()->__set('useCheckoutCss', $useCheckoutCss);
	}    
    
    /**
     * @since 3.6.6
     */
    public function checkoutCssPageContent()
    {
        $checkoutCss = WCIntegrationOptions::getInstance()->checkoutCss;
        $useCheckoutCss = WCIntegrationOptions::getInstance()->useCheckoutCss;
        
        $checked = $useCheckoutCss ? 'checked="checked"' : '';
        
        $content  = '<div class="wrap">';
        $content .=     '<h1>MLMSoft Integration: CSS for checkout page.</h1>';
        $content .=     '<div id="mlmsoft-checkout-css" style="margin-top: 1rem;" class="mlmsoft-checkout-css">';
        $content .=         '<form id="mlmsoft-checkout-css-form" method="post">';
        $content .=             '<div style="font-size: 18px;">';
        $content .=                 '<input type="checkbox" name="'.self::USE_CHECKOUT_CSS_ID.'" id="'.self::USE_CHECKOUT_CSS_ID.'" '.$checked.' />';
        $content .=                 'Enable the use of custom CSS on the checkout page.';
        $content .=             '</div>';
        $content .=             '<div id="ace-editor-css" name="ace-editor-css" style="top:1rem;width:50%;height:600px;">';
        $content .=                 $checkoutCss;
        $content .=             '</div>';
        $content .=             '<div class="">';
        $content .=                 '<input type="hidden" id="'.self::ACE_EDITOR_CONTENT_ID.'" name="'.self::ACE_EDITOR_CONTENT_ID.'" value="" />';
        $content .=             '</div>';
        $content .=             '<div class="nonce-field">';
        $content .=                 wp_nonce_field(self::NONCE_ACTION, '_wpnonce', true, false);
        $content .=             '</div>';
        $content .=             '<div class="submit-button" style="">';
        $content .=                 get_submit_button(__('Save'));
        $content .=             '</div>';
        $content .=         '</form>';
        $content .=     '</div><!-- .mlmsoft-checkout-css -->';
        $content .= '</div><!-- .wrap -->';
        
        echo $content;
    }
    
    public function registerPages()
    {
        /**
         * Revising of the function with the addition of an action.
         *
         * @since 3.4.13
         */
		 
        /**
        add_menu_page('MLM Soft', 'MLM Soft', 'manage_options', MLMSoftPlugin::PLUGIN_PREFIX . 'options_page', [$this, 'showAdminPage'], '', 5);
        // */

        $menu_slug = MLMSoftPlugin::PLUGIN_PREFIX . 'options_page';
        $capability = 'manage_options';

        $page_hook = add_menu_page(
            'MLM Soft', 
            'MLM Soft', 
            $capability, 
            $menu_slug,
            [$this, 'showAdminPage'], 
            '',
            5
        );

        $args = array(
            'menu_slug'  => $menu_slug,
            'page_hook'  => $page_hook,
            'capability' => $capability,
        );

        /**
         * Fires after a top-level menu page is added.
         *
         * @since 3.4.13
         *
         * @param array $args Array of arguments.
         */		
        do_action_ref_array('mlmsoft_admin_panel_register_pages', array( &$args ));
    }

    public function registerSettings()
    {
        add_settings_section(MLMSoftPlugin::PLUGIN_PREFIX . 'admin_panel', '', '', MLMSoftPlugin::PLUGIN_PREFIX . 'options_page');
    }

    public function showAdminPage()
    {
        $this->frontend->addScriptParams('adminParams', [
            'locale' => get_locale()
        ]);
        $this->frontend->enqueue();
        echo '<div id="app"></div>';
    }
}