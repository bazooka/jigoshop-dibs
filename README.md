# DIBS Payment Window for Jigoshop

Allows you to use [DIBS payment window](http://dibspayment.com) with the [Jigoshop ecommerce plugin](http://jigoshop.com).

## Installation

1. Put the plugin in the directory /wp-content/plugins/jigoshop-dibs/
1. Make sure you have enabled Permalinks for Wordpress posts
1. Enable the Jigoshop DIBS plugin
1. Enter your DIBS Merchant id under Jigoshop -> Settings -> Payment Gateways -> DIBS
1. Ready to use!
1. For added security, also enter your DIBS MAC Key under Settings

## Settings

**Enable DIBS Payment Window**
Check this to enable DIBS payment gateway. If disabled, DIBS is entirely hidden for your customers.

**Method title**
Enter the name for the DIBS Payment Window shown to your customers.

**Description**
Enter the description for the DIBS Gateway shown to your customers.

**DIBS Merchant ID**
Your DIBS merchant ID is also your account number at DIBS and the username used to log in to their administration. Required in order to take payment!

**DIBS MAC Key**
This is a secret unique alphanumeric key that is used to verify communications with DIBS. You can generate this key in the DIBS administration interface. It is recommended to use this, but payments will work without it.

**Enable test mode**
Check this box to enable DIBS test mode. Only their own test-cards will be accepted, and all transactions will be clearly marked "TEST" in the DIBS administration interface. [DIBS list of test cards](http://tech.dibspayment.com/10_step_guide/your_own_test/)

**Language**
Select the language to use for DIBS Payment Window. 

If set to WPML detect, it automatically chooses one of the supported languages listed matching your site language. If no matching language is found it defaults to US English.