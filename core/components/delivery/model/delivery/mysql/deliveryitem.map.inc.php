<?php
$xpdo_meta_map['deliveryItem']= array (
  'package' => 'delivery',
  'version' => '1.1',
  'table' => 'tp_delivery',
  'fields' => 
  array (
    'ID' => NULL,
    'NAME' => NULL,
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
    'NAME' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
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
