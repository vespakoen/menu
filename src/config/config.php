<?php return array(

  // Global options ------------------------------------------------ /

  // The maximum depth a list can be generated
  // 0 means no limit
  'max_depth' => 0,

  // Items --------------------------------------------------------- /

  // Various options related to Items
  'item' => array(

    // The default Item element
    'element' => 'li',

    // Various classes to mark active items or children
    'active_class'       => 'active',
    'active_child_class' => 'active-child',
  ),

  // ItemLists ----------------------------------------------------- /

  'item_list' => array(

    // The default ItemList element
    'element' => 'ul',

    // A prefix to prepend the links URLs with
    'prefix'         => null,

    // Whether links should inherit their parent/handler's prefix
    'prefix_parents' => false,
    'prefix_handler' => false,
  ),
);
