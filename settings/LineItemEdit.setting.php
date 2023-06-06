<?php

use CRM_Lineitemedit_ExtensionUtil as E;

return array(
  'line_item_number' => array(
    'name' => 'line_item_number',
    'type' => 'Integer',
    'default' => 10,
    'html_type' => 'text',
    'title' => ts('Adjust line number'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Limits the number of lines shown'),
    'settings_pages' => ['contribute' => ['weight' => 10]],
  )
);

