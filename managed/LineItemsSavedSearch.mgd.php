<?php

use CRM_Lineitemedit_ExtensionUtil as E;

// There seems to be inconsistency with whether BASEURL has a trailing slash so when removing it, we also handle what is now a leading slash.
$buttonRelativeUrl = '/' . ltrim(str_replace(CIVICRM_UF_BASEURL, '', E::url('templates/CRM/Lineitemedit/Form/Buttons.html')), '/');
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
        ],
      ],
    ], 
    [
      'name' => 'SavedSearch_Line_Items_SearchDisplay_Contribution_Amount_Tax', 
      'entity' => 'SearchDisplay', 
      'cleanup' => 'unused', 
      'update' => 'unmodified', 
      'params' => [
        'version' => 4, 
        'values' => [
          'name' => 'Line_Items_Table_Tax', 
          'label' => E::ts('Contribution Amount'), 
          'saved_search_id.name' => 'Line_Items', 
          'type' => 'table', 
          'settings' => [
            'description' => NULL,
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
                'id',
                'ASC',
              ],
            ], 
            'columns' => [
              [
                'type' => 'field', 
                'key' => 'label', 
                'dataType' => 'String', 
                'label' => E::ts('Item'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Financial Type'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Qty'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Unit Price'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Total Price'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Tax Amount'), 
                'sortable' => FALSE,
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
                'path' => $buttonRelativeUrl,
                'type' => 'include',
                'alignment' => 'text-right',
              ],
            ],
            'actions' => FALSE,
            'headerCount' => FALSE,
            'button' => NULL,
          ],
        ],
      ],
    ], 
    [
      'name' => 'SavedSearch_Line_Items_SearchDisplay_Line_Items_Table', 
      'entity' => 'SearchDisplay', 
      'cleanup' => 'unused', 
      'update' => 'unmodified', 
      'params' => [
        'version' => 4, 
        'values' => [
          'name' => 'Line_Items_Table', 
          'label' => E::ts('Contribution Amount'), 
          'saved_search_id.name' => 'Line_Items', 
          'type' => 'table', 
          'settings' => [
            'description' => NULL, 
            'sort' => [
              [
                'id',
                'ASC',
              ],
            ], 
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
            'columns' => [
              [
                'type' => 'field', 
                'key' => 'label', 
                'dataType' => 'String', 
                'label' => E::ts('Item'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Financial Type'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Qty'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Unit Price'), 
                'sortable' => FALSE,
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
                'label' => E::ts('Total Price'), 
                'sortable' => FALSE,
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
                'path' => $buttonRelativeUrl,
                'type' => 'include',
                'alignment' => 'text-right',
              ],
            ], 
            'actions' => FALSE, 
            'headerCount' => FALSE, 
            'button' => NULL,
          ], 
        ],
      ],
    ],
  ];
