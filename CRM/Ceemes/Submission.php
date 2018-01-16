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

class CRM_Ceemes_Submission {

  /**
   * @param string $greeting
   *   The string out of which to parse prefix and gender.
   *
   * @return array
   *   An array with the keys "prefix_id" and "gender_id" containing the
   *   matching IDs for the parsed values for the particular fields, or
   *   containing NULL if the field value could not be parsed out of the given
   *   greeting.
   */
  public static function parseGreeting($greeting) {
    $result = array(
      'prefix_id' => NULL,
      'gender_id' => NULL,
    );

    // TODO: Parse prefix and gender out of the given greeting.

    return $result;
  }

}
