<?php
/*
  Plugin Name: Jigoshop - Dibs Payment Gateway
  Plugin URI: http://bazooka.se/
  Description: Allows you to use Dibs payment gateway with the Jigoshop ecommerce plugin.
  Version: 00.09
  Author: Esbjörn Eriksson
  Author URI: http://bazooka.se/
 */


/*  Copyright 2012  Bazooka AB  (email : info@bazooka.se)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add a custom payment class after Jigoshop has loaded */
add_action('plugins_loaded', 'jigoshop_dibspayment', 0);
function jigoshop_dibspayment()
{
	if (!class_exists('jigoshop_payment_gateway'))
		return; // if the Jigoshop payment gateway class is not available, do nothing

	/**
	 * Add the gateway to JigoShop
	 **/
	function add_dibspayment_gateway( $methods ) {
		$methods[] = 'dibspayment';
		return $methods;
	}
	add_filter( 'jigoshop_payment_gateways', 'add_dibspayment_gateway', 50 );


	class dibspayment extends jigoshop_payment_gateway {

		public function __construct() {
			
			parent::__construct();
			
			$this->id = 'dibspayment';
			$this->icon = '';
			$this->has_fields = false;
			$this->enabled = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_enabled');
			$this->title = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_title');
			$this->merchant = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_merchant');
			$this->description  = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_description');
			$this->testmode = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_testmode');
			$this->MAC_key = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_key');
			$this->instant = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_instant');
			$this->language = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_language');

			add_action('init', array(&$this, 'check_callback') );
			add_action('valid-dibspayment-callback', array(&$this, 'successful_request') );
			add_action('receipt_dibspayment', array(&$this, 'receipt_page'));
			add_filter('jigoshop_thankyou_message', array(&$this, 'thankyou_message') );

		}


		/**
		 * Default Option settings for WordPress Settings API using the Jigoshop_Options class
		 *
		 * These will be installed on the Jigoshop_Options 'Payment Gateways' tab by the parent class 'jigoshop_payment_gateway'
		 *
		 */	
		protected function get_default_options() {
		
			$defaults = array();
			
			// Define the Section name for the Jigoshop_Options
			$defaults[] = array( 
				'name' => __('DIBS Payment Window', 'jigoshop'), 'type' => 'title', 
				'desc' => __('This is the new DIBS Payment Window for 2012. It works by sending the user to <a href="http://www.dibspayment.com/">DIBS</a> to enter their payment information.', 'jigoshop')
				);
			
			// List each option in order of appearance with details
			$defaults[] = array(
				'name'		=> __('Enable DIBS Payment Window','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> '',
				'id' 		=> 'jigoshop_dibspayment_enabled',
				'std' 		=> 'no',
				'type' 		=> 'checkbox',
				'choices'	=> array(
					'no'			=> __('No', 'jigoshop'),
					'yes'			=> __('Yes', 'jigoshop')
				)
			);
			
			$defaults[] = array(
				'name'		=> __('Method Title','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> __('This controls the title which the user sees during checkout.','jigoshop'),
				'id' 		=> 'jigoshop_dibspayment_title',
				'std' 		=> __('DIBS','jigoshop'),
				'type' 		=> 'text'
			);
			
			$defaults[] = array(
				'name'		=> __('Description','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> __('This controls the description which the user sees during checkout.','jigoshop'),
				'id' 		=> 'jigoshop_dibspayment_description',
				'std' 		=> __("Pay via DIBS using credit card or bank transfer.", 'jigoshop'),
				'type' 		=> 'longtext'
			);

			$defaults[] = array(
				'name'		=> __('DIBS Merchant ID','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> __('Please enter your DIBS merchant id; this is needed in order to take payment!','jigoshop'),
				'id' 		=> 'jigoshop_dibspayment_merchant',
				'std' 		=> '',
				'type' 		=> 'text'
			);

			$defaults[] = array(
				'name'		=> __('DIBS MAC Key','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> __('Please enter your DIBS MAC key; this will further secure your payments.','jigoshop'),
				'id' 		=> 'jigoshop_dibspayment_key',
				'std' 		=> '',
				'type' 		=> 'longtext'
			);

			$defaults[] = array(
				'name'		=> __('Enable test mode','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> __('When test mode is enabled only DIBS specific test-cards are accepted.','jigoshop'),
				'id' 		=> 'jigoshop_dibspayment_testmode',
				'std' 		=> 'no',
				'type' 		=> 'checkbox',
				'choices'	=> array(
					'no'			=> __('No', 'jigoshop'),
					'yes'			=> __('Yes', 'jigoshop')
				)
			);

			$defaults[] = array(
				'name'		=> __('Language','jigoshop'),
				'desc' 		=> '',
				'tip' 		=> __('Show Dibs Payment Window in this language. If set to WPML detect, it switches between the languages listed here, but if not found defaults to English.','jigoshop'),
				'id' 		=> 'jigoshop_dibspayment_language',
				'std' 		=> 'en',
				'type' 		=> 'select',
				'choices'	=> array(
					'detect_wpml' => __('Detect using WPML', 'jigoshop'),
					'en_US'				=> __('English (US)', 'jigoshop'),
					'en_GB'				=> __('English (GB)', 'jigoshop'),
					'da_DK'				=> __('Danish', 'jigoshop'),
					'sv_SE'				=> __('Swedish', 'jigoshop'),
					'nb_NO'				=> __('Norwegian (Bokmål)', 'jigoshop'),
				)
			);

			return $defaults;
		}


		/**
		* There are no payment fields for dibs, but we want to show the description if set.
		**/
		function payment_fields() {
			if ($jigoshop_dibspayment_description = Jigoshop_Base::get_options()->get_option('jigoshop_dibspayment_description')) echo wpautop(wptexturize($jigoshop_dibspayment_description));
		}

		/**
		* Generate the dibs button link
		**/
		public function generate_form( $order_id ) {
			
			$order = new jigoshop_order( $order_id );

			$action_adr = 'https://sat1.dibspayment.com/dibspaymentwindow/entrypoint';

			// filter redirect page
			$checkout_redirect = apply_filters( 'jigoshop_get_checkout_redirect_page_id', jigoshop_get_page_id('thanks') );

			// Define language
			$lang = $this->language;
			if ( $this->language == 'detect_wpml' ) {
				$valid_wpml_languages = array('da','en','sv','nb');
				if ( defined(ICL_LANGUAGE_CODE) && in_array(ICL_LANGUAGE_CODE, $valid_wpml_languages) ) {
					$lang = ICL_LANGUAGE_CODE;
				} else {
					$lang = 'en';
				}
			}

			$args =
				array(
					// Merchant
					'merchant'   => $this->merchant,

					// Session
					'language'   => $lang,

					// Order
					'amount'     => $order->order_total * 100,
					'orderId'    => $order_id,
					'currency'   => Jigoshop_Base::get_options()->get_option('jigoshop_currency'),

					// URLs
					'callbackUrl' => site_url('/jigoshop/dibscallback'),
					'acceptReturnUrl' =>  get_permalink($checkout_redirect),
					'cancelReturnUrl' => site_url('/jigoshop/dibscancel'),

					// Custom
					's_jigoshop_order_key' => $order->order_key,
			);

			if ( $this->instant ) {
				$args['capturenow'] = 1;
			}

			if ( $this->testmode == 'yes' ) {
				$args['test'] = 1;
			}


			// Calculate HMAC
			if ( $this->MAC_key != '' ) {
				$args['MAC'] = $this->dibs_calculate_mac($args);
			}



			$fields = '';
			foreach ($args as $key => $value) {
				$fields .= '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'" />';
			}

			return '<form action="'.$action_adr.'" method="post" id="dibs_payment_form">
					' . $fields . '
					<input type="submit" class="button-alt" id="submit_dibs_payment_form" value="'.__('Pay via DIBS', 'jigoshop').'" /> <a class="button cancel" href="'.esc_url($order->get_cancel_order_url()).'">'.__('Cancel order &amp; restore cart', 'jigoshop').'</a>
					<script type="text/javascript">
						jQuery(function(){
							jQuery("body").block(
								{
									message: "<img src=\"'.jigoshop::assets_url().'/assets/images/ajax-loader.gif\" alt=\"Redirecting...\" />'.__('Thank you for your order. We are now redirecting you to DIBS to make payment.', 'jigoshop').'",
									overlayCSS:
									{
										background: "#fff",
										opacity: 0.6
									},
									css: {
												padding:        20,
												textAlign:      "center",
												color:          "#555",
												border:         "3px solid #aaa",
												backgroundColor:"#fff",
												cursor:         "wait"
										}
								});
							jQuery("#submit_dibs_payment_form").click();
						});
					</script>
				</form>';

		}

		/**
		 * Process the payment and return the result
		 **/
		function process_payment( $order_id ) {

			$order = new jigoshop_order( $order_id );

			return array(
				'result' => 'success',
				'redirect' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, apply_filters('jigoshop_get_return_url', get_permalink(jigoshop_get_page_id('pay')))))
			);

		}

		/**
		* receipt_page
		**/
		function receipt_page( $order ) {

			echo '<p>'.__('Thank you for your order, please click the button below to pay with DIBS.', 'jigoshop').'</p>';

			echo $this->generate_form( $order );

		}

		/**
		* Check for DIBS Response
		**/
		function check_callback() {

			// Cancel order POST
			if ( strpos($_SERVER["REQUEST_URI"], 'jigoshop/dibscancel') !== false) {
				$this->cancel_order(stripslashes_deep($_POST));
				return;
			}

			if ( strpos($_SERVER["REQUEST_URI"], 'jigoshop/dibscallback') !== false ) {
				header("HTTP/1.1 200 Ok");
				do_action("valid-dibspayment-callback", stripslashes_deep($_POST));
				
			}
		}

		// This is a modified version of jigoshop_cancel_order. 
		// We must have our own since the original checks nonce on GET variables.
		function cancel_order($posted) {
			if(isset($posted['orderId']) && is_numeric($posted['orderId']) && isset($posted['s_jigoshop_order_key']) ) {

				// Also verify HMAC
				$MAC = $this->dibs_calculate_mac($posted);

				// Cancel order the same way as jigoshop_cancel_order
				$order_id = $_POST['orderId'];
				$order_key = $_POST['s_jigoshop_order_key'];

				$order = new jigoshop_order( $order_id );

				if ($posted['MAC'] == $MAC && $order->id == $order_id && $order->order_key == $order_key && $order->status=='pending') :

					// Cancel the order + restore stock
					$order->cancel_order( __('Order cancelled by customer.', 'jigoshop') );

					// Message
					jigoshop::add_message( __('Your order was cancelled.', 'jigoshop') );

				elseif ($order->status!='pending') :

					jigoshop::add_error( __('Your order is no longer pending and could not be cancelled. Please contact us if you need assistance.', 'jigoshop') );

				else :

					jigoshop::add_error( __('Invalid order.', 'jigoshop') );

				endif;

				wp_safe_redirect(jigoshop_cart::get_cart_url());
				exit;

			}
		}

		function thankyou_message($message) {
			
			// Fake a GET request for the Thank you page
			if(isset($_POST['orderId']) && is_numeric($_POST['orderId']) && isset($_POST['s_jigoshop_order_key']) ) {
				$_GET['order'] = $_POST['orderId'];
				$_GET['key'] = $_POST['s_jigoshop_order_key'];
			}
				
			return $message;
			
		}


		/**
		* Successful Payment!
		**/
		function successful_request( $posted ) {
		
			// Custom holds post ID
			if ( !empty($posted['transaction']) && !empty($posted['orderId']) && is_numeric($posted['orderId']) ) {

				$error = FALSE;
				$order_id = (int) $posted['orderId'];
				$order_key = $posted['s_jigoshop_order_key'];
				$transaction_id = $posted['transaction'];

				// Load this order from database
				$order = new jigoshop_order( $order_id );

				// Verify HMAC
				$MAC = $this->dibs_calculate_mac($posted);
				
				if($posted['MAC'] != $MAC) {
					$log = sprintf( __('DIBS transaction %s failed Dibs security check. HMAC from dibs was %s and calculated HMAC was %s.', 'jigoshop'), 
						$transaction_id, $posted['MAC'], $MAC ) ;
					error_log($log);
					$order->add_order_note($log);
					$error = TRUE;
				}

				// Check transaction status
				if ( ! isset($_POST['status']) || $_POST['status'] != 'ACCEPTED' ) {
					$log = sprintf( __('DIBS transaction %s was completed but not successful. Please check Dibs administration for more info.', 'jigoshop'), $transaction_id ) ;
					error_log($log);
					$order->add_order_note($log);
					$error = TRUE;
				}

				// Verify order key
				if ($order->order_key !== $posted['s_jigoshop_order_key']) {
					$log = sprintf( __('DIBS transaction %s failed Jigoshop security check. Key from dibs was %s and stored key was %s.', 'jigoshop'), 
						$transaction_id, $order_key, $order->order_key ) ;
					error_log($log);
					$order->add_order_note($log);
					$error = TRUE;
				}

				if ( $error ) print "Error.\n";

				// If all went well, complete order
				if ($error === FALSE && $order->status !== 'completed') {
					
					$order->add_order_note( sprintf( __('DIBS payment completed with transaction id %s.', 'jigoshop'), $transaction_id ) );
					$order->payment_complete();
					print "Success.\n";
				}

			}

			exit('Dibs callback complete.');

		}



		// Found here: http://tech.dibs.dk/dibs_api/other_features/mac_calculation/

		// This function converts an array holding the form key values to a string.
		// The generated string represents the message to be signed by the MAC.
		function dibs_create_message($formKeyValues) {
			$string = "";
			if (is_array($formKeyValues)) {
				ksort($formKeyValues); // Sort the posted values by alphanumeric
				foreach ($formKeyValues as $key => $value) {
					if ($key != "MAC") { // Don't include the MAC in the calculation of the MAC.
						if (strlen($string) > 0) $string .= "&";
						$string .= "$key=$value"; // create string representation
					}
				}
				return $string;
		 
			} else {
				return "An array must be used as input!";
			}
		}

		// This function converts from a hexadecimal representation to a string representation.
		function dibs_hextostr($hex) {
			$string = "";
			foreach (explode("\n", trim(chunk_split($hex, 2))) as $h) {
				$string .= chr(hexdec($h));
			}
		 
			return $string;
		}

		// This function calculates the MAC for an array holding the form key values.
		// The $logfile is optional.
		function dibs_calculate_mac($formKeyValues, $logfile = null) {
			$HmacKey = $this->MAC_key;

			// Create the message to be signed.
			if (is_array($formKeyValues)) {
				$messageToBeSigned = $this->dibs_create_message($formKeyValues);
				// Calculate the MAC.
				$MAC = hash_hmac("sha256", $messageToBeSigned, $this->dibs_hextostr($HmacKey));
			 
				// Following is only relevant if you wan't to log the calculated MAC to a log file.
				if ($logfile) {
					$fp = fopen($logfile, 'a') or exit("Can't open $logfile!");
					fwrite($fp, "messageToBeSigned: " . $messageToBeSigned . PHP_EOL
						. " HmacKey: " . $HmacKey . PHP_EOL . " generated MAC: " . $MAC . PHP_EOL);
					if (isset($formKeyValues["MAC"]) && $formKeyValues["MAC"] != "")
						fwrite($fp, " posted MAC:    " . $formKeyValues["MAC"] . PHP_EOL);
				}
			 
				return $MAC;
		 
			} else {
				die("Form key values must be given as an array");
			}
		}
		
	}

}
