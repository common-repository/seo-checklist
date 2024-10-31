<?php


    if( !class_exists( 'WP_Http' ) )
        include_once( ABSPATH . WPINC. '/class-http.php' );



class SEOCHECKLIST_SEOChecks

{
    private $check_list = [
        'SEOCHECKLIST_check_domain_length',
        'SEOCHECKLIST_server_response_time',
        'SEOCHECKLIST_check_ssl',
        'SEOCHECKLIST_check_site_title_and_tagline',
        'SEOCHECKLIST_language_and_time_info',
        'SEOCHECKLIST_check_search_engine_visibility_settings',
        'SEOCHECKLIST_check_restrictions_in_robotstxt',
        'SEOCHECKLIST_check_permalink_structure',
        'SEOCHECKLIST_check_if_seo_plugin_is_installed',
        'SEOCHECKLIST_check_if_caching_plugin_is_installed',
        'SEOCHECKLIST_check_if_firewall_plugin_is_installed',
        'SEOCHECKLIST_check_if_affiliate_plugin_is_installed',
        'SEOCHECKLIST_check_if_sitemap_exists',
        'SEOCHECKLIST_check_for_google_analytics_script',
        'SEOCHECKLIST_check_number_of_active_plugins',
        'SEOCHECKLIST_check_h1_tags',
        'SEOCHECKLIST_keyword_test',
    ];


     public $total_time;
    /**
     * Check constructor.
     * Creates ajax actions for all the checks
     */
    protected   $request;
    public function __construct()
    {
        add_action('wp_ajax_' . 'SEOCHECKLIST_return_seo_checks', [$this, 'SEOCHECKLIST_return_seo_checks']);

        $checks = $this->SEOCHECKLIST_seo_checks();
       
        foreach ($checks as $check) {
           
            add_action('wp_ajax_' . $check, [$this, $check]);
        }
        $this->request = new WP_Http;
        add_action( 'requests-curl.after_request', array( $this, 'SEOCHECKLIST_store_total_time' ), 10, 2 );

    }

    /**
     * How To Add New Checks
     * 1. Always expect the check failed. Let your checks proof the user did okay. If no $status is send to
     * return_json_object() the system will assign the value 'failed'.
     * 2. Fill in all the messages
     * 3. Add the function name to $this->checks. This will be used to create the necessary action and
     * ajax call in Javascript
     * 4. Great, you're done!
     *
     * Example function:
     */
    public static function SEOCHECKLIST_is_this_true()
    {
        if (1 == 1) // == true
            $status = 'p'; // = passed

        $title = "This is a Title";
        $message = "This is a Message";
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }

    /**
     * Checks if domain is less than 15 characters
     */
    public static function SEOCHECKLIST_check_domain_length() {
        $url = str_replace("www.","", $_SERVER['SERVER_NAME']); 
        $url = explode('.', $url);
        $domain = $url[0];
        $domainLength = strlen($domain);
        
        if ($domainLength < 15) {
            $status = 'p';
            $title = "Your domain is not too long";
            $message = "Your domain " . $domain . " is " . $domainLength . " characters. Domains that are under 15 characters are best for SEO.";
        } else {
            $title = "Your domain is too long.";
            $message = "Your domain " . $domain . " is " . $domainLength . " characters. Domains that are under 15 characters are best for SEO.";
            $message .= "</br><b>Fix: </b>Look for short domains. Stay under 15 characters.";
        }
        
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
        
    }
    
    /**
     * Checks server response time.
     */
    public static function SEOCHECKLIST_server_response_time() {

        $result = wp_remote_get( site_url() );
        $response_time = $this->total_time;
       
        if ( $response_time < 2) {
            $status = 'p';
            $title = "Your server didn't take too long to respond.";
            $message = 'Your server took ' . $response_time . 's total to respond. Ideal response time is less than 2s.';
        } else {
            $title = "Your server took too long to respond.";
            $message = 'Your server took ' . $response_time . 's total to respond. Ideal response time is less than 2s.';
            $message .= "</br><b>Fix: </b> Consider getting a faster host. We recommend <a href='https://flexithemes.com/recommends/siteground/'>SiteGround</a>, <a href='https://flexithemes.com/recommends/fastcomet/'>FastComet</a>, <a href='https://flexithemes.com/recommends/liquidweb/'>LiquidWeb</a> or <a href='https://flexithemes.com/recommends/wpengine/'>WPEngine</a>";
        }
        
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks if website uses SSL / HTTPS.
     */
    public static function SEOCHECKLIST_check_ssl() {
                
        if (is_ssl()) {
            $status = 'p';
            $title = "Your site uses SSL/HTTPS";
            $message = "You have SSL certificate installed in your WordPress website.";
        } else {
            $title = "Your site does not use SSL/HTTPS";
            $message = '<b>Fix: </b>Contact your hosting company to get an SSL certificate installed for your WordPress website.';
        }
        
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks if Site Title and Tagline is set.
     */
    public static function SEOCHECKLIST_check_site_title_and_tagline() {
        $siteTitle = get_bloginfo('name');
        $siteTagline = get_bloginfo('description');
        
        if ($siteTitle && $siteTagline) {
            $status = 'p';
            $title = "Your Site Title and Tagline is set";
            $message = "Your Site Title is: " . $siteTitle;
            $message .= "</br>Your Site Tagline is: " . $siteTagline;
        } elseif(!get_bloginfo('name') && get_bloginfo('description')) {
            $title = "Your Site Title is not set.";
            $message = "Your Site Tagline is: " . $siteTagline;
            $message .= "</br>Your Site Title is not set.";
            $message .= '</br><b>Fix: </b>Go to: Settings -> General. Set your Site Title';        
        } elseif(get_bloginfo('name') && !get_bloginfo('description')) {
            $title = "Your Site Tagline is not set.";
            $message = "Your Site Title is: " . $siteTitle;
            $message .= "</br>Your Site Tagline is not set.";
            $message .= '</br><b>Fix: </b>Go to: Settings -> General. Set your Site Tagline';        
        } else {
            $title = "Your Site Title and Tagline not set.";
            $message .= '<b>Fix: </b>Go to: Settings -> General. Set your Site Title and Tagline';        
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Language and Time Info.
     */
    public static function SEOCHECKLIST_language_and_time_info() {
        $status = 'i';
        $language = get_bloginfo('language');
        if(get_option('timezone_string')){
            $timezone = get_option('timezone_string');
        } else {
            $timezone = 'UTC' . (get_option('gmt_offset') < 0 ? '-' : '+') . get_option('gmt_offset');
        }
        $dateFormat = get_option('date_format');
        $timeFormat = get_option('time_format');
        $title = "Check your Site Language, Timezone, Date, and Time formats.";
        $message = "Your Site Language is: " . $language;
        $message .= "</br>Your Site Timezone is: " . $timezone;
        $message .= "</br>Your Site Date Format is: " . $dateFormat;
        $message .= "</br>Your Site Time Format is: " . $timeFormat;
        $message .= "</br>Your Site Language, Timezone, Date, and Time formats are used for some metadata and by some plugins. If you schedule your WordPress posts to publish at a certain time, you don’t want to have the default time zone. It’s better to use your audience’s timezone. You can only have one, so pick the primary target audience’s timezone. You can change these settings at Settings > General";
        
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    
    /**
     * Checks wordpress search engine visibility settings
     */
    public static function SEOCHECKLIST_check_search_engine_visibility_settings() {
                
        if( 1 == get_option( 'blog_public' ) ) {
            $status = 'p';
            $title = "Your site allows search engine visibility";
            $message = "Your site has the 'Discourage search engines from indexing this site' setting disabled.";
        } else {
            $title = "Your site does not allow search engine visibility";
            $message = "Your site has 'Discourage search engines from indexing this site' settings enabled";
            $message .= '</br><b>Fix: </b>Go to Settings > Reading. Make sure "Discourage search engines from indexing this site" is not enabled';
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks for search engine restrictions in robots.txt
     */
    public static function SEOCHECKLIST_check_restrictions_in_robotstxt() {
        
        $robots = get_site_url() . '/robots.txt';
        $content = file_get_contents($robots);
        $result = $this->request->request( $robots );
        $statusCode = wp_remote_retrieve_response_code($result);
   
                
        if( $statusCode =='200' ) {
            $status = 'p';
            $title = "Your site has a robots.txt file and it does not restrict search engine bots from indexing your site.";
            $message = "Here's what your robots.txt file looks like: ";
            $message .= "</br><pre>" . $content . "</pre>";
            if( trim($content) == false) {
            
                $status = 'w';
                $title = "Your site's robots.txt file is empty.";
                $message = "<b>Fix: </b>Double check content of robots.txt file and make sure that whatever you want to be publicly accessible isn’t blocked. We've set up Google Search Console in the <a href='https://flexithemes.com/wordpress-google-search-console/'>advanced WordPress SEO diagnostics guide</a>, you can go to their <a href='https://www.google.com/webmasters/tools/robots-testing-tool' target='_blank' rel='noopener'>robots testing tool</a> to check for any issues.";
            } elseif( strpos($content, 'Disallow: /wp-content' )) {
            
                $status = 'w';
                $title = "Your site's robots.txt file may disallow important parts of your website.";
                $message = "Here's what your robots.txt file looks like: ";
                $message .= "</br><pre>" . $content . "</pre>";
                $message .= "</br><b>Fix: </b>Double check the content of robots.txt file and make sure that whatever you want to be publicly accessible isn’t blocked. We've set up Google Search Console in the <a href='https://flexithemes.com/wordpress-google-search-console/'>advanced WordPress SEO diagnostics guide</a>, you can go to their <a href='https://www.google.com/webmasters/tools/robots-testing-tool' target='_blank' rel='noopener'>robots testing tool</a> to check for any issues.";
            }
        } else {
            $title = "Your site does not have a robots.txt file";
            $message = "<b>Fix: </b>Make sure you have a robots.txt file in the root directory for search engine bots to index your website. Once you have a robots.txt file, make sure that whatever you want to be publicly accessible isn’t blocked. We've set up Google Search Console in the <a href='https://flexithemes.com/wordpress-google-search-console/'>advanced WordPress SEO diagnostics guide</a>, you can go to their <a href='https://www.google.com/webmasters/tools/robots-testing-tool' target='_blank' rel='noopener'>robots testing tool</a> to check for any issues.";
        }
        
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks wordpress permalink structure
     */
    public static function SEOCHECKLIST_check_permalink_structure() {
                
        if( '/%postname%/' == get_option( 'permalink_structure' ) ) {
            $status = 'p';
            $title = "You have an SEO friendly permalink structure.";
            $message = "Your site's permalink settings is set to Post Name";
        } else {
            $title = "You do not have an SEO friendly permalink structure.";
            $message .= "Your site's permalink settings is not set to Post Name.";
            $message = "</br><b>Fix: </b>Go to Settings > Permalinks. Make sure your permalink settings is set to Post Name, but be sure you <a href='https://premium.wpmudev.org/blog/change-wordpress-site-permalink-structure/' target='_blank' rel='noopener'>safely redirect</a> it and preserve your SEO.";
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks if SEO plugin is installed
     */
    public static function SEOCHECKLIST_check_if_seo_plugin_is_installed()
    {
        
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $seoPlugins = [
            'wordpress-seo/wp-seo.php' => 'Yoast SEO',
            'all-in-one-seo-pack/all_in_one_seo_pack.php' => 'All In One SEO Pack',
            'seo-by-rank-math/rank-math.php' => 'Rank Math',
            'autodescription/autodescription.php' => 'The SEO Framework',
            'squirrly-seo/squirrly.php' => 'Squirrly SEO',
            'wp-seopress/seopress.php' => 'SEOPress'
        ];
        $number = 0;
        $pluginNames[] = '';
        
        foreach ($seoPlugins as $key => $value) {
            if (is_plugin_active($key)){
                $pluginNames[] = $value;
                $number += 1;
            }
        }
        
        if ($number == 0) {
            $title = "You do not have an active SEO Plugin.";
            $message = "<b>Fix: </b>Install an SEO Plugin. We recommend <a href='https://wordpress.org/plugins/all-in-one-seo-pack/'>All in One SEO Pack</a>, <a href='https://wordpress.org/plugins/seo-by-rank-math/'>Rank Math</a>, or <a href='https://wordpress.org/plugins/wordpress-seo/'>Yoast SEO</a>";
            $message .= "<div class='note'>Did we miss a plugin? <a href='https://flexithemes.com/contact/'>Let us know</a>.</div>";
        } elseif ($number > 1) {
            $status = 'w';
            $lastItem = array_pop($pluginNames);
            $title = "Multiple SEO plugins detected.";
            $message = "You have " . $number . " active SEO plugins: " . implode(', ', $pluginNames) . ' and ' . $lastItem . ".";
            $message .= "</br><b>Fix: </b>Keep only one of these plugins.";
        } else {
            $status = 'p';
            $title = "You have an active SEO plugin";
            $message = "You are using: " . implode('', $pluginNames);
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    
    /**
     * Checks if Caching plugin is installed
     */
    public static function SEOCHECKLIST_check_if_caching_plugin_is_installed()
    {
        
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $cachingPlugins = [
            'wp-fastest-cache/wpFastestCache.php' => 'WP Fastest Cache',
            'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
            'wp-super-cache/wp-cache.php' => 'WP Super Cache',
            'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
            'comet-cache/comet-cache.php' => 'Comet Cache',
            'cache-enabler/cache-enabler.php' => 'Cache Enabler',
            'redis-cache/redis-cache.php' => 'Redis Object Cache',
            'hyper-cache/plugin.php' => 'Hyper Cache',
            'cache-control/cache-control.php' => 'Cache-Control',
            'simple-cache/simple-cache.php' => 'Simple Cache',
            'hummingbird-performance/wp-hummingbird.php' => 'Hummingbird Page Speed Optimization',
            'cachify/cachify.php' => 'Cachify',
            'wp-speed-of-light/wp-speed-of-light.php' => 'WP Speed of Light',
            'breeze/breeze.php' => 'Breeze — Free WordPress Cache Plugin',
        ];
        $number = 0;
        $pluginNames[] = '';
        
        foreach ($cachingPlugins as $key => $value) {
            if (is_plugin_active($key)){
                $pluginNames[] = $value;
                $number += 1;
            }
        }
        
        if ($number == 0) {
            $title = "You do not have an active Caching Plugin.";
            $message = "<b>Fix: </b>Install a Caching Plugin. We recommend <a href='https://wordpress.org/plugins/litespeed-cache/'>LiteSpeed Cache</a> or <a href='https://flexithemes.com/recommends/wp-rocket/'>WP Rocket</a>";
            $message .= "<div class='note'>Did we miss a plugin? <a href='https://flexithemes.com/contact/'>Let us know</a>.</div>";
        } elseif ($number > 1) {
            $status = 'w';
            $lastItem = array_pop($pluginNames);
            $title = "Multiple Caching plugins detected.";
            $message = "You have " . $number . " active Caching plugins: " . implode(', ', $pluginNames) . ' and ' . $lastItem . ".";
            $message .= "</br><b>Fix: </b>Keep only one of these plugins.";
        } else {
            $status = 'p';
            $title = "You have an active Caching plugin";
            $message = "You are using: " . implode('', $pluginNames);
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks if Firewall plugin is installed
     */
    public static function SEOCHECKLIST_check_if_firewall_plugin_is_installed()
    {
        
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $firewallPlugins = [
            'cloudflare/cloudflare.php' => 'Cloudflare',
            'wordfence/wordfence.php' => 'Wordfence Security',
            'all-in-one-wp-security-and-firewall/wp-security.php' => 'All In One WP Security & Firewall',
            'cleantalk-spam-protect/cleantalk.php' => 'Spam protection, AntiSpam, FireWall by CleanTalk',
            'ninjafirewall/ninjafirewall.php' => 'NinjaFirewall (WP Edition)',
            'block-bad-queries/block-bad-queries.php' => 'BBQ: Block Bad Queries',
            'bulletproof-security/bulletproof-security.php' => 'BulletProof Security',
            'gotmls/index.php' => 'Anti-Malware Security and Brute-Force Firewall',
            'malcare-security/malcare.php' => 'Security & Firewall – MalCare Security',
            'querywall/querywall.php' => "QueryWall: Plug'n Play Firewall",
            'security-antivirus-firewall/index.php' => 'Security, Antivirus, Firewall – S.A.F',
            'security-malware-firewall/security-malware-firewall.php' => 'Security & Malware scan by CleanTalk',
            'shieldfy/shieldfy.php' => 'Shieldfy Security Firewall and Anti Virus',
            'stop-user-enumeration/stop-user-enumeration.php' => 'Stop User Enumeration',
            'sucuri-scanner/sucuri.php' => 'Sucuri Security',
            'wp-antivirus-website-protection-and-firewall/wp-antivirus-website-protection-and-firewall.php' => 'WP Antivirus Website Protection and Website Firewall (by SiteGuarding.com)',
            'wp-cerber/wp-cerber.php' => 'Cerber Security, Antispam & Malware Scan',
            'wp-simple-firewall/icwp-wpsf.php' => 'Shield Security for WordPress',           
        ];
        $number = 0;
        $pluginNames[] = '';
        
        foreach ($firewallPlugins as $key => $value) {
            if (is_plugin_active($key)){
                $pluginNames[] = $value;
                $number += 1;
            }
        }
        
        if ($number == 0) {
            $title = "You do not have an active Firewall Plugin.";
            $message = "<b>Fix: </b>Install a Firewall Plugin. We recommend <a href='https://wordpress.org/plugins/block-bad-queries/'>BBQ: Block Bad Queries</a>";
            $message .= "<div class='note'>Did we miss a plugin? <a href='https://flexithemes.com/contact/'>Let us know</a>.</div>";
        } elseif ($number > 1) {
            $status = 'w';
            $lastItem = array_pop($pluginNames);
            $title = "Multiple Firewall plugins detected.";
            $message = "You have " . $number . " active Firewall plugins: " . implode(', ', $pluginNames) . ' and ' . $lastItem . ".";
            $message .= "</br><b>Fix: </b>Keep only one of these plugins.";
        } else {
            $status = 'p';
            $title = "You have an active Firewall plugin";
            $message = "You are using: " . implode('', $pluginNames);
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks if Affiliate plugin is installed
     */
    public static function SEOCHECKLIST_check_if_affiliate_plugin_is_installed()
    {
        
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $affiliatePlugins = [
            'affiliate-coupons/affiliate-coupons.php' => 'Affiliate Coupons',
            'affiliate-links/affiliate-links.php' => 'Affiliate Links Lite',
            'affiliate-power/affiliate-power.php' => 'Affiliate Power',
            'affiliates/affiliates.php' => 'Affiliates',
            'affiliates-manager/boot-strap.php' => 'WP Affiliate Manager',
            'affiliates-woocommerce-light/affiliates-woocommerce-light.php' => 'Affiliates WooCommerce Light',
            'affiliatewp-affiliate-product-rates/affiliatewp-affiliate-product-rates.php' => 'AffiliateWP - Affiliate Product Rates',
            'affiliatewp-allowed-products/affiliatewp-allowed-products.php' => 'AffiliateWP - Allowed Products',
            'affiliatewp-leaderboard/affiliatewp-leaderboard.php' => 'AffiliateWP - Leaderboard',
            'affiliatewp-show-affiliate-coupons/affiliatewp-show-affiliate-coupons.php' => 'AffiliateWP - Show Affiliate Coupons',
            'amazon-auto-links/amazon-auto-links.php' => 'Amazon Auto Links',
            'easy-affiliate-links/easy-affiliate-links.php' => 'Easy Affiliate Links',
            'easyazon/easyazon.php' => 'EasyAzon',
            'thirstyaffiliates/thirstyaffiliates.php' => 'ThirstyAffiliates',
            'woocommerce-cloak-affiliate-links/woocommerce-cloak-affiliate-links.php' => 'WooCommerce Cloak Affiliate Links',
            'wp-auto-affiliate-links/WP-auto-affiliate-links.php' => 'Auto Affiliate Links',
            'yith-woocommerce-affiliates/init.php' => 'YITH WooCommerce Affiliates',  
        ];
        $number = 0;
        $pluginNames[] = '';
        
        foreach ($affiliatePlugins as $key => $value) {
            if (is_plugin_active($key)){
                $pluginNames[] = $value;
                $number += 1;
            }
        }
        
        if ($number == 0) {
            $status = 'w';
            $title = "You do not have an active Affiliate Plugin.";
            $message = "If you use affiliate links and don't add 'nofollows' to them, Google can and will penalize you appropriately.";
            $message .= "</br><b>Fix: </b>Install an Affiliate Plugin like <a href='https://wordpress.org/plugins/thirstyaffiliates'>ThirstyAffiliates Affiliate Link Manager</a> that automatically adds 'nofollows' to your affiliate links.";
            $message .= "<div class='note'>Did we miss a plugin? <a href='https://flexithemes.com/contact/'>Let us know</a>.</div>";
        } elseif ($number > 3) {
            $status = 'w';
            $lastItem = array_pop($pluginNames);
            $title = "More than 3 Affiliate plugins detected.";
            $message = "You have " . $number . " active Affiliate plugins: " . implode(', ', $pluginNames) . ' and ' . $lastItem . ".";
            $message .= "</br><b>Fix: </b>Try Keeping only 3 of these plugins.";
        } else {
            $status = 'p';
            $title = "You have an active Affiliate plugin";
            $message = "You are using: " . implode('', $pluginNames);
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks if you search engines are allowed to index this site
     */
    public static function SEOCHECKLIST_check_if_sitemap_exists()
    {
        $urls = [
            get_site_url() . '/sitemap.xml',
            get_site_url() . '/sitemap_index.xml'
        ];
        $number = 0;

        foreach ($urls as $url) {
        	 $result = $this->request->request( $url );
             $statusCode = wp_remote_retrieve_response_code($result);
            if($statusCode == '200'){
                $sitemapUrls[] = $url;
                $number += 1;
            }

        }

        if($number == 0){
            $title = "Sitemap was not detected.";
            $message = "<b>Fix: </b>If you have an active SEO plugin, check if it provides an option to enable the sitemap. If it does not have that option, try <a href='https://wordpress.org/plugins/google-sitemap-generator/'>Google XML Sitemaps</a>";
        } elseif ($number > 1) {
            $status = 'w';
            $lastItem = array_pop($sitemapUrls);
            $title = "Multiple sitemaps detected.";
            $message = "You have " . $number . " active sitemaps: " . implode(', ', $sitemapUrls) . ' and ' . $lastItem . ".";
            $message .= "</br><b>Fix</b>Make sure you have only one active sitemap.";
        } else {
            $status = 'p';
            $title = "Sitemap was detected.";
            $message = "Your sitemap url is: " . implode(' ', $sitemapUrls);
        }
       
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks if you search engines are allowed to index this site
     */
    public static function SEOCHECKLIST_check_for_google_analytics_script()
    {
        $file = file_get_contents(get_site_url());
        $script_check = strpos($file, '/analytics.js');
        $script_check2 = strpos($file, '/ga.js');

        if ($script_check || $script_check2) {
            $status = 'p';
            $title = "Google Analytics Script was found.";
            $message = '';
        } else {
            $title = "Google Analytics script was not found.";
            $message = "<b>Fix: </b>Install a <a href='https://wordpress.org/plugins/google-analytics-dashboard-for-wp/'>google analytics plugin</a> or insert the script manually. Learn how it’s valuable in our <a href='https://flexithemes.com/wordpress-google-search-console/'>advanced SEO guide.</a>";
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks number of Active Plugins
     */
    public static function SEOCHECKLIST_check_number_of_active_plugins()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        $plugins = get_option('active_plugins');
        $number = 0;
        
        foreach ($plugins as $plugin) {
            $number += 1;
        }

        if ($number <= 20) {
            $status = 'p';
            $title = "You don't have too many Active Plugins.";
            $message = "You have " . $number . " Active Plugins.";
        } elseif ($number > 20 && $number <= 50) {
            $status = 'w';
            $title = "More than 20 active plugins detected.";
            $message = "You have " . $number . " Active Plugins. Try keeping this number not more than 20.";
            $message .= "</br><b>Fix: </b>Uninstall plugins that may not be in use or that may be slowing your site down. Just stick to the bare necessities.";
        } else {
            $title = "More than 50 active plugins detected.";
            $message = "You have " . $number . " Active Plugins. Try keeping this number not more than 20.";
            $message .= "</br><b>Fix: </b>Uninstall plugins that may not be in use or that may be slowing your site down. Just stick to the bare necessities.";
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Checks h1 tags
     */
    public static function SEOCHECKLIST_check_h1_tags()
    {
        $page = get_site_url();
        $html = file_get_contents($page);
        $dom = new domDocument('1.0', 'utf-8');
        $dom->loadHTML($html);
        $dom->preserveWhiteSpace = false; 
        $h1tags = $dom->getElementsByTagName('h1');
        $number = 0;
        
        foreach ($h1tags as $h1tag) {
            $number += 1;
            $h1texts[] = $h1tag->nodeValue;
        }
        
        if ($number == 0) {
            $title = "h1 tag was not found.";
            $message = "<b>Fix: </b>Make sure you have an h1 tag.";
        } 
        elseif ($number == 1) {
            $h1length = strlen(implode('', $h1texts));
            if ($h1length <= 70){
                $status = 'p';
                $title = 'h1 tag was found.';
                $message = "Your h1 tag for the home page is: " . implode(', ', $h1texts);
                $message .= ".</br>The length of your h1 tag is: " . $h1length . " characters and is not too long.";
            } else {
                $status = 'w';
                $title = "Your h1 tag is too long.";
                $message = "Your h1 tag is: " . implode(', ', $h1texts);
                $message .= ".</br>The length of your h1 tag is " . $h1length . " characters." ;
                $message .= "</br><b>Fix: </b>Make sure your h1 tag is not more than 70 characters.";
            }
        }
        else {
            $lastItem = array_pop($h1texts);
            $title = "You have more than 1 h1 tags.";
            $message = "You have " . $number . " h1 tags: " . implode(', ', $h1texts) . ' and ' . $lastItem . '.';
            $message .= "</br><b>Fix: </b>Make sure you have only 1 h1 tag.";
        }

        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Lists top 5 keywords that appear the most on the page
     */
    public static function SEOCHECKLIST_keyword_test()
    {
        $status = 'i';
        
        $page = get_site_url();
        $html = file_get_contents($page);
        preg_match('~<body[^>]*>(.*?)</body>~si', $html, $body);
        $excludewords = array("the", "a", "you", "your", "on", "are", "that", "this", "to", "in", "it", "for", "as", "if");
        $words = str_word_count(strip_tags(strtolower($body[1])), 1);
        $words = array_diff($words, $excludewords);
        $words = array_count_values($words);
        arsort($words);
        
        $keyWords = array_slice($words, 0, 5);

        $title = 'Check if the most frequently used keywords focus on the intended topic of your page.';
        $message = 'Top 5 keywords that appear the most on your home page are:</br>';
        foreach($keyWords as $keyWord => $count){
            $message .= $keyWord . ' => ' . $count . ' times.</br>';
        }
        SEOCHECKLIST_SEOChecks::SEOCHECKLIST_return_json_object($title, $message, $status);
    }
    
    /**
     * Echoes json object for you
     * @param $title string
     * @param $message string
     * @param $fixMessage string
     * @param $status 'passed'| 'p' | 'failed' | 'f' | 'warning' | 'w'
     * @param $location 'list'|'sidebar'
     * @param $debug
     */
    public static function SEOCHECKLIST_return_json_object($title, $message, $status = 'failed', $location = 'list', $debug = [])
    {
        if ($status == '') {
            $status = 'failed';
        } else if ($status == 'p') {
            $status = 'passed';
        } else if ($status == 'i') {
            $status = 'info';
        } else if ($status == 'w') {
            $status = 'warning';
        }

        $messages = [];
        $messages['title'] = $title;
        $messages['message'] = $message;

        echo json_encode(['status' => $status, 'messages' => $messages, 'location' => $location, 'debug' => $debug]);
        die();
    }

    /**
     * @return array
     */
    public function SEOCHECKLIST_seo_checks()
    {
        return $this->check_list;
    }

    /**
     * Return list with checks
     */
    public function SEOCHECKLIST_return_seo_checks()
    {
        $check = new SEOCHECKLIST_SEOChecks();
        echo json_encode($check->SEOCHECKLIST_seo_checks());
        die();
    }

    /** 
     Return the response time

    */
     public function SEOCHECKLIST_store_total_time( $headers, $info)
     {
        $this->total_time = $info['total_time'];
     }
    
}
