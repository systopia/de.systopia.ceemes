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
  // Translate parameters.
  try {
    $idx_field = civicrm_api3('CustomField', 'get', array('name' => 'Ceemes_ID'));
    if ($idx_field['count'] != 1) {
      throw new CiviCRM_API3_Exception('Custom field "Ceemes_ID" not found.', 0);
    }
  }
  catch (CiviCRM_API3_Exception $exception) {
    return civicrm_api3_create_error($exception->getMessage());
  }
  foreach (array(
    'firstname' => 'first_name',
    'lastname' => 'last_name',
    'idx' => 'custom_' . $idx_field['id'],
           ) as $parameter => $field_name) {
    $params[$field_name] = $params[$parameter];
    unset($params[$parameter]);
  }
  if (!empty($params['greeting'])) {
    $params += CRM_Ceemes_Submission::parseGreeting($params['greeting']);
  }

  // Find or create contact using XCM.
  $contact_data = array_intersect_key($params, array_flip(array(
    'custom_' . $idx_field['id'],
    'email',
    'first_name',
    'last_name',
    'prefix_id',
    'gender_id',
  )));
  try {
    $contact_id = civicrm_api3('Contact', 'getorcreate', $contact_data);
  }
  catch (CiviCRM_API3_Exception $exception) {
    return civicrm_api3_create_error($exception->getMessage());
  }

  // TODO: Add group membership with subscribed status.
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
    'api.required' => 0, // TODO: Is required for first call for this contact.
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
