<?php return array(

  // Global options ------------------------------------------------ /

  // The maximum depth a list can be generated
  // -1 means no limit
  'max_depth' => -1,

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

    // The default breadcrumb separator, set to '' to not output any separators for
    // use with bootstrap.
    'breadcrumb_separator' => '/',

    // A prefix to prepend the links URLs with
    'prefix'         => null,

    // Whether links should inherit their parent/handler's prefix
    'prefix_parents' => false,
    'prefix_handler' => false,
  ),
);
