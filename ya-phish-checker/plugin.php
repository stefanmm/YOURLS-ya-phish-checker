<?php
/*
Plugin Name: YA Phish Checker
Plugin URI: https://github.com/stefanmm/YOURLS-ya-phish-checker
Description: Yet Another Phish Checker - Prevent shortening malware URLs using ipqualityscore.com API (fork of phishtank-2.0)
Version: 1.1
Author: Stefan Marjanov
Author URI: https://www.stefanmarjanov.com/
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Add the admin page
yourls_add_action( 'plugins_loaded', 'yapc_add_page' );

function yapc_add_page() {
   yourls_register_plugin_page( 'yapc', 'YA Phish Checker', 'yapc_do_page' );
}

// Display admin page
function yapc_do_page() {
	$is_page_updated = "";
	// Check if a form was submitted
	if( isset( $_POST['yapc_api_key'] ) ) {
		// Check nonce
		yourls_verify_nonce( 'yapc' );
		
		// Process form - update option in database
		yourls_update_option( 'yapc_api_key', $_POST['yapc_api_key'] );
		if(isset($_POST['yapc_err_msg'])) yourls_update_option( 'yapc_err_msg', $_POST['yapc_err_msg'] );
		if(isset($_POST['yapc_recheck'])) yourls_update_option( 'yapc_recheck', $_POST['yapc_recheck'] );
		if(isset($_POST['yapc_soft'])) yourls_update_option( 'yapc_soft', $_POST['yapc_soft'] );
		if(isset($_POST['yapc_cust_toggle'])) yourls_update_option( 'yapc_cust_toggle', $_POST['yapc_cust_toggle'] );
		if(isset($_POST['whitelist_form'])){
			$whitelist_form = explode ( "\r\n" , $_POST['whitelist_form'] );
			yourls_update_option ('yapc_whitelisted', serialize($whitelist_form));
		}
		$is_page_updated = "Settings saved!";
	}

	// Get values from database
	$yapc_api_key = yourls_get_option( 'yapc_api_key' );
	$yapc_err_msg = (yourls_get_option( 'yapc_err_msg' )) ? yourls_get_option( 'yapc_err_msg' ) : "This domain is reported as dangerous";
	$yapc_recheck = yourls_get_option( 'yapc_recheck' );
	$yapc_soft = yourls_get_option( 'yapc_soft' );
	$yapc_cust_toggle = yourls_get_option( 'yapc_cust_toggle' );
	$yapc_intercept = yourls_get_option( 'yapc_intercept' );
	$whitelist = yourls_get_option ('yapc_whitelisted');
	if($whitelist){
		$whitelist = implode ( "\r\n" , unserialize ( $whitelist ) );
	}
	
	// set defaults
	if ($yapc_recheck !== "false") {
		$rck_chk = 'checked';
		$vis_rck = 'inline';
		} else {
		$rck_chk = null;
		$vis_rck = 'none';
		}
	if ($yapc_soft !== "false") { 
		$pl_ck = 'checked';
		$vis_pl = 'inline';
		} else {
		$pl_ck = null;
		$vis_pl = 'none';
		}
	if ($yapc_cust_toggle !== "true") { 
		$url_chk = null;
		$vis_url = 'none';
		} else {
		$url_chk = 'checked';
		$vis_url = 'inline';
		}

	// Create nonce
	$nonce = yourls_create_nonce( 'yapc' );

	echo <<<HTML
		<div id="wrap">
		<h2>YA Phish Checker Settings</h2>
		<p>You can use ipqualityscore's free API key, but you will get only 5.000 API calls per month. <a href="https://www.ipqualityscore.com/create-account" target="_blank">Click here</a> to learn more, or to register account and obtain a key. You will find your API key <a href="https://www.ipqualityscore.com/documentation/malicious-url-scanner-api/overview" target="_blank">HERE</a> (must be logged-in)</p>
		
		<form method="post" autocomplete="off">
		<input type="hidden" name="nonce" value="$nonce" />
		<p><label for="yapc_api_key">Your Private API Key:  </label> <input type="password" autocomplete="false" size=60 id="yapc_api_key" name="yapc_api_key" value="$yapc_api_key" /></p>

		<h2>Redirect Rechecks: old links behavior</h2>
		<p>Old links can be re-checked every time that they are clicked. <b>Default behavior is to check them</b>. Important: every URL redirect = 1 API call. Whitelisted domains will not be checked.</p>

		<div class="checkbox">
		  <label>
		    <input type="hidden" name="yapc_recheck" value="false" />
		    <input name="yapc_recheck" type="checkbox" value="true" $rck_chk > Recheck old links?
		  </label>
		</div>

		<div style="display:$vis_rck;" >
			<p>You can decide to either preserve or delete links that fail a re-check here. <b>Default is to preserve them</b>, as many links tend not to stay blacklisted indefinately.</p>
		
			<div class="checkbox">
			  <label>
			    <input type="hidden" name="yapc_soft" value="false" />
			    <input name="yapc_soft" type="checkbox" value="true" $pl_ck > Preserve links & intercept on failed re-check?
			  </label>
			  <p>Links that fail re-checks and are preserved are added to the <a href="https://github.com/joshp23/YOURLS-Compliance" target="_blank" >Compliance</a> flaglist if it is installed.</p>
			</div>
		
			<div class="checkbox" style="display:$vis_pl;">
			  <label>
				<input name="yapc_cust_toggle" type="hidden" value="false" /><br>
				<input name="yapc_cust_toggle" type="checkbox" value="true" $url_chk >Use Custom Intercept URL?
			  </label>
			</div>
			<div style="display:$vis_url;">
				<p>Setting the above option without setting this will fall back to default behavior.</p>
				<p><label for="yapc_intercept">Intercept URL </label> <input type="text" size=40 id="yapc_intercept" name="yapc_intercept" value="$yapc_intercept" /></p>
			</div>
		</div>
		<h2>Whitelist domains</h2>
		<p>Domains from this list will NOT be checked for spam! <strong>API will not be called</strong>. One domain per line. Example: domain.com will match its sub-domains (like www.domain.com) as well as all sub-pages (like domain.com/page) and combinations (like www.domain.com/page)</p>
		<p><textarea cols="60" rows="15" name="whitelist_form">$whitelist</textarea></p>
		
		<h2>Other settings</h2>
		<p><label for="yapc_err_msg">Default error message:  </label> <input type="text" size=60 id="yapc_err_msg" name="yapc_err_msg" value="$yapc_err_msg" /></p>
		<p><input type="submit" value="Save settings" style="cursor: pointer;padding: 5px 14px;" /></p>
		</form>
		<div>$is_page_updated</div>
		</div>
HTML;
}

// Check if URL is whitelisted
function yapc_is_whitelisted( $url ) {
	$whitelisted = false;
	
	$whitelist = yourls_get_option ('yapc_whitelisted');
	if ( $whitelist ) {
		$whitelist = unserialize ( $whitelist );
		foreach($whitelist as $whitelist_domain) {
			if ( strpos($url, $whitelist_domain) ) {
				$whitelisted = true;
				break;
			}
		}
	}
	return $whitelisted;
}

// Check yapc when a new link is added
yourls_add_filter( 'shunt_add_new_link', 'yapc_check_add' );
function yapc_check_add( $false, $url ) {
    $url = yourls_sanitize_url( $url );
	// Only check for http(s)
    if( in_array( yourls_get_protocol( $url ), array( 'http://', 'https://' ) ) ) {
		if ( yapc_is_blacklisted( $url ) ) {
			
			$yapc_err_msg = (yourls_get_option( 'yapc_err_msg' )) ? yourls_get_option( 'yapc_err_msg' ) : "This domain is reported as dangerous";
			
			return array(
				'status' => 'fail',
				'code'   => 'error:spam',
				'message' => $yapc_err_msg,
				'errorCode' => '403',
			);
		}
	}
	// All clear, not interrupting the normal flow of events
	return $false;
}

// Re-Check yapc on redirection
yourls_add_action( 'redirect_shorturl', 'yapc_check_redirect' );
function yapc_check_redirect( $url, $keyword = false ) {
	// Are we performing rechecks?
	$yapc_recheck = yourls_get_option( 'yapc_recheck' );
	if ($yapc_recheck !== "false" ) {
		if( is_array( $url ) && $keyword == false ) {
			$keyword = $url[1];
			$url = $url[0];
		}

		// Check when the link was added
		// If shorturl is fresh (ie probably clicked more often?) check once every 10 times, otherwise check every time
		// Define fresh = 3 days = 259200 secondes
		$now  = date( 'U' );
		$then = date( 'U', strtotime( yourls_get_keyword_timestamp( $keyword ) ) );
		$chances = ( ( $now - $then ) > 259200 ? 10 : 1 );
		if( $chances == mt_rand( 1, $chances ) ) {
			if( yapc_is_blacklisted( $url ) ) {
				// We got a hit, do we delete or intercept?
				$yapc_soft = yourls_get_option( 'yapc_soft' );
				// Intercept by default
				if( $yapc_soft !== "false" ) {
					// Compliance integration
					if((yourls_is_active_plugin('compliance/plugin.php')) !== false) {
						global $ydb;
						$table = YOURLS_DB_PREFIX . 'flagged';
						$binds = array('keyword' => $keyword, 'reason' => 'ipqualityscore Auto-Flag');
						$sql = "REPLACE INTO `$table` (keyword, reason) VALUES (:keyword, :reason)";
						$insert = $ydb->fetchAffected($sql, $binds);
					}
					// use default intercept page?
					$yapc_cust_toggle = yourls_get_option( 'yapc_cust_toggle' );
					$yapc_intercept = yourls_get_option( 'yapc_intercept' );
					if (($yapc_cust_toggle == "true") && ($yapc_intercept !== '')) {
						// How to pass keyword and url to redirect?
						yourls_redirect( $yapc_intercept, 302 );
						die();
					}
					// Or go to default flag intercept 
					yapc_display_phlagpage( $keyword );
				} else {
					// Otherwise delete & die
					yourls_delete_link_by_keyword( $keyword );
					http_response_code(403);
					echo "<div style='margin: 20px auto 20px auto;'><center><p>The page that you are trying to visit has been blacklisted. We have deleted this link from our records. Have a nice day.</p></center></div>";
					die();
				} 
			}
		}
		// Nothing found, move along
	}
	// Re-check disabled, move along
}
// Soft on Spam ~ intercept warning
function yapc_display_phlagpage($keyword) {

	$title = yourls_get_keyword_title( $keyword );
	$url   = yourls_get_keyword_longurl( $keyword );
	$base  = YOURLS_SITE;
	$img   = yourls_plugin_url( dirname( __FILE__ ).'/assets/caution.png' );
	$css   = yourls_plugin_url( dirname( __FILE__ ).'/assets/bootstrap.min.css');

	$vars = array();
		$vars['keyword'] = $keyword;
		$vars['title'] = $title;
		$vars['url'] = $url;
		$vars['base'] = $base;
		$vars['img'] = $img;
		$vars['css'] = $css;

	$intercept = file_get_contents( dirname( __FILE__ ) . '/assets/intercept.php' );
	// Replace all %stuff% in the intercept with variable $stuff
	$intercept = preg_replace_callback( '/%([^%]+)?%/', function( $match ) use( $vars ) { return $vars[ $match[1] ]; }, $intercept );

	echo $intercept;

	die();
}

// Is the link spam? true / false 
function yapc_is_blacklisted( $url ) {
	$parsed = parse_url( $url );
	
	if( !isset( $parsed['host'] ) )
		return yourls_apply_filter( 'yapc_malformed', false );
	
	// Remove www. from domain (but not from www.com)
	$parsed['host'] = preg_replace( '/^www\.(.+\.)/i', '$1', $parsed['host'] );
	
	// Return early if domain is whitelisted
	if( yapc_is_whitelisted($url) ){
		return yourls_apply_filter( 'yapc_clean', false );
	}
	
	// encoded url
	$url = urlencode($url);
	
	// ipqualityscore API key
	$yapc_api_key = yourls_get_option( 'yapc_api_key' );

	$parameters = array(
		'strictness' => 0
	);
	$formatted_parameters = http_build_query($parameters);
        
	$url = sprintf(
		'https://www.ipqualityscore.com/api/json/url/%s/%s?%s',
		$yapc_api_key,
		$url,
		$formatted_parameters
	);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);

	$json = curl_exec($curl);
	curl_close($curl);

	// Decode the result into an array.
	$result = json_decode($json, true);
	
	if(isset($result['success']) && $result['success'] === true){
		if($result['suspicious'] === true){
			// flag suspicious URL
			return yourls_apply_filter( 'yapc_blacklisted', true );
		}
	}

	// All clear, probably not spam
	return yourls_apply_filter( 'yapc_clean', false );
}