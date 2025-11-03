# Paynow Donations Plugin

The following repository is a wordpress plugin that makes receiving donation with [paynow](https://www.paynow.pl/) easy.  

It implemets two shortcodes ( donation form, return screen ), transaction history ( with sorting and fitering capabilites ) and a settings page for setting your apiKeys.

## Installation

The plugin is not currently avaible on the Wordpress Plugins Directory, to install it:
1. download the newest `paynow-donations.zip` file from this repository
2. go to the ***Plugins*** menu on your Wordpress
3. click the ***Add Plugin*** button
4. click the ***Upload Plugin*** button
5. upload the downloaded file
6. the plugin is now officially installed

## Setup

To set up the plugin you have to be the owner of an active [paynow account](https://www.paynow.pl/jak-aktywowac)

Simply copy the `ApiKey` and `SignatureKey` from your paynow panel into the corresponding field in the **Paynow -> Paynow Settings** menu and save them.

Set the `Environment` setting to `PRODUCTION`

In your paynow panel set up the correct `Shop Domain`  

Set the `Notifications URL` to `https://yourdomain.example/wp-json/paynownotifications/notify`

Set the `Return address` to a site where you'll use the `[paynow_return]` shortcode

## Testing

To set up a testing environment you basically have to follow the Setup above, the only difference is instead of creating a real paynow account, you sign up for a [testing one](https://panel.sandbox.paynow.pl/auth/register)

Make sure to also change the `Environment` setting to `SANDBOX`

> [!WARNING]
> in order to ( properly ) use the sandbox testing your site must be avaible under a public domain ( for notifications purposes )

## Shortcodes

As mentioned before, the plugin implements *two* shortcodes

### [paynow_donation_form]

This shorcodes generates a custom form that enables your site's visitors to make easy donations.

#### attributes

attribute name | default value | explanation
--- | --- | ---
main_text | Donate Here | main text displayed above the form
user_legend | Donate Information | the text displayed as a legend to the users info part of the form
payment_legend | Payment Information | the text displayed as a legend to the users info part of the form
name_label | Name | The text display next to the name field
name_placeholder | Full Name | The placeholder inside the name field
surname_label | Surname | The text displayed next to the surname field
surname_placeholder | Surname | The placeholder inside the surname field
email_label | Email | The text displayed next to the email field
email_placeholder | Email Address | The placeholder inside the email field
description_label | Description | The text displayed next to the description field
description_placeholder | Payment Description | The placeholder inside the description field
amount_label | Amount | The text displayed next to the amount field
amount_placeholder | Amount | The placeholder inside the description field
button_text | Donate | The main text display on the button

#### full shortcode

```
[ paynow_donation_form main_text="Donate Here" user_legend="Donor Information" payment_legend="Payment Information" name_label="Name" name_placeholder="Full Name" surname_label="Surname" surname_placeholder="Surname" email_label="Email" email_placeholder="Email Address" description_label="Description" description_placeholder="Payment Description" amount_label="Amount" amount_placeholder="Amount" button_text="Donate" ]
```

### [paynow_return]

This shortcode generates a dynamic message for the user returning after completing a payment.

#### attributes

attribute name | default value | explanation
--- | --- | ---
button_text | Main Page | Text displayed on the button
button_url | N/A | The url the button points to
success_msg | Thank you for your contribution | The main message displayed after succesfull payment
fail_msg | Something went wrong with your payment | The main message displayed after an unsuccesfull payment
show_id | true | True/False whether to show the Transaction ID to the user
transaction_id | Your transaction ID is | The message after which the Transaction ID is displayed ( if show_id is true )

#### full shortcode

```
[ paynow_return button_text="Main Page" button_url=" success_msg="Thank you for your contribution" fail_msg="Something went wrong with your payment" show_id=true transaction_id_ms="Your transaction ID is" ]
```

## Admin Pages

This plugin creates **3** wordpress menu subpages

### History

This menu is avaible for users with the `Editor` Role ( or higher ). It allows you to track the payments from the perspective of your server.

This site features sorting by all columns and filtering by the following params:
* Statuses ( NEW, PENDING, CONFIRMED, REJECTED, ERROR, EXPIRED, ABANDONED )
* Amount of results ( 25, 50, 75, 100, 1000 )
* Minimum and maximum amount
* Description
* Email
* Name
* Surname
* Transaction ID

### Settings

This menu is avaible only to the `Admin` role. Here you can configure the plugin with the following settings:
* API Key - The API Key copied from the paynow panel
* Signature Key - The Signature Key copied from the paynow panel
* Environment - SANDBOX / PRODUCTION
* Debug - ON / OFF

### Debug

This menu is avaible only to the `Admin` role, but it is hidden by default. To show it, change the `Debug` setting to `ON`.

In this menu you can track *all* of the notifications your server receives from paynow.

This site also features sorting by all columns and filtering by the following params:
* Statuses ( NEW, PENDING, CONFIRMED, REJECTED, ERROR, EXPIRED, ABANDONED )
* Amount of results ( 25, 50, 75, 100, 1000 )
* Transaction ID
* Internal Ref

Where `Internal Ref` is the ID assigned to the transaction by the plugin. 

> [!WARNING]
> When a user retries the payment, `Transaction ID` changes, but the `Internal Ref` stays the same
