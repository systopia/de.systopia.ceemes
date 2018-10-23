# CEEMES API

API extension for portrix.net's CEEMES framework.

## Features

Currently, there is an API for Newsletter subscription submissions to be
registered as group memberships in CiviCRM.

There is a dependency on the
[Extended Contact Matcher (XCM) extension](https://github.com/systopia/de.systopia.xcm)
to match existing contacts or create new ones.

### CeemesSubscription.Submit API action

This API action registers Newsletter subscriptions as group memberships of
contacts.

* Entity: `CeemesSubscription`
* action: `Submit`

The action accepts the following parameters:

| Parameter    | Type    | Description                      | Values/Format                                       | Required                                   | Notes                                                            |
|--------------|---------|----------------------------------|-----------------------------------------------------|--------------------------------------------|------------------------------------------------------------------|
| <nobr>`idx`</nobr>        | Integer | CEEMES subscriber ID             | unsigned                                            | Yes                                        | Mapped to a custom field `ceemes_id`                             |
| <nobr>`cgid`</nobr>       | Integer | CEEMES newsletter group ID       | unsigned                                            | Yes                                        | CiviCRM Group ID as defined as a PHP constant in the code        |
| <nobr>`cgname`</nobr>     | String  | CEEMES newletter group name      |                                                     |                                            | Not processed.                                                   |
| <nobr>`email`</nobr>      | String  | CEEMES subscriber e-mail address | valid e-mail address                                | Yes, or one of `first_name` and `lastname` | E-mail address of the CiviCRM contact                            |
| <nobr>`firstname`</nobr>  | String  | CEEMES subscriber first name     |                                                     | Yes, or one of `email` and `lastname`      | First name of the CiviCRM contact                                |
| <nobr>`lastname`</nobr>   | String  | CEEMES subscriber last name      |                                                     | Yes, or one of `email` and `firstname`     | Last name of the CiviCRM contact                                 |
| <nobr>`greeting`</nobr>   | String  | CEEMES subscriber greeting       | one of `Sehr geehrte Frau` and `Sehr geehrter Herr` |                                            | Gender and greeting will be derived from a mapping               |
| <nobr>`subscribed`</nobr> | String  | CEEMES subscription status       | one of `t` (subscribed) and `f` (unsubscribed)      | Yes                                        | Mapped to CiviCRM group membership status (`Added` or `Removed`) |
