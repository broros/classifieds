<?php
/**
* The template for displaying the Checkout page.
* You can override this file in your active theme.
*
* @package Classifieds
* @subpackage UI Front
* @since Classifieds 2.0
*/

global $current_user;

$current_user = wp_get_current_user();

$cf_options = $this->get_options();

$step = get_query_var('cf_step');
$step = (empty($step)) ? '' : $step;

$step = (empty($_GET['step'])) ? $step : $_GET['step'];

$error = get_query_var('cf_error');
$error = (empty($error)) ? '' : $error;

?>

<?php //print_r('start'); exit; ?>

<?php
if ( $this->is_full_access() && $step != 'success' && $step != 'api_call_error' ) {
	_e( 'You already have access to add ads.', $this->text_domain );
	$step = '';
}
?>

<?php if ( $step == 'disabled' ): ?>

<?php _e( 'This feature is currently disabled by the system administrator.', $this->text_domain ); ?>

<?php elseif ( !empty($error) ): ?>
<div class="invalid-login"><?php echo $error; ?></div>
<?php endif; ?>

<!--End PreLogin -->






<?php if ( $step == 'terms'): ?>
<!-- Begin Terms -->

<form action="#" method="post"  class="cf-checkout">

	<strong><?php _e( 'Cost of Service', $this->text_domain ); ?></strong>
	<table <?php do_action( 'billing_invalid' ); ?>>
		<?php if($this->use_credits && ! $this->is_full_access() ): ?>
		<tr>
			<td>
			<label for="billing"><?php _e( 'Buy Credits', $this->text_domain ) ?></label></td>
				<td>
					<input type="radio" name="billing" value="credits" checked />
					<select name="credits_cost">
						<?php for ( $i = 1; $i <= 10; $i++ ): ?>
						<?php $credits = 10 * $i; ?>
						<?php $amount = $credits * $cf_options['payments']['cost_credit']; ?>
						<option value="<?php echo $amount; ?>" <?php if ( isset( $_POST['credits_cost'] ) && $_POST['credits_cost'] == $amount ) echo 'selected="selected"'; ?> ><?php echo $credits; ?> Credits for <?php echo $amount . ' ' . $cf_options['payment_types']['paypal']['currency']; ?></option>
							<?php endfor; ?>
						</select>
					</td>
				</tr>
				<?php endif; ?>
				<?php if ( $this->use_annual ): ?>
				<tr>
					<td><label for="billing"><?php echo $cf_options['payments']['annual_txt']; ?></label></td>
					<td>
						<input type="radio" name="billing" value="annual" <?php if ( isset( $_POST['billing'] ) && $_POST['billing'] == 'annual' ) echo 'checked="checked"'; ?> />  <?php echo $cf_options['payments']['annual_cost']; ?> <?php echo $cf_options['payment_types']['paypal']['currency']; ?>
						<input type="hidden" name="annual_cost" value="<?php echo $cf_options['payments']['annual_cost']; ?>" />
					</td>
				</tr>
				<?php endif;?>

				<?php if ( $this->use_once ): ?>
				<tr>
					<td><label for="billing"><?php echo $cf_options['payments']['one_time_txt']; ?></label></td>
					<td>
						<input type="radio" name="billing" value="one_time" <?php if ( isset( $_POST['billing'] ) && $_POST['billing'] == 'one_time' ) echo 'checked="checked"'; ?> /> <?php echo $cf_options['payments']['one_time_cost']; ?> <?php echo $cf_options['payment_types']['paypal']['currency']; ?>
						<input type="hidden" name="one_time_cost" value="<?php echo $cf_options['payments']['one_time_cost']; ?>" />
					</td>
				</tr>
				<?php endif;?>
			</table>
			<br />

			<strong><?php _e( 'Terms of Service', $this->text_domain ); ?></strong>
			<table>
				<tr>
					<td><div class="terms"><?php if ( isset( $cf_options['payments']['tos_txt'] ) ) echo nl2br( $cf_options['payments']['tos_txt'] ); ?></div></td>
				</tr>
			</table>
			<br />

			<table  <?php do_action( 'tos_invalid' ); ?> >
				<tr>
					<td><label for="tos_agree"><?php _e( 'I agree with the Terms of Service', $this->text_domain ); ?></label></td>
					<td><input type="checkbox" id="tos_agree" name="tos_agree" value="1" <?php if ( isset( $_POST['tos_agree'] ) ) echo 'checked="checked"'; ?> /></td>
				</tr>
			</table>

			<div class="submit">
				<input type="submit" name="terms_submit" value="<?php _e( 'Continue', $this->text_domain ); ?>" />
			</div>
		</form>

		<?php if ( ! empty($error) ): ?>
		<div class="invalid-login"><?php echo $error; ?></div>
		<?php endif; ?>
		<!-- End Terms -->










		<?php elseif( $step == 'payment_method' ): ?>
		<!-- Begin Payment Method -->
		<?php $cf_options = $this->get_options(); ?>

		<?php if( empty($cf_options['payment_types']['use_free'] )): ?>

		<form action="#" method="post"  class="cf-checkout">
			<strong><?php _e('Choose Payment Method', $this->text_domain ); ?></strong>
			<table>
				<?php if(! empty($cf_options['payment_types']['use_paypal'] )): ?>
				<tr>
					<td><label for="payment_method"><?php _e( 'PayPal', $this->text_domain ); ?></label></td>
					<td>
						<input type="radio" name="payment_method" value="paypal"/>
						<img  src="https://www.paypal.com/en_US/i/logo/PayPal_mark_37x23.gif" border="0" alt="Acceptance Mark">
					</td>
				</tr>
				<?php endif; ?>
				<?php if(! empty($cf_options['payment_types']['use_authorizenet'] )): ?>
				<tr>
					<td><label for="payment_method"><?php _e( 'Credit Card', $this->text_domain ); ?></label></td>
					<td>
						<input type="radio" name="payment_method" value="cc" />
						<img  src="<?php echo CF_PLUGIN_URL; ?>ui-front/general/images/cc-logos-small.jpg" border="0" alt="Solution Graphics">
					</td>
				</tr>
				<?php endif; ?>
			</table>

			<div class="submit">

				<?php if ( $_POST['billing'] == 'credits' ): ?>
				<input type="hidden" name="cost" value="<?php echo $_POST['credits_cost']; ?>" />
				<input type="hidden" name="billing" value="credits" />
				<?php $cf_options = $this->get_options(); ?>
				<?php $credits = ( isset( $_POST['credits_cost'] ) && 0 < $_POST['credits_cost'] ) ? $_POST['credits_cost'] / $cf_options['payments']['cost_credit'] : 0; ?>
				<input type="hidden" name="credits" value="<?php echo $credits; ?>" />
				<?php elseif ( $_POST['billing'] == 'annual' ): ?>
				<input type="hidden" name="cost" value="<?php echo $_POST['annual_cost']; ?>" />
				<input type="hidden" name="billing" value="annual" />
				<?php elseif ( $_POST['billing'] == 'one_time' ): ?>
				<input type="hidden" name="cost" value="<?php echo $_POST['one_time_cost']; ?>" />
				<input type="hidden" name="billing" value="one_time" />
				<?php endif; ?>

				<input type="submit" name="payment_method_submit" value="<?php _e( 'Continue', $this->text_domain ); ?>" />
			</div>
		</form>
		<?php else: ?>
		<strong><?php _e( 'Posting Classified Ads is Free when Logged In' ); ?></strong>
		<?php endif; ?>
		<!--End Payment Method -->









		<?php elseif ( $step == 'cc_details' ): ?>
		<!--Begin CC Details -->

		<form action="#" method="post" class="cf-checkout" id="cfcheckout">

			<strong><?php _e( 'Payment Details', $this->text_domain ); ?></strong>
			<div class="clear"></div>
			<table>
				<tr>
					<td><label for="email"><?php _e( 'Email Address', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="email" name="email" value="<?php echo esc_attr($current_user->user_email); ?>" class="required email" /></td>
				</tr>
				<tr>
					<td><label for="first-name"><?php _e( 'First Name', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="first-name" name="first_name" value="<?php echo esc_attr($current_user->user_firstname); ?>" class="required"  /></td>
				</tr>
				<tr>
					<td><label for="last-name"><?php _e( 'Last Name', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="last-name" name="last_name" value="<?php echo esc_attr($current_user->user_lastname); ?>" class="required"  /></td>
				</tr>
				<tr>
					<td><label for="street"><?php _e( 'Street', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="street" name="street" value="" class="required" /></td>
				</tr>
				<tr>
					<td><label for="city"><?php _e( 'City', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="city" name="city" value="" class="required" /></td>
				</tr>
				<tr>
					<td><label for="state"><?php _e( 'State', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="state" name="state" value="" class="required" /></td>
				</tr>
				<tr>
					<td><label for="zip"><?php _e( 'Postal Code', $this->text_domain ); ?>:</label></td>
					<td><input type="text" id="zip" name="zip" value="" class="required" /></td>
				</tr>
				<tr>
					<td><label for="country"><?php _e( 'Country', $this->text_domain ); ?>:</label></td>
					<td>
						<select id="country" name="country_code"  class="required">
							<option value="">Select One</option>
							<option value="US">United States</option>
							<option value="CA">Canada</option>
							<option value="">----------</option>
							<option value="AF">Afghanistan</option>
							<option value="AL">Albania</option>
							<option value="DZ">Algeria</option>
							<option value="AS">American Samoa</option>
							<option value="AD">Andorra</option>
							<option value="AO">Angola</option>
							<option value="AI">Anguilla</option>
							<option value="AQ">Antarctica</option>
							<option value="AG">Antigua and Barbuda</option>
							<option value="AR">Argentina</option>
							<option value="AM">Armenia</option>
							<option value="AW">Aruba</option>
							<option value="AU">Australia</option>
							<option value="AT">Austria</option>
							<option value="AZ">Azerbaidjan</option>
							<option value="BS">Bahamas</option>
							<option value="BH">Bahrain</option>
							<option value="BD">Bangladesh</option>
							<option value="BB">Barbados</option>
							<option value="BY">Belarus</option>
							<option value="BE">Belgium</option>
							<option value="BZ">Belize</option>
							<option value="BJ">Benin</option>
							<option value="BM">Bermuda</option>
							<option value="BT">Bhutan</option>
							<option value="BO">Bolivia</option>
							<option value="BA">Bosnia-Herzegovina</option>
							<option value="BW">Botswana</option>
							<option value="BV">Bouvet Island</option>
							<option value="BR">Brazil</option>
							<option value="IO">British Indian Ocean Territory</option>
							<option value="BN">Brunei Darussalam</option>
							<option value="BG">Bulgaria</option>
							<option value="BF">Burkina Faso</option>
							<option value="BI">Burundi</option>
							<option value="KH">Cambodia</option>
							<option value="CM">Cameroon</option>
							<option value="CV">Cape Verde</option>
							<option value="KY">Cayman Islands</option>
							<option value="CF">Central African Republic</option>
							<option value="TD">Chad</option>
							<option value="CL">Chile</option>
							<option value="CN">China</option>
							<option value="CX">Christmas Island</option>
							<option value="CC">Cocos (Keeling) Islands</option>
							<option value="CO">Colombia</option>
							<option value="KM">Comoros</option>
							<option value="CG">Congo</option>
							<option value="CK">Cook Islands</option>
							<option value="CR">Costa Rica</option>
							<option value="HR">Croatia</option>
							<option value="CU">Cuba</option>
							<option value="CY">Cyprus</option>
							<option value="CZ">Czech Republic</option>
							<option value="DK">Denmark</option>
							<option value="DJ">Djibouti</option>
							<option value="DM">Dominica</option>
							<option value="DO">Dominican Republic</option>
							<option value="TP">East Timor</option>
							<option value="EC">Ecuador</option>
							<option value="EG">Egypt</option>
							<option value="SV">El Salvador</option>
							<option value="GQ">Equatorial Guinea</option>
							<option value="ER">Eritrea</option>
							<option value="EE">Estonia</option>
							<option value="ET">Ethiopia</option>
							<option value="FK">Falkland Islands</option>
							<option value="FO">Faroe Islands</option>
							<option value="FJ">Fiji</option>
							<option value="FI">Finland</option>
							<option value="CS">Former Czechoslovakia</option>
							<option value="SU">Former USSR</option>
							<option value="FR">France</option>
							<option value="FX">France (European Territory)</option>
							<option value="GF">French Guyana</option>
							<option value="TF">French Southern Territories</option>
							<option value="GA">Gabon</option>
							<option value="GM">Gambia</option>
							<option value="GE">Georgia</option>
							<option value="DE">Germany</option>
							<option value="GH">Ghana</option>
							<option value="GI">Gibraltar</option>
							<option value="GB">Great Britain</option>
							<option value="GR">Greece</option>
							<option value="GL">Greenland</option>
							<option value="GD">Grenada</option>
							<option value="GP">Guadeloupe (French)</option>
							<option value="GU">Guam (USA)</option>
							<option value="GT">Guatemala</option>
							<option value="GN">Guinea</option>
							<option value="GW">Guinea Bissau</option>
							<option value="GY">Guyana</option>
							<option value="HT">Haiti</option>
							<option value="HM">Heard and McDonald Islands</option>
							<option value="HN">Honduras</option>
							<option value="HK">Hong Kong</option>
							<option value="HU">Hungary</option>
							<option value="IS">Iceland</option>
							<option value="IN">India</option>
							<option value="ID">Indonesia</option>
							<option value="INT">International</option>
							<option value="IR">Iran</option>
							<option value="IQ">Iraq</option>
							<option value="IE">Ireland</option>
							<option value="IL">Israel</option>
							<option value="IT">Italy</option>
							<option value="CI">Ivory Coast (Cote D&#39;Ivoire)</option>
							<option value="JM">Jamaica</option>
							<option value="JP">Japan</option>
							<option value="JO">Jordan</option>
							<option value="KZ">Kazakhstan</option>
							<option value="KE">Kenya</option>
							<option value="KI">Kiribati</option>
							<option value="KW">Kuwait</option>
							<option value="KG">Kyrgyzstan</option>
							<option value="LA">Laos</option>
							<option value="LV">Latvia</option>
							<option value="LB">Lebanon</option>
							<option value="LS">Lesotho</option>
							<option value="LR">Liberia</option>
							<option value="LY">Libya</option>
							<option value="LI">Liechtenstein</option>
							<option value="LT">Lithuania</option>
							<option value="LU">Luxembourg</option>
							<option value="MO">Macau</option>
							<option value="MK">Macedonia</option>
							<option value="MG">Madagascar</option>
							<option value="MW">Malawi</option>
							<option value="MY">Malaysia</option>
							<option value="MV">Maldives</option>
							<option value="ML">Mali</option>
							<option value="MT">Malta</option>
							<option value="MH">Marshall Islands</option>
							<option value="MQ">Martinique (French)</option>
							<option value="MR">Mauritania</option>
							<option value="MU">Mauritius</option>
							<option value="YT">Mayotte</option>
							<option value="MX">Mexico</option>
							<option value="FM">Micronesia</option>
							<option value="MD">Moldavia</option>
							<option value="MC">Monaco</option>
							<option value="MN">Mongolia</option>
							<option value="MS">Montserrat</option>
							<option value="MA">Morocco</option>
							<option value="MZ">Mozambique</option>
							<option value="MM">Myanmar</option>
							<option value="NA">Namibia</option>
							<option value="NR">Nauru</option>
							<option value="NP">Nepal</option>
							<option value="NL">Netherlands</option>
							<option value="AN">Netherlands Antilles</option>
							<option value="NT">Neutral Zone</option>
							<option value="NC">New Caledonia (French)</option>
							<option value="NZ">New Zealand</option>
							<option value="NI">Nicaragua</option>
							<option value="NE">Niger</option>
							<option value="NG">Nigeria</option>
							<option value="NU">Niue</option>
							<option value="NF">Norfolk Island</option>
							<option value="KP">North Korea</option>
							<option value="MP">Northern Mariana Islands</option>
							<option value="NO">Norway</option>
							<option value="OM">Oman</option>
							<option value="PK">Pakistan</option>
							<option value="PW">Palau</option>
							<option value="PA">Panama</option>
							<option value="PG">Papua New Guinea</option>
							<option value="PY">Paraguay</option>
							<option value="PE">Peru</option>
							<option value="PH">Philippines</option>
							<option value="PN">Pitcairn Island</option>
							<option value="PL">Poland</option>
							<option value="PF">Polynesia (French)</option>
							<option value="PT">Portugal</option>
							<option value="PR">Puerto Rico</option>
							<option value="QA">Qatar</option>
							<option value="RE">Reunion (French)</option>
							<option value="RO">Romania</option>
							<option value="RU">Russian Federation</option>
							<option value="RW">Rwanda</option>
							<option value="GS">S. Georgia & S. Sandwich Isls.</option>
							<option value="SH">Saint Helena</option>
							<option value="KN">Saint Kitts & Nevis Anguilla</option>
							<option value="LC">Saint Lucia</option>
							<option value="PM">Saint Pierre and Miquelon</option>
							<option value="ST">Saint Tome (Sao Tome) and Principe</option>
							<option value="VC">Saint Vincent & Grenadines</option>
							<option value="WS">Samoa</option>
							<option value="SM">San Marino</option>
							<option value="SA">Saudi Arabia</option>
							<option value="SN">Senegal</option>
							<option value="SC">Seychelles</option>
							<option value="SL">Sierra Leone</option>
							<option value="SG">Singapore</option>
							<option value="SK">Slovak Republic</option>
							<option value="SI">Slovenia</option>
							<option value="SB">Solomon Islands</option>
							<option value="SO">Somalia</option>
							<option value="ZA">South Africa</option>
							<option value="KR">South Korea</option>
							<option value="ES">Spain</option>
							<option value="LK">Sri Lanka</option>
							<option value="SD">Sudan</option>
							<option value="SR">Suriname</option>
							<option value="SJ">Svalbard and Jan Mayen Islands</option>
							<option value="SZ">Swaziland</option>
							<option value="SE">Sweden</option>
							<option value="CH">Switzerland</option>
							<option value="SY">Syria</option>
							<option value="TJ">Tadjikistan</option>
							<option value="TW">Taiwan</option>
							<option value="TZ">Tanzania</option>
							<option value="TH">Thailand</option>
							<option value="TG">Togo</option>
							<option value="TK">Tokelau</option>
							<option value="TO">Tonga</option>
							<option value="TT">Trinidad and Tobago</option>
							<option value="TN">Tunisia</option>
							<option value="TR">Turkey</option>
							<option value="TM">Turkmenistan</option>
							<option value="TC">Turks and Caicos Islands</option>
							<option value="TV">Tuvalu</option>
							<option value="UG">Uganda</option>
							<option value="UA">Ukraine</option>
							<option value="AE">United Arab Emirates</option>
							<option value="GB">United Kingdom</option>
							<option value="UY">Uruguay</option>
							<option value="MIL">USA Military</option>
							<option value="UM">USA Minor Outlying Islands</option>
							<option value="UZ">Uzbekistan</option>
							<option value="VU">Vanuatu</option>
							<option value="VA">Vatican City State</option>
							<option value="VE">Venezuela</option>
							<option value="VN">Vietnam</option>
							<option value="VG">Virgin Islands (British)</option>
							<option value="VI">Virgin Islands (USA)</option>
							<option value="WF">Wallis and Futuna Islands</option>
							<option value="EH">Western Sahara</option>
							<option value="YE">Yemen</option>
							<option value="YU">Yugoslavia</option>
							<option value="ZR">Zaire</option>
							<option value="ZM">Zambia</option>
							<option value="ZW">Zimbabwe</option>
						</select>
					</td>
				</tr>

				<?php if(! $this->use_free): ?>

				<tr>
					<td><?php _e( 'Total Amount', $this->text_domain ); ?>:</td>
					<td>
						<strong><?php echo $_POST['cost']; ?> USD</strong>
						<input type="hidden" name="total_amount" value="<?php echo $_POST['cost']; ?>" />
					</td>
				</tr>

				<tr>
					<td><label for="cc_type"><?php _e( 'Credit Card Type', $this->text_domain ); ?>:</label></td>
					<td>
						<select name="cc_type">
							<option><?php _e( 'Visa', $this->text_domain ); ?></option>
							<option><?php _e( 'MasterCard', $this->text_domain ); ?></option>
							<option><?php _e( 'Amex', $this->text_domain ); ?></option>
							<option><?php _e( 'Discover', $this->text_domain ); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="cc_number"><?php _e( 'Credit Card Number', $this->text_domain ); ?>:</label></td>
					<td><input type="text" name="cc_number" class="required"/></td>
				</tr>
				<tr>
					<td><label for="exp_date"><?php _e( 'Expiration Date (mm/yy)', $this->text_domain ); ?>:</label></td>
					<td><input type="text" name="exp_date" class="required" /></td>
				</tr>
				<tr>
					<td><label for="cvv2"><?php _e( 'CVV2', $this->text_domain ); ?>:</label></td>
					<td><input type="text" name="cvv2" class="required" /></td>
				</tr>
				<?php endif; ?>

			</table>

			<div class="clear"></div>
			<div class="submit">
				<input type="submit" name="direct_payment_submit" value="Continue" />
			</div>

		</form>
		<!-- End CC Details -->




		<?php elseif ( $step == 'confirm_payment' ): ?>
		<!-- Confirm -->
		<form action="" method="post" class="cf-checkout">

			<?php
			if ($_SESSION['payment_method'] == 'paypal'):
			$transaction_details = get_query_var('cf_transaction_details');
			$_POST['email'] = $transaction_details['EMAIL'];
			$_POST['first_name'] = $transaction_details['FIRSTNAME'];
			$_POST['last_name'] = $transaction_details['LASTNAME'];
			$_POST['street'] = $transaction_details['SHIPTOSTREET'];
			$_POST['city'] = $transaction_details['SHIPTOCITY'];
			$_POST['state'] = $transaction_details['SHIPTOSTATE'];
			$_POST['zip'] = $transaction_details['SHIPTOZIP'];
			$_POST['country_code'] = $transaction_details['SHIPTOCOUNTRYNAME'];
			$_POST['total_amount'] = $transaction_details['AMT'];
			$_POST['currancy_code'] = $transaction_details['CURRENCYCODE'];
			endif;
			?>

			<input type="hidden" name="email" value="<?php echo (empty($_POST['email']) ) ? '' : $_POST['email']; ?>" />
			<input type="hidden" name="total_amount" value="<?php echo (empty($_POST['total_amount']) ) ? '' : $_POST['total_amount']; ?>" />
			<input type="hidden" name="cc_number" value="<?php echo (empty($_POST['cc_number']) ) ? '' : $_POST['cc_number']; ?>" />
			<input type="hidden" name="cvv2" value="<?php echo (empty($_POST['cvv2']) ) ? '' : $_POST['cvv2']; ?>" />
			<input type="hidden" name="exp_date" value="<?php echo (empty($_POST['exp_date']) ) ? '' : $_POST['exp_date']; ?>" />
			<input type="hidden" name="first_name" value="<?php echo (empty($_POST['first_name']) ) ? '' : $_POST['first_name']; ?>" />
			<input type="hidden" name="last_name" value="<?php echo (empty($_POST['last_name']) ) ? '' : $_POST['last_name']; ?>" />
			<input type="hidden" name="street" value="<?php echo (empty($_POST['street']) ) ? '' : $_POST['street']; ?>" />
			<input type="hidden" name="city" value="<?php echo (empty($_POST['city']) ) ? '' : $_POST['city']; ?>" />
			<input type="hidden" name="state" value="<?php echo (empty($_POST['state']) ) ? '' : $_POST['state']; ?>" />
			<input type="hidden" name="zip" value="<?php echo (empty($_POST['zip']) ) ? '' : $_POST['zip']; ?>" />
			<input type="hidden" name="country_code" value="<?php echo (empty($_POST['country_code']) ) ? '' : $_POST['country_code']; ?>" />
			<input type="hidden" name="billing" value="<?php echo $_SESSION['billing']; ?>" />
			<input type="hidden" name="credits" value="<?php echo $_SESSION['credits']; ?>" />


			<strong><?php _e( 'Confirm Payment', 'classifieds' ); ?></strong>
			<table>
				<tr>
					<td><label><?php _e( 'Email Address', $this->text_domain ); ?>:</label></td>
					<td><?php echo (empty($_POST['email']) ) ? '' : $_POST['email']; ?></td>
				</tr>
				<tr>
					<td><label><?php _e( 'Name', $this->text_domain ); ?>:</label></td>
					<td><?php echo (empty($_POST['first_name']) ) ? '' : $_POST['first_name']; ?> <?php echo (empty($_POST['last_name']) ) ? '' : $_POST['last_name']; ?></td>
				</tr>
				<tr>
					<td><label><?php _e( 'Address', $this->text_domain ); ?>:</label></td>
					<td><?php echo (empty($_POST['street']) ) ? '' : $_POST['street']; ?>, <?php echo (empty($_POST['city']) ) ? '' : $_POST['city']; ?>, <?php echo (empty($_POST['state']) ) ? '' : $_POST['state']; ?>, <?php echo (empty($_POST['zip']) ) ? '' : $_POST['zip']; ?>, <?php echo (empty($_POST['country_code']) ) ? '' : $_POST['country_code']; ?></td>
					</tr>
					<tr>
						<td><label><?php _e('Total Amount', 'directory'); ?>:</label></td>
						<td>
							<strong><?php echo (empty($_POST['total_amount']) ) ? '' : $_POST['total_amount']; ?> <?php echo (empty($_POST['currancy_code']) ) ? 'USD' : $_POST['currancy_code']; ?></strong>
						</td>
					</tr>
				</table>
				<div class="submit">
					<input type="submit" name="confirm_payment_submit" value="Confirm Payment" />
				</div>

			</form>
			<!--End Confirm-->






				<?php elseif ( $step == 'api_call_error' ): ?>
				<!--Begin Call Error -->

				<ul>
					<li><?php echo $error['error_call'] . ' API call failed.'; ?></li>
					<li><?php echo 'Detailed Error Message: ' . $error['error_long_msg']; ?></li>
					<li><?php echo 'Short Error Message: '    . $error['error_short_msg']; ?></li>
					<li><?php echo 'Error Code: '             . $error['error_code']; ?></li>
					<li><?php echo 'Error Severity Code: '    . $error['error_severity_code']; ?></li>
				</ul>
				<!-- End Call Error-->









				<?php elseif ( $step == 'success' ): ?>
				<!-- Begin Success -->
				<div class="dp-thank-you"><?php _e( 'Thank you for your business. Transaction processed successfully!', $this->text_domain ); ?></div>
				<span class="dp-submit-txt"><?php _e( 'You can go to your profile and review/change your personal information. You can also go straight to classifieds submission page.', $this->text_domain ); ?></span>
				<br /><br />

				<form action="#" method="post">
					<input type="submit" name="redirect_my_classifieds" value="Proceed To Your Classifieds" />
				</form>

				<?php endif; ?>
				<!-- End Success -->
				<div class="clear"></div><br />

				<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>

				<script type="text/javascript">jQuery('.cf-checkout').validate();</script>

