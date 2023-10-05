<?php

use CRM_Lineitemedit_ExtensionUtil as E;

return [
    [
      'name' => 'Line_Items',
      'entity' => 'SavedSearch',
      'cleanup' => 'always',
      'update' => 'always',
      'params' => [
        'version' => 4,
        'values' => [
          'name' => 'Line_Items',
          'label' => E::ts('Contribution Amount'),
          'form_values' => NULL,
          'mapping_id' => NULL,
          'search_custom_id' => NULL,
          'api_entity' => 'LineItem',
          'api_params' => [
            'version' => 4,
            'select' => [
              'entity_id',
              'label',
              'financial_type_id:label',
              'qty',
              'unit_price',
              'line_total',
              'tax_amount',
            ],
            'orderBy' => [],
            'where' => [],
            'groupBy' => [],
            'join' => [
              [
                'Contribution AS LineItem_Contribution_contribution_id_01',
                'INNER',
                [
                  'contribution_id',
                  '=',
                  'LineItem_Contribution_contribution_id_01.id',
                ],
              ],
            ],
            'having' => [],
          ], 
          'expires_date' => NULL, 
          'description' => NULL, 
          'mapping_id' => NULL,
        ],
      ],
    ], 
    [
      'name' => 'SavedSearch_Line_Items_SearchDisplay_Contribution_Amount_Tax', 
      'entity' => 'SearchDisplay', 
      'cleanup' => 'always', 
      'update' => 'always', 
      'params' => [
        'version' => 4, 
        'values' => [
          'name' => 'Line_Items_Table_Tax', 
          'label' => 'Contribution Amount', 
          'saved_search_id.name' => 'Line_Items', 
          'type' => 'table', 
          'settings' => [
            'actions' => FALSE, 
            'limit' => 25, 
            'classes' => [
              'table', 
              'table-striped', 
              'table-bordered',
            ], 
            'pager' => [
              'show_count' => FALSE, 
              'expose_limit' => FALSE,
            ], 
            'sort' => [
              [
                'label', 
                'ASC',
              ],
            ], 
            'columns' => [
              [
                'type' => 'field', 
                'key' => 'label', 
                'dataType' => 'String', 
                'label' => 'Item', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'financial_type_id:label', 
                'dataType' => 'Integer', 
                'label' => 'Financial Type', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'qty', 
                'dataType' => 'Money', 
                'label' => 'Qty', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'unit_price', 
                'dataType' => 'Money', 
                'label' => 'Unit Price', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'line_total', 
                'dataType' => 'Money', 
                'label' => 'Total Price', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'tax_amount', 
                'dataType' => 'Money', 
                'label' => 'Tax Amount', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ],
              [
                'size' => '',
                'links' => [
                  [
                    'entity' => '',
                    'action' => '',
                    'join' => '',
                    'target' => 'crm-popup',
                    'icon' => 'fa-pencil',
                    'text' => '',
                    'style' => 'secondary',
                    'path' => 'civicrm/lineitem/edit?reset=1&id=[id]',
                    'condition' => [],
                    'title' => 'Edit Item',
                  ],
                  [
                    'entity' => '',
                    'action' => '',
                    'join' => '',
                    'target' => 'crm-popup',
                    'icon' => 'fa-repeat fa-flip-horizontal',
                    'text' => '',
                    'style' => 'secondary',
                    'path' => 'civicrm/lineitem/cancel?reset=1&id=[id]',
                    'condition' => [],
                    'title' => 'Cancel Item',
                  ],
                ],
                'type' => 'buttons',
                'alignment' => 'text-right',
              ],
            ],
          ], 
          'acl_bypass' => FALSE,
        ],
      ],
    ], 
    [
      'name' => 'SavedSearch_Line_Items_SearchDisplay_Line_Items_Table', 
      'entity' => 'SearchDisplay', 
      'cleanup' => 'always', 
      'update' => 'always', 
      'params' => [
        'version' => 4, 
        'values' => [
          'name' => 'Line_Items_Table', 
          'label' => 'Contribution Amount', 
          'saved_search_id.name' => 'Line_Items', 
          'type' => 'table', 
          'settings' => [
            'description' => NULL, 
            'sort' => [
              [
                'label', 
                'ASC',
              ],
            ], 
            'limit' => 25, 
            'classes' => [
              'table', 
              'table-striped', 
              'table-bordered',
            ], 
            'pager' => [], 
            'columns' => [
              [
                'type' => 'field', 
                'key' => 'label', 
                'dataType' => 'String', 
                'label' => 'Item', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'financial_type_id:label', 
                'dataType' => 'Integer', 
                'label' => 'Financial Type', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'qty', 
                'dataType' => 'Integer', 
                'label' => 'Qty', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'unit_price', 
                'dataType' => 'Money', 
                'label' => 'Unit Price', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'type' => 'field', 
                'key' => 'line_total', 
                'dataType' => 'Money', 
                'label' => 'Total Price', 
                'sortable' => TRUE,
                'cssRules' => [
                  [
                    'disabled', 
                    'qty', 
                    '=', 
                    '0.00',
                  ],
                ],
              ], 
              [
                'size' => '', 
                'links' => [
                  [
                    'entity' => '', 
                    'action' => '', 
                    'join' => '', 
                    'target' => 'crm-popup', 
                    'icon' => 'fa-pencil', 
                    'text' => '', 
                    'style' => 'secondary', 
                    'path' => 'civicrm/lineitem/edit?reset=1&id=[id]', 
                    'condition' => [], 
                    'title' => 'Edit Item',
                  ], 
                  [
                    'entity' => '', 
                    'action' => '', 
                    'join' => '', 
                    'target' => 'crm-popup', 
                    'icon' => 'fa-repeat fa-flip-horizontal', 
                    'text' => '', 
                    'style' => 'secondary', 
                    'path' => 'civicrm/lineitem/cancel?reset=1&id=[id]', 
                    'condition' => [], 
                    'title' => 'Cancel Item',
                  ],
                ], 
                'type' => 'buttons', 
                'alignment' => 'text-right',
              ],
            ], 
            'actions' => FALSE, 
            'headerCount' => FALSE, 
            'button' => NULL,
          ], 
          'acl_bypass' => FALSE,
        ],
      ],
    ],
  ];