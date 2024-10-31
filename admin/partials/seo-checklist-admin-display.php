<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://flexithemes.com
 * @since      1.0.0
 *
 * @package    Seo_Checklist
 * @subpackage Seo_Checklist/admin/partials
 */

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

// display the plugin admin page
function SEOCHECKLIST_seo_checklist_display_admin_page() {
	
    // check if user is allowed access
    if ( ! current_user_can( 'manage_options' ) ) return;

    ?>

    <div class="seo-checklist">
        <div class="seo-checklist-title">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <h3>The Ultimate SEO Checklist for WordPress! Use it to find SEO issues, improve your site, and gain more traffic.</h3>
        </div>
        <button class="button seo-checklist-button">Run Checks</button>
        <div class="seo-checklist-loading">
            <div class="seo-checklist-loading-bar"></div>
        </div>
        <div id="general" class="seo-checklist-panel">
            <div class="seo-checklist-count-container">
                <div class="seo-checklist-count seo-checklist-count--passed"><span class="number"></span>Passed</div>
                <div class="seo-checklist-count seo-checklist-count--warning"><span class="number"></span>Warning</div>
                <div class="seo-checklist-count seo-checklist-count--failed"><span class="number"></span>Failed</div>
                <div class="seo-checklist-count seo-checklist-count--info"><span class="number"></span>Info</div>
            </div>
            <div class="seo-checklist-container">
                <ul class="seo-checklist-list"></ul>
            </div>
        </div>
        
    </div>

    <?php
	
}
