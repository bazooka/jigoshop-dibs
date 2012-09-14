# DIBS Payment Window for Jigoshop

Integrates the 2012 [DIBS Payment Window](http://dibspayment.com) with the [Jigoshop eCommerce plugin](http://jigoshop.com).

Curious about what DIBS Payment Window 2012 looks like? [Watch a video!](http://www.youtube.com/watch?v=ttlZTwuvc4Q)

## Quick start
To start taking payments with DIBS you need to enable the payment gateway under Jigoshop settings and also enter your DIBS Merchant ID. Then you're good to go, but remember to test your shop before releasing it to the public.

## Beta software!
This plugin has not yet been tested in production and should be used with caution, but you are welcome to try it out.

## Need help?
This plugin is built and maintained by the digital agency [Bazooka](http://bazooka.se). 

If you encounter any problems, please report them as issues on GitHub and we'll fix them as soon as we can. 

If you need help building an awesome webshop with Jigoshop or any other custom solution, please contact us at info@bazooka.se.

## Installation

1. Ensure you have a working Jigoshop install.
2. Make sure you have enabled Permalinks for Wordpress posts.
3. Put the DIBS plugin with your other plugins: /wp-content/plugins/jigoshop-dibs/
4. Enable the Jigoshop DIBS plugin.
5. Enable the gateway and enter your DIBS Merchant id under Jigoshop -> Settings -> Payment Gateways -> DIBS
6. Your customers can now use DIBS at checkout. (Remember to test it yourself)
7. For added security, you can also enter your DIBS MAC Key under Settings.

## Settings
You'll find these settings in Wordpress Admin under Jigoshop -> Settings -> Payment Gateways.

### Enable DIBS Payment Window
Check this to enable DIBS payment gateway. If disabled, DIBS is entirely hidden for your customers.

### Method title
Enter the name for the DIBS Payment Window shown to your customers.

### Description
Enter the description for the DIBS Gateway shown to your customers.

### DIBS Merchant ID
Your DIBS merchant ID is also your account number at DIBS and the username used to log in to their administration. Required in order to take payment!

### DIBS MAC Key
This is a secret unique alphanumeric key that is used to verify communications with DIBS. You can generate this key in the DIBS administration interface. It is recommended to use this, but payments will work without it.

### Enable test mode
Check this box to enable DIBS test mode. Only their own dummy cards will be accepted, and all transactions will be clearly marked "TEST" in the DIBS administration interface. [Find your DIBS test card](http://tech.dibspayment.com/10_step_guide/your_own_test/)

### Language
Select the language to use for DIBS Payment Window. 

If set to WPML detect, it automatically chooses one of the supported languages listed matching your site language. If no matching language is found it defaults to US English. (Requires the WPML plugin)