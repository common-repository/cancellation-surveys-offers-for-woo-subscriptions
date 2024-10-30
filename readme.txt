=== Cancellation Survey and Offers for Woo Subscriptions ===
Contributors: meowcrew, freemius
Tags: woocommerce subscriptions,subscription management,survey,discounts,subscription
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.0.0
Requires PHP: 7.2
License: GNU General Public License v2
License URI: https://www.gnu.org/licenses/license-list.html#GPLCompatibleLicenses

Increase retention for WooCommerce Subscriptions by offering discounts and collecting feedback with surveys when customers consider cancelling.

== Description ==
Subscriptions can be canceled for many reasons. By learning these reasons through surveys and responding with appropriate offers, you can turn a potential cancellation into a continued subscription.

Cancellation Survey and Offers for WooCommerce Subscriptions helps you keep more subscribers by addressing their concerns when they decide to cancel. This plugin lets you set up surveys to understand why customers are leaving and offers them discounts to encourage them to stay.

## Features
- **Customizable Feedback Forms**: Create surveys to find out reasons why your customers want to stop their subscriptions.
- **Automated Discount Offers**: Automatically give discounts if certain survey answers are chosen.*[premium version]*
- **Segmented Surveys and Offers**: Make different surveys and offers for various types of products and categories as well as user roles or accounts.
- **Subscription Duration Limits**: Limit on whom discounts are offered based on how long the subscription has been active.
- **Discount offers limits**: Control how many times a discount can be used by a customer to prevent overuse.*[premium version]*
- **Detailed Analytics**: Track each cancellation attempt, cancellation reasons, and discount offers performance in the survey dashboard to continually improve your approach.
- **Inherits Coupon Code Capabilities**: Link a specific coupon code that will automatically apply to the user's subscription when they accept the offer.*[premium version]*

The plugin works with WooCommerce's recurring coupon codes to offer discounts when someone tries to cancel their subscription. It includes all the features of recurring coupon codes, such as fixed amount off, percentage off, or discounts for a set number of renewals. Additionally, since the discount system is based on regular WooCommerce coupons, you can enhance your discount strategies by integrating with other plugins that enhance coupon codes (like Smart Coupons). This combination gives you greater control over your discount offers, making it easier to tailor them to specific subscription scenarios.

## How to use
1. **Create cancellation survey** - Design and deploy surveys to understand why customers might cancel their subscriptions. These surveys can be tailored to specific subscriber segments and product types, allowing you to gather precise feedback directly from the source.
2. **Add discount offer to cancellation survey** - Attach specific discount offers (offers are based on coupons) to your surveys that automatically trigger based on certain responses. This allows you to immediately address customer concerns with enticing offers, aiming to convert potential cancellations into retained subscriptions.
3. **Set limits to users, subscriptions, and products** - Set restrictions to control who can receive discount offers and under what conditions. Set parameters based on user roles, subscription duration, and product categories to ensure offers are targeted and effective.
4. **Test surveys and offers** - Make a test purchase of a subscription and attempt to cancel it through the user account. Choose a cancellation reason and check if you receive a discount offer (provided the reason is linked to an offer). If an offer is presented, accept it and verify how the total is adjusted.
5. **Track retention success in dashboard** - Go to Survey Answers in the Surveys & Offers tab, where you can view all cancellation attempts along with the selected reasons, the coupon codes offered, and their success status.

By offering discounts and collecting feedback during the cancellation process, the Cancellation Survey and Offers for WooCommerce Subscriptions plugin helps you address subscriber concerns effectively, encouraging them to reconsider their decision to cancel.


== Installation ==
1. Upload the plugin files to the /wp-content/plugins/cancellation-surveys-offers-woocommerce-subscriptions directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress
3. Go to Offers & Surveys tab > Add Cancellation Offer > Create first Survey and\or Discount offer
4. After setting up first Survey & Offer > Make test purchase of subscription > Go to account - subscription and try canceling subscription

== Frequently Asked Questions ==
= What types of discounts can I offer? =
The discounts are controlled by default WooCommerce coupon codes. You can link any coupon to your offer as long as it's 'recurring' type of coupon, including fixed amount, percentage-based. And use other coupon settings as discounts limited to a number of renewals, etc.

= Are there limitations on how many offers a user can accept? =
You can limit users to accept only 1 offer, or not limit, so they would get discount offers every time they try cancelling subscription.

= Can I customize the survey questions? =
Sure. There are default questions when you create your first survey, but you can delete, add, or edit any more. You can also decide which of the responses are expected to come with additional text field.

= Can I track the effectiveness of offered discounts? =
Yes, the plugin includes a statistics dashboard that lets you track each discount offer's acceptance rate and its impact on subscription retention. This helps you refine your strategies based on real data.

= Is there a way to prevent abuse of the discount offers? =
Yes, you can set limits on users who can accept offers and on the length of the subscription. For example, if a subscription hasn't been renewed three or more times, the user will not receive a discount offer. Similarly, if the subscription has been active for less than X days, no discount will be offered. You can also limit users to receiving a discount offer only once if they have already accepted it.

= How many surveys and offers can I create? =
There are no limitations on the number of surveys; you can create a separate survey for each subscription product. Each survey can include one offer, within which you can specify which survey responses qualify a user to receive a discount offer.

== Screenshots ==
1. Cancellation Survey and Offers for WooCommerce Subscriptions
2. Add survey options for step 1
3. Link coupon code and set up discount for step 2
4. Set subscription requirements for discount offer
5. Select products and user roles for the current survey and offer
6. Create several offers and surveys for different products and users
7. Find survey answers and offers status in the dashboard
8. Survey popup when user tries to cancel subscription
9. Discount offer when user selects one of cancellation reasons
10. Coupon code applied on user subscription in user account
11. Regular WooCommerce coupon setup for subscription

== Changelog ==

2024-07-22 - version 1.0.0
* Initial release