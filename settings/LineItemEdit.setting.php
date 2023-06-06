<?php

use CRM_Lineitemedit_ExtensionUtil as E;

return array(
  'line_item_number' => array(
    'name' => 'Line Item Number',
    'type' => 'Integer',
    'default' => 10,
    'html_type' => 'text',
    'html_attributes' => ['class' => 'crm-form-text'],
    'title' => ts('Adjust line number'),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => ts('Limits the number of lines shown'),
    'settings_pages' => ['contribute' => ['weight' => 10]],
  )
);

