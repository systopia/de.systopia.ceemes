<?php
/*----------------------------------------------------+
| SYSTOPIA Ceemes Newsletter tool Integration         |
| Copyright (C) 2017 SYSTOPIA                         |
| Author: B. Endres (endres@systopia.de)              |
|         J. Schuppe (schuppe@systopia.de)            |
+-----------------------------------------------------+
| This program is released as free software under the |
| Affero GPL license. You can redistribute it and/or  |
| modify it under the terms of this license which you |
| can read by viewing the included agpl.txt or online |
| at www.gnu.org/licenses/agpl.html. Removal of this  |
| copyright header is strictly prohibited without     |
| written permission from the original author(s).     |
+-----------------------------------------------------*/

/**
 * API callback for "submit" call on "CeemesSubscription" entity.
 *
 * @param $params
 *
 * @return array
 */
function civicrm_api3_ceemes_subscription_submit($params) {
  try {
    // Translate parameters into CiviCRM field names.
    foreach (array(
               'firstname' => 'first_name',
               'lastname' => 'last_name',
               'idx' => 'external_identifier',
             ) as $parameter => $field_name) {
      $params[$field_name] = $params[$parameter];
      unset($params[$parameter]);
    }

    // Determine gender from the given greeting.
    if (!empty($params['greeting'])) {
      $gender_options = civicrm_api3('OptionValue', 'get', array('option_group_id' => 'gender'));
      $genders = array();
      foreach ($gender_options['values'] as $gender_option) {
        $genders[$gender_option['value']] = $gender_option['name'];
      }
      switch ($params['greeting']) {
        case 'Sehr geehrter Herr':
          $params['gender_id'] = array_search('Male', $genders);
          break;
        case 'Sehr geehrte Frau':
          $params['gender_id'] = array_search('Female', $genders);
          break;
        default:
          throw new CiviCRM_API3_Exception('Could not determine gender from the given greeting.', 0);
          break;
      }
    }

    // Find or create contact using XCM.
    $contact_data = array_intersect_key($params, array_flip(array(
      'external_identifier',
      'email',
      'first_name',
      'last_name',
      'gender_id',
    )));
    $contact_result = civicrm_api3('Contact', 'getorcreate', $contact_data);
    if ($contact_result['count'] == 1) {
      $contact_id = $contact_result['id'];
    }
    else {
      throw new CiviCRM_API3_Exception('Could not find a distinct contact for the given contact data.', 0);
    }

    // Add group membership with subscribed status.
    switch ($params['cgid']) {
      case 55:
        $group_id = 2;
        break;
      default:
        throw new CiviCRM_API3_Exception('Could not match given group ID.', 0);
        break;
    }
    if (!in_array($params['subscribed'], array('t', 'f'))) {
      throw new CiviCRM_API3_Exception('Unknown value for parameter "subscribed"', 0);
    }
    $group_contact = civicrm_api3('GroupContact', 'create', array(
      'group_id' => $group_id,
      'contact_id' => $contact_id,
      'status' => ($params['subscribed'] == 't' ? 'Added' : 'Removed'),
    ));

    // Return the result.
    return civicrm_api3_create_success(array(array(
      'contact_id' => $contact_id,
      'group_id' => $group_id,
      'status' => $group_contact,
    )));
  }
  catch (CiviCRM_API3_Exception $exception) {
    return civicrm_api3_create_error($exception->getMessage());
  }
}

/**
 * API specification for "submit" call on "CeemesSubscription" entity.
 *
 * @param $params
 */
function _civicrm_api3_ceemes_subscription_submit_spec(&$params) {
  $params['idx'] = array(
    'name' => 'idx',
    'title' => 'Ceemes ID',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
    'description' => 'The Ceemes subscriber ID.',
  );
  $params['cgid'] = array(
    'name' => 'cgid',
    'title' => 'Ceemes newsletter group ID',
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
    'description' => 'The Ceemes newsletter group ID.',
  );
  $params['cgname'] = array(
    'name' => 'cgname',
    'title' => 'Ceemes newsletter group name',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The Ceemes newsletter group name.',
  );
  $params['email'] = array(
    'name' => 'email',
    'title' => 'E-mail address',
    'type' => CRM_Utils_Type::T_STRING,
    // Required for creating contact, will be handled by the Contact API.
    'api.required' => 0,
    'description' => 'The subscriber\'s e-mail address.',
  );
  $params['firstname'] = array(
    'name' => 'firstname',
    'title' => 'First name',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The subscriber\'s first name.',
  );
  $params['lastname'] = array(
    'name' => 'lastname',
    'title' => 'Last name',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The subscriber\'s last name.',
  );
  $params['greeting'] = array(
    'name' => 'greeting',
    'title' => 'Greeting',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
    'description' => 'The subscriber\'s greeting. Prefix and gender will be parsed out of the greeting.',
  );
  $params['subscribed'] = array(
    'name' => 'subscribed',
    'title' => 'Subscription status',
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
    'description' => 'The subscriber\'s subscription status. May be either "t" (subscribed) or "f" (unsubscribed).',
  );
}
