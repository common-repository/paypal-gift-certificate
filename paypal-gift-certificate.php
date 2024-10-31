<?php

/*
Plugin Name: Easy PayPal Gift Certificate
Plugin URI: https://wpplugin.org/easy-paypal-gift-certificate/
Tags: PayPal, gift certificate, gift card, PayPal, button, payment, buy now, ecommerce, pay now, gateway
Description: A simple and easy way to integrate Gift Certificates into your WordPress website.
Author: Scott Paterson
Author URI: https://wpplugin.org
License: GPL2
Version: 1.2.3
*/

/*  Copyright 2014-2015 Scott Paterson

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/








global $pagenow, $typenow;


// add media button for editor page
if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) && $typenow != 'download' ) {



add_action('media_buttons', 'wpppgc_add_my_media_button', 20);
function wpppgc_add_my_media_button() {
    echo '<a href="#TB_inline?width=600&height=400&inlineId=wpppgc_popup_container" title="Easy PayPal Gift Certificate" id="insert-my-media" class="button thickbox">Easy Gift Certificate</a>';
}

add_action( 'admin_footer',  'wpppgc_add_inline_popup_content' );
function wpppgc_add_inline_popup_content() {
?>



<script type="text/javascript">
function wpppgc_InsertShortcode(){

var wpppgc_alignmentc = document.getElementById("wpppgc_alignment");
var wpppgc_alignmentb = wpppgc_alignmentc.options[wpppgc_alignmentc.selectedIndex].value;

if(wpppgc_alignmentb == "none") { var wpppgc_alignment = ""; } else { var wpppgc_alignment = ' align="' + wpppgc_alignmentb + '"'; };

wpppgc_alignmentc.selectedIndex = null;

window.send_to_editor('[wpppgc' + wpppgc_alignment + ']');
}
</script>


<div id="wpppgc_popup_container" style="display:none;">

<h2>Insert a Gift Certificate Button</h2>

<table><tr><td>

Alignment: </td><td><select name="wpppgc_alignment" id="wpppgc_alignment"><option value="none"></option><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select> 
Optional</td><td></td></tr><tr><td>

</td></tr><tr><td>

<br />
</td></tr><tr><td>

<input type="button" id="wpppgc-insert" class="button-primary" onclick="wpppgc_InsertShortcode();" value="Insert">
<br /><br />

</td></tr></table>

</div>
<?php
}
}



// plugin functions


wpppgc_wpppgc::init_wpppgcfn();

class wpppgc_wpppgc {
public static function init_wpppgcfn() {
register_deactivation_hook( __FILE__, array( __CLASS__, "wpppgc_deactivate" ));
register_uninstall_hook( __FILE__, array( __CLASS__, "wpppgc_uninstall" ));


$wpppgc_settingsoptions = array(
'currency'    => '25',
'language'    => '3',
'liveaccount'    => '',
'sandboxaccount'    => '',
'mode'    => '2',
'size'    => '2',
'opens'    => '2',
'cancel'    => '',
'return'    => '',
'note'    => '1',
'fixed'    => '',
'redeem'    => '',
'min'    => '5.00',
'max'    => '1,000',
'image_1'    => '',
'image_2'    => ''
);


add_option("wpppgc_settingsoptions", $wpppgc_settingsoptions);
}
function wpppgc_deactivate() {
delete_option("wpppgc_my_plugin_notice_shown");
}
function wpppgc_uninstall() {
}
}







// display activation notice

add_action('admin_notices', 'wpppgc_my_plugin_admin_notices');

function wpppgc_my_plugin_admin_notices() {
if (!get_option('wpppgc_my_plugin_notice_shown')) {
echo "<div class='updated'><p><a href='admin.php?page=paypal-gift-certificate-settings'>Click here to view the plugin settings</a>.</p></div>";
update_option("wpppgc_my_plugin_notice_shown", "true");
}
}






// settings page menu link
add_action( "admin_menu", "wpppgc_plugin_menu" );

function wpppgc_plugin_menu() {
add_options_page( "Easy PayPal GC", "Easy PayPal GC", "manage_options", "paypal-gift-certificate-settings", "wpppgc_plugin_options" );
}
add_filter('plugin_action_links', 'wpppgc_myplugin_plugin_action_links', 10, 2);

function wpppgc_myplugin_plugin_action_links($links, $file) {
static $this_plugin;
if (!$this_plugin) {
$this_plugin = plugin_basename(__FILE__);
}
if ($file == $this_plugin) {
$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=paypal-gift-certificate-settings">Settings</a>';
array_unshift($links, $settings_link);
}
return $links;
}



function wpppgc_plugin_settings_link($links)
{
unset($links['edit']);

$forum_link   = '<a target="_blank" href="https://wordpress.org/support/plugin/paypal-gift-certificate">' . __('Support', 'PTP_LOC') . '</a>';
$premium_link = '<a target="_blank" href="https://wpplugin.org/downloads/easy-paypal-gift-certificate/">' . __('Purchase Premium', 'PTP_LOC') . '</a>';
array_push($links, $forum_link);
array_push($links, $premium_link);
return $links; 
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wpppgc_plugin_settings_link' );





function wpppgc_plugin_options() {
if ( !current_user_can( "manage_options" ) )  {
wp_die( __( "You do not have sufficient permissions to access this page." ) );
}










// settings page

echo "<table width='100%'><tr><td width='70%'><br />";
echo "<label style='color: #000;font-size:18pt;'><center>Easy PayPal Gift Certificate Settings</center></label>";
echo "<form method='post' action='".$_SERVER["REQUEST_URI"]."'>";



// save and update options
if (isset($_POST['update'])) {

$options['currency'] = $_POST['currency'];
$options['language'] = $_POST['language'];
$options['liveaccount'] = $_POST['liveaccount'];
$options['sandboxaccount'] = $_POST['sandboxaccount'];
$options['mode'] = $_POST['mode'];
$options['size'] = $_POST['size'];
$options['opens'] = $_POST['opens'];
$options['cancel'] = $_POST['cancel'];
$options['return'] = $_POST['return'];
$options['min'] = $_POST['min'];
$options['max'] = $_POST['max'];
$options['redeem'] = $_POST['redeem'];


update_option("wpppgc_settingsoptions", $options);

echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";

}


// get options for settings page
$options = get_option('wpppgc_settingsoptions');
foreach ($options as $k => $v ) { $value[$k] = $v; }


echo "</td><td></td></tr><tr><td>";





// form
echo "<br />";
?>

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Usage
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<b>Adding Buttons - Automatic Method: </b><br />
In a page or post editor you will see a new button called "Easy Gift Certificate" located right above the text area beside the Add Media button. By using this you can automatically 
create shortcodes for Gift Certificate buttons.

<br /><br />
<b>Adding Buttons - Manual Method: </b><br />

If you want to place a Gfit Certificate button on your site, use <b>[wpppgc]</b>.

<br /><br />
<b>Note: </b> There is no limit to the amount of times you can place an add to cart or view cart button in a post or a page.

<br /><br /><br />

<b>Customers Redeeming: </b><br />
In order for your customers to redeem their gift certificiate, you will need to have PayPal integerated into your site. Either via a button, checkout, or shopping cart. If you don't already have PayPal on your site yet, 
take a look at my other plugin <a target='top' href="plugin-install.php?tab=plugin-information&plugin=wp-ecommerce-paypal&TB_iframe=true&width=600&height=550">Easy PayPal Button</a> where you can place a PayPal button
anywhere you need it. Once the customer goes to purchse your product, there will be a field where they can enter their Gift Certificate code.

<br /><br />
Please note that PayPal requires that the Gift Certificates purchased from your site be used on your site. PayPal knows the URL of your site from the "Gift Certificate Redeem URL" option below. Please keep this in
mind if you ever plan on changing the domain name or your site.


<br /><br /></div>
<br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Gift Certificate Preview
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<?php

if (empty($value['color'])) { $color = ""; } else { $color = $value['color']; }

if (empty($value['theme'])) { $theme = ""; } else { $theme = $value['theme']; }

if (empty($value['image_2'])) { $image_2 = ""; } else { $image_2 = $value['image_2']; }

?>


<a target='_blank' href='https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_run-printable-gc&code=oegc&style_color=<?php echo $color ?>&style_theme=<?php echo $theme; ?>&logo_image_custom=<?php echo $image_2; ?>&url_custom=<?php echo $value['redeem']; ?>'>Click here to preview what the gift certificate will look like.</a>

<br /><br />

Please save any changes you have made to refresh the preview. Note that this is only an example, the Certificiate amount and date is not updated and the code is not real.

<br /><br /></div>
<br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Language & Currency
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<b>Language:</b>
<select name="language">
<option <?php if ($value['language'] == "1") { echo "SELECTED"; } ?> value="1">Danish</option>
<option <?php if ($value['language'] == "2") { echo "SELECTED"; } ?> value="2">Dutch</option>
<option <?php if ($value['language'] == "3") { echo "SELECTED"; } ?> value="3">English</option>
<option <?php if ($value['language'] == "4") { echo "SELECTED"; } ?> value="4">French</option>
<option <?php if ($value['language'] == "5") { echo "SELECTED"; } ?> value="5">German</option>
<option <?php if ($value['language'] == "6") { echo "SELECTED"; } ?> value="6">Hebrew</option>
<option <?php if ($value['language'] == "7") { echo "SELECTED"; } ?> value="7">Italian</option>
<option <?php if ($value['language'] == "8") { echo "SELECTED"; } ?> value="8">Japanese</option>
<option <?php if ($value['language'] == "9") { echo "SELECTED"; } ?> value="9">Norwgian</option>
<option <?php if ($value['language'] == "10") { echo "SELECTED"; } ?> value="10">Polish</option>
<option <?php if ($value['language'] == "11") { echo "SELECTED"; } ?> value="11">Portuguese</option>
<option <?php if ($value['language'] == "12") { echo "SELECTED"; } ?> value="12">Russian</option>
<option <?php if ($value['language'] == "13") { echo "SELECTED"; } ?> value="13">Spanish</option>
<option <?php if ($value['language'] == "14") { echo "SELECTED"; } ?> value="14">Swedish</option>
<option <?php if ($value['language'] == "15") { echo "SELECTED"; } ?> value="15">Simplified Chinese -China only</option>
<option <?php if ($value['language'] == "16") { echo "SELECTED"; } ?> value="16">Traditional Chinese - Hong Kong only</option>
<option <?php if ($value['language'] == "17") { echo "SELECTED"; } ?> value="17">Traditional Chinese - Taiwan only</option>
<option <?php if ($value['language'] == "18") { echo "SELECTED"; } ?> value="18">Turkish</option>
<option <?php if ($value['language'] == "19") { echo "SELECTED"; } ?> value="19">Thai</option>
</select>

PayPal currently supports 18 languages.
<br /><br />


<b>Currency:</b> 
<select name="currency">
<option <?php if ($value['currency'] == "1") { echo "SELECTED"; } ?> value="1">Australian Dollar - AUD</option>
<option <?php if ($value['currency'] == "2") { echo "SELECTED"; } ?> value="2">Brazilian Real - BRL</option> 
<option <?php if ($value['currency'] == "3") { echo "SELECTED"; } ?> value="3">Canadian Dollar - CAD</option>
<option <?php if ($value['currency'] == "4") { echo "SELECTED"; } ?> value="4">Czech Koruna - CZK</option>
<option <?php if ($value['currency'] == "5") { echo "SELECTED"; } ?> value="5">Danish Krone - DKK</option>
<option <?php if ($value['currency'] == "6") { echo "SELECTED"; } ?> value="6">Euro - EUR</option>
<option <?php if ($value['currency'] == "7") { echo "SELECTED"; } ?> value="7">Hong Kong Dollar - HKD</option> 	 
<option <?php if ($value['currency'] == "8") { echo "SELECTED"; } ?> value="8">Hungarian Forint - HUF</option>
<option <?php if ($value['currency'] == "9") { echo "SELECTED"; } ?> value="9">Israeli New Sheqel - ILS</option>
<option <?php if ($value['currency'] == "10") { echo "SELECTED"; } ?> value="10">Japanese Yen - JPY</option>
<option <?php if ($value['currency'] == "11") { echo "SELECTED"; } ?> value="11">Malaysian Ringgit - MYR</option>
<option <?php if ($value['currency'] == "12") { echo "SELECTED"; } ?> value="12">Mexican Peso - MXN</option>
<option <?php if ($value['currency'] == "13") { echo "SELECTED"; } ?> value="13">Norwegian Krone - NOK</option>
<option <?php if ($value['currency'] == "14") { echo "SELECTED"; } ?> value="14">New Zealand Dollar - NZD</option>
<option <?php if ($value['currency'] == "15") { echo "SELECTED"; } ?> value="15">Philippine Peso - PHP</option>
<option <?php if ($value['currency'] == "16") { echo "SELECTED"; } ?> value="16">Polish Zloty - PLN</option>
<option <?php if ($value['currency'] == "17") { echo "SELECTED"; } ?> value="17">Pound Sterling - GBP</option>
<option <?php if ($value['currency'] == "18") { echo "SELECTED"; } ?> value="18">Russian Ruble - RUB</option>
<option <?php if ($value['currency'] == "19") { echo "SELECTED"; } ?> value="19">Singapore Dollar - SGD</option>
<option <?php if ($value['currency'] == "20") { echo "SELECTED"; } ?> value="20">Swedish Krona - SEK</option>
<option <?php if ($value['currency'] == "21") { echo "SELECTED"; } ?> value="21">Swiss Franc - CHF</option>
<option <?php if ($value['currency'] == "22") { echo "SELECTED"; } ?> value="22">Taiwan New Dollar - TWD</option>
<option <?php if ($value['currency'] == "23") { echo "SELECTED"; } ?> value="23">Thai Baht - THB</option>
<option <?php if ($value['currency'] == "24") { echo "SELECTED"; } ?> value="24">Turkish Lira - TRY</option>
<option <?php if ($value['currency'] == "25") { echo "SELECTED"; } ?> value="25">U.S. Dollar - USD</option>
</select>
PayPal currently supports 25 currencies.

<br /><br />
</div>


<br /><br /><div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; PayPal Account </div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<?php

echo "<b>Live Account: </b><input type='text' name='liveaccount' value='".$value['liveaccount']."'> Required";
echo "<br />Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account.";
echo "<br /><br />You can find your Merchant account ID in your PayPal account under Profile -> My business info -> Merchant account ID";

echo "<br /><br />If you don't have a PayPal account, you can sign up for free at <a target='_blank' href='https://paypal.com'>PayPal</a>. <br /><br />";

echo "<br />";

echo "<b>Sandbox Account: </b><input type='text' name='sandboxaccount' value='".$value['sandboxaccount']."'> Optional";
echo "<br />Enter a valid sandbox PayPal account email address. A Sandbox account is a fake account used for testing. This is useful to make sure your PayPal account and settings are working properly being going live.";
echo "<br /><br />If you don't have a PayPal developer account, you can sign up for free at the <a target='_blank' href='https://developer.paypal.com/developer'>PayPal Developer</a> site. <br /><br />";

echo "<br />";

echo "<b>Sandbox Mode:</b>";
echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "1") { echo "checked='checked'"; } echo " type='radio' name='mode' value='1'>On (Sandbox mode)";
echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "2") { echo "checked='checked'"; } echo " type='radio' name='mode' value='2'>Off (Live mode)";

echo "<br /><br /></div>";



?>
<br /><br />
<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Other Settings
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />
<?php

echo "<table cellpadding='8'><tr><td valign='top'>";



echo "<b>Button Size and type:</b></td><td valign='top' style='text-align: center;'>";
echo "<input "; if ($value['size'] == "1") { echo "checked='checked'"; } echo " type='radio' name='size' value='1'>Small <br /><img src='https://www.paypalobjects.com/en_US/i/btn/btn_gift_SM.gif'></td><td valign='top' style='text-align: center;'>";
echo "<input "; if ($value['size'] == "2") { echo "checked='checked'"; } echo " type='radio' name='size' value='2'>Big <br /><img src='https://www.paypalobjects.com/en_US/i/btn/btn_gift_LG.gif'></td><td valign='top' style='text-align: center;'>";
echo "<input "; if ($value['size'] == "3") { echo "checked='checked'"; } echo " type='radio' name='size' value='3'>Big with credit cards <br /><img src='https://www.paypalobjects.com/en_US/i/btn/btn_giftCC_LG.gif'></td><td valign='top' style='text-align: center;'>";



echo "</td></tr><tr><td><b>PayPal opens in:</b></td>";
echo "<td><input "; if ($value['opens'] == "1") { echo "checked='checked'"; } echo " type='radio' name='opens' value='1'>Same page</td>";
echo "<td><input "; if ($value['opens'] == "2") { echo "checked='checked'"; } echo " type='radio' name='opens' value='2'>New page</td></tr>";

echo "</table><br /><br />";

echo "<br/ >";




echo "<b>Minimum Gift Card Amount: </b>";
echo "<input type='text' size='10' name='min' value='".$value['min']."'> Optional <br />";
echo "The minimum amount that someone buy a gift certificate for. <br /><br />

This value is auto grouped by PayPal in increments up to the maximum value amount; 
so if you enter a mininum of $1 and a maximum of $30 the customer will see $5, $10, $25 as their only options.
<br /><br /> 
PayPal currently does not support the ability for a customer to enter their own amount.<br /><br />";

echo "<br />";

echo "<b>Maximum Gift Card Amount: </b>";
echo "<input type='text' size='10' name='max' value='".$value['max']."'> Optional <br />";
echo "The maximum amount that someone buy a gift certificate for. <br /><br />";

echo "<br />";



$siteurl = get_site_url();


echo "<b>Gift Certificate Redeem URL: </b>";
echo "<input type='text' size='40' name='redeem' value='".$value['redeem']."'> Required <br />";
echo "If the customer goes to PayPal and purchaces a gift certificate, where does the gift certificate recepiant go to spend it. <br /><b> We strongly recommend you use only your domain name. </b> Example: $siteurl <br /><br />
 If you are selling it for an ebay shop, use the format: http://stores.ebay.com/shopname<br /><br /> Max length: 1,024 characters. <br /><br />";

echo "<br />";

echo "<b>Cancel URL: </b>";
echo "<input type='text' size='40' name='cancel' value='".$value['cancel']."'> Optional <br />";
echo "If the customer goes to PayPal and clicks the cancel button, where do they go. <br /> Example: $siteurl/cancel <br /><br /> Max length: 1,024 characters. <br /><br />";

echo "<br />";

echo "<b>Return URL: </b>";
echo "<input type='text' size='40' name='return' value='".$value['return']."'> Optional <br />";
echo "If the customer goes to PayPal and successfully pays, where are they redirected to after. <br /> Example: $siteurl/thankyou <br /><br /> Max length: 1,024 characters. <br /><br />";

?>
</div>


<input type='hidden' name='update'><br />
<input type='submit' name='btn2' class='button-primary' value='Save Settings'>

<br /><br />

<br /><br /><br />


WPPlugin is an offical PayPal Partner. Various trademarks held by their respective owners.


</form>














</td><td width='5%'>
</td><td width='24%' valign='top'>

<br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Professional Version
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;">


<center><label style="font-size:14pt;">With the Pro version you'll <br /> be able to: </label></center>
 
<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Charge a fixed amount <br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Display your company logo<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Change theme / color<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div> Add a custom button image<br />



<br />
<center><a target='_blank' href="https://wpplugin.org/downloads/easy-paypal-gift-certificate/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Upgrade Now</a></center>
<br />
</div>



<br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Premium Support
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;"><br />

<center><label style="font-size:14pt;">Get Personalized Support </label></center>
 
<br />
Even with the free version you still get Premum Support.

<br /><br />
<center><a target='_blank' href="https://wpplugin.org/email-support/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Learn More</a></center>
<br />


</div>

<br /><br />



<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Quick Links
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;"><br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wordpress.org/support/plugin/paypal-gift-certificate">Support Forum</a> <br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/easy-paypal-gift-certificate-support/">FAQ</a> <br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/downloads/easy-paypal-gift-certificate/">Easy PayPal Gift Certificate Pro</a> <br /><br />

</div>

<br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Like this Plugin?
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;"><br />

<center><a target='_blank' href="https://wordpress.org/plugins/paypal-gift-certificate/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Leave a Review</a></center>
<br />
<center><a target='_blank' href="https://wpplugin.org/donate" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Donate</a></center>
<br />

</div>



</td><td width='1%'>

</td></tr></table>


<?php

// end settings page and required permissions
}


















// shortcode

add_shortcode('wpppgc', 'wpppgc_options');


function wpppgc_options($atts) {

// get shortcode user fields
$atts = shortcode_atts(array('name' => 'Example Name','price' => '0.00','size' => '','align' => ''), $atts);

// get settings page values
$options = get_option('wpppgc_settingsoptions');
foreach ($options as $k => $v ) { $value[$k] = $v; }


// live of test mode
if ($value['mode'] == "1") {
$account = $value['sandboxaccount'];
$path = "sandbox.paypal";
} elseif ($value['mode'] == "2")  {
$account = $value['liveaccount'];
$path = "paypal";
}

// currency
if ($value['currency'] == "1") { $currency = "AUD"; }
if ($value['currency'] == "2") { $currency = "BRL"; }
if ($value['currency'] == "3") { $currency = "CAD"; }
if ($value['currency'] == "4") { $currency = "CZK"; }
if ($value['currency'] == "5") { $currency = "DKK"; }
if ($value['currency'] == "6") { $currency = "EUR"; }
if ($value['currency'] == "7") { $currency = "HKD"; }
if ($value['currency'] == "8") { $currency = "HUF"; }
if ($value['currency'] == "9") { $currency = "ILS"; }
if ($value['currency'] == "10") { $currency = "JPY"; }
if ($value['currency'] == "11") { $currency = "MYR"; }
if ($value['currency'] == "12") { $currency = "MXN"; }
if ($value['currency'] == "13") { $currency = "NOK"; }
if ($value['currency'] == "14") { $currency = "NZD"; }
if ($value['currency'] == "15") { $currency = "PHP"; }
if ($value['currency'] == "16") { $currency = "PLN"; }
if ($value['currency'] == "17") { $currency = "GBP"; }
if ($value['currency'] == "18") { $currency = "RUB"; }
if ($value['currency'] == "19") { $currency = "SGD"; }
if ($value['currency'] == "20") { $currency = "SEK"; }
if ($value['currency'] == "21") { $currency = "CHF"; }
if ($value['currency'] == "22") { $currency = "TWD"; }
if ($value['currency'] == "23") { $currency = "THB"; }
if ($value['currency'] == "24") { $currency = "TRY"; }
if ($value['currency'] == "25") { $currency = "USD"; }

// language
if ($value['language'] == "1") {
$language = "da_DK";
$image = "https://www.paypalobjects.com/da_DK/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/da_DK/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/da_DK/DK/i/btn/btn_giftCC_LG.gif";
} //Danish

if ($value['language'] == "2") {
$language = "nl_BE";
$image = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_giftCC_LG.gif";
} //Dutch

if ($value['language'] == "3") {
$language = "US_EN";
$image = "https://www.paypalobjects.com/en_US/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/en_US/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/en_US/i/btn/btn_giftCC_LG.gif";
} //English

if ($value['language'] == "4") {
$language = "fr_CA";
$image = "https://www.paypalobjects.com/fr_CA/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/fr_CA/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/fr_CA/i/btn/btn_giftCC_LG.gif";
} //French

if ($value['language'] == "5") {
$language = "de_DE";
$image = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/de_DE/DE/i/btn/btn_giftCC_LG.gif";
} //German

if ($value['language'] == "6") {
$language = "he_IL";
$image = "https://www.paypalobjects.com/he_IL/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/he_IL/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/he_IL/IL/i/btn/btn_giftCC_LG.gif";
} //Hebrew

if ($value['language'] == "7") {
$language = "it_IT";
$image = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/it_IT/IT/i/btn/btn_giftCC_LG.gif";
} //Italian

if ($value['language'] == "8") {
$language = "ja_JP";
$image = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_giftCC_LG.gif";
} //Japanese

if ($value['language'] == "9") {
$language = "no_NO";
$image = "https://www.paypalobjects.com/no_NO/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/no_NO/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/no_NO/NO/i/btn/btn_giftCC_LG.gif";
} //Norwgian

if ($value['language'] == "10") {
$language = "pl_PL";
$image = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/pl_PL/PL/i/btn/btn_giftCC_LG.gif";
} //Polish

if ($value['language'] == "11") {
$language = "pt_BR";
$image = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/pt_PT/PT/i/btn/btn_giftCC_LG.gif";
} //Portuguese

if ($value['language'] == "12") {
$language = "ru_RU";
$image = "https://www.paypalobjects.com/ru_RU/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/ru_RU/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/ru_RU/RU/i/btn/btn_giftCC_LG.gif";
} //Russian

if ($value['language'] == "13") {
$language = "es_ES";
$image = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/es_ES/ES/i/btn/btn_giftCC_LG.gif";
} //Spanish

if ($value['language'] == "14") {
$language = "sv_SE";
$image = "https://www.paypalobjects.com/sv_SE/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/sv_SE/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/sv_SE/SE/i/btn/btn_giftCC_LG.gif";
} //Swedish

if ($value['language'] == "15") {
$language = "zh_CN";
$image = "https://www.paypalobjects.com/zh_XC/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/zh_XC/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/zh_XC/C2/i/btn/btn_giftCC_LG.gif";
} //Simplified Chinese - China

if ($value['language'] == "16") {
$language = "zh_HK";
$image = "https://www.paypalobjects.com/zh_HK/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/zh_HK/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/zh_HK/HK/i/btn/btn_giftCC_LG.gif";
} //Traditional Chinese - Hong Kong

if ($value['language'] == "17") {
$language = "zh_TW";
$image = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/zh_TW/TW/i/btn/btn_giftCC_LG.gif";
} //Traditional Chinese - Taiwan

if ($value['language'] == "18") {
$language = "tr_TR";
$image = "https://www.paypalobjects.com/tr_TR/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/tr_TR/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/tr_TR/TR/i/btn/btn_giftCC_LG.gif";
} //Turkish

if ($value['language'] == "19") {
$language = "th_TH";
$image = "https://www.paypalobjects.com/th_TH/i/btn/btn_gift_SM.gif";
$imageb = "https://www.paypalobjects.com/th_TH/i/btn/btn_gift_LG.gif";
$imagecc = "https://www.paypalobjects.com/th_TH/TH/i/btn/btn_giftCC_LG.gif";
} //Thai

if (!empty($atts['size'])) {
if ($atts['size'] == "1") { $img = $image; }
if ($atts['size'] == "2") { $img = $imageb; }
if ($atts['size'] == "3") { $img = $imagecc; }
} else {
if ($value['size'] == "1") { $img = $image; }
if ($value['size'] == "2") { $img = $imageb; }
if ($value['size'] == "3") { $img = $imagecc; }
if ($value['size'] == "4") { $img = $value['image_1']; }
}

// window action
if ($value['opens'] == "1") { $target = ""; }
if ($value['opens'] == "2") { $target = "_blank"; }

// alignment
if ($atts['align'] == "left") { $alignment = "style='float: left;'"; }
if ($atts['align'] == "right") { $alignment = "style='float: right;'"; }
if ($atts['align'] == "center") { $alignment = "style='margin-left: auto;margin-right: auto;width:179px'"; }
if (empty($atts['align'])) { $alignment = ""; }

$output = "";
$output .= "<div $alignment>";
$output .= "<form target='$target' action='https://www.$path.com/cgi-bin/webscr' method='post'>";
$output .= "<input type='hidden' name='cmd' value='_oe-gift-certificate'>";
$output .= "<input type='hidden' name='business' value='$account' />";
$output .= "<input type='hidden' name='currency_code' value='$currency' />";
$output .= "<input type='hidden' name='no_shipping' value='0'>";
$output .= "<input type='hidden' name='lc' value='$language'>";
$output .= "<input type='hidden' name='min_denom' value='". $value['min'] ."'>";
$output .= "<input type='hidden' name='max_denom' value='". $value['max'] ."'>";
$output .= "<input type='hidden' name='shopping_url' value='". $value['redeem'] ."'>";
$output .= "<input type='hidden' name='return' value='". $value['return'] ."' />";
$output .= "<input type='hidden' name='bn' value='WPPlugin_SP'>";
$output .= "<input type='hidden' name='cancel_return' value='". $value['cancel'] ."' />";
$output .= "<input type='image' style='border: none;' src='$img' border='0' name='submit' alt='Make your payments with PayPal. It is free, secure, effective.'>";
$output .= "<img class='paypalgiftbutton' alt='' border='0' style='border:none;display:none;' src='https://www.paypal.com/$language/i/scr/pixel.gif' width='1' height='1'>";
$output .= "</form></div>";

return $output;

}




?>