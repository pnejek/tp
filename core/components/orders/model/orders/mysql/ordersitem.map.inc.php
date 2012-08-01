<?php
$xpdo_meta_map['ordersItem']= array (
  'package' => 'orders',
  'version' => '1.1',
  'table' => 'tp_orders',
  'fields' => 
  array (
    'ID' => NULL,
    'USER_ID' => NULL,
	'COMMENT' => NULL,
	'DELIVIRY_ID' => NULL,
	'PAYMENT_ID' => NULL, 
	'STATUS' => NULL,
	'ADDRESS' => NULL,
	'PAYANYWAY' => NULL,
	'DATE' => NULL,
  ),
  'fieldMeta' => 
  array (
    'ID' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
	'USER_ID' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
	  'default'=>0
    ),
    'COMMENT' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
	  'default' => NULL
    ),
	'DELIVIRY_ID' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
	  'default'=>0
    ),
	'PAYMENT_ID' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
	  'default'=>0
    ),
	'STATUS' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
	  'default'=>0
    ),
	'ADDRESS' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
	  'default' => NULL
    ),
	'PAYANYWAY' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
	  'default' => NULL
    ),
	'DATE' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'date',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'ID' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
