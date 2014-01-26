# Menu

[![Build Status](https://secure.travis-ci.org/vespakoen/menu.png?branch=master)](http://travis-ci.org/vespakoen/menu)

[Api Docs](http://vespakoen.github.io/menu/api/ "API Docs")

Are you the type of person that writes menus by hand in view files or do you find yourself looking for the best place to store links to pages on your website? then Menu is for you!

# Key concepts

## Item lists
An item list is what a menu is all about and it should be pretty self explanatory because it simply stores a list of items.
there are some configurations available for an item list.
You can, for example set the HMTL element that will be used to render the list, prefix every item in the list with all the parent's url segments, and a lot more. We will explore these options later.

## Menu handlers
Menu handlers allow us to create and interact with item lists and act as a place to store and retrieve our menus.
Because we are able to interact with multiple item lists at the same time some interesting possibilities become available to us.

## Items
The Menu package has 2 types of items available out of the box.

- Link
  For creating links to other pages
- Raw
  Be free to add anything you like in the item.
  This type is usually used for dividers, titles etc.

The HTML element and attributes for the item can also be changed, more on this topic later.

# Installing

## Laravel 3

Install Menu via the artisan command line tool. Open the terminal and navigate to your Laravel project's root.
Now type the following command :

```shell
php artisan bundle:install menu
```

To let Laravel know the Laravel Menu package should be started, open up `application/packages.php` and add the following lines to the packages array.

```php
'menu' => array('auto' => true),
```

## Laravel 4

Add this to your `composer.json` file's `"require"` :

```json
"vespakoen/menu": "dev-master"
```

And add the following to your `app/config/app.php` file :

- In the Service Providers array : `'Menu\MenuServiceProvider',`
- In the aliases array : `'Menu' => 'Menu\Menu',`

# Basic usage

First, let's load some pages into the menu.
We will do this by utilising the `hydrate` method.


```php
Menu::handler('main')->hydrate(function()
  {
    return Page::with('translation')
      ->where('group', '=', 'main')
      ->get();
  },
  function($children, $item)
  {
    $children->add($item->translation->slug, $item->translation->name, Menu::items($item->as));
  });

/* the hydrate method takes these arguments
  $resolver       Closure     A callback to resolve results
  $decorator      Closure     A callback that gets called for every result fetched from the resolver with the corresponding ItemList and result as the arguments
  $idField        integer (default = 'id')           the property on the result that contains the id
  $parentIdField  integer (default = 'parent_id')    the property on the result that contains the parent id
  $parentId       integer (default = 0)              the parentId to start hydrating from
*/
```

Now that we have loaded our pages into the menu, and even identified every menu item with a name (via `Menu::items($item->as)`) a lot of options are available to us.

Find a node by it's name and add a subitem.
```php
Menu::find('users')
  ->add('users/create', 'Create new user');
```

Add some properties to the root node
```php
Menu::handler('main')
  ->addClass('nav navbar-nav');
```

Get all `ItemList`s at a certain depth and add a class
```php
Menu::handler('main')
  ->getItemListsAtDepth(0)
  ->addClass('level-1');
```

Get all `ItemList`s at a depth range and change the element
```php
Menu::handler('main')
  ->getItemListsAtDepthRange(0,2)
  ->setElement('div');
```

Get all `Item`s at a certain depth and add a class
```php
Menu::handler('main')
  ->getItemsAtDepth(0)
  ->addClass('level-1');
```

Get `Item`s by it's content type and use the map function to walk over the results and perform actions based on gathered information
```php
Menu::handler('main')
  ->getItemsByContentType('Menu\Items\Contents\Link')
  ->map(function($item)
  {
    if($item->isActive() && $item->hasChildren())
    {
      $item->addClass('is-active-link-with-children');
    }

    if($item->getContent()->getUrl() == 'home')
    {
      $item->addClass('is-home');
    }
  });
```

Get all `ItemList`s and add a class to them if they have children
```php
Menu::handler('main')
  ->getAllItemLists()
  ->map(function($itemList)
  {
    if($itemList->hasChildren())
    {
      $itemList->addClass('has-children');
    }
  });
```

# Breadcrumbs

Breadcrumb hell is a thing of the past.

Bootstrap ready breadcrums are as easy as this
```php
Menu::handler('main')
    ->breadcrumbs()
    ->setElement('ol')
    ->addClass('breadcrumb');
```

The breadcrumbs method searches all handlers and returns a plain old ItemList, that you can manipulate.
If you call the breadcrumbs method directly on the Menu class, it will search all your handlers for breadcrumbs, and by default will return the first match.
However, there might be cases where you want to choose the breadcrumbs out of the ones it found, for this you can provide a callback method as the first argument.

And example is shown below:
```php
Menu::breadcrumbs(function($itemLists)
    {
      return $itemLists[0]; // returns first match
    })
    ->setElement('ol')
    ->addClass('breadcrumb');
```

# Diving deeper

The Laravel Menu packages consists of a couple of classes, but you can interact with all of them via the _Menu_ class.
Let's take a look at the **handler** method. it takes a string or an array as the only argument, the string(s) given are the names for the item lists we want to retrieve.
If an itemlist we asked for didn't exist yet, it will create it for us.
After the menu class has found and created the item lists we want, it will hand back a menuhandler that handles the item lists we asked for.

```php
// Get a MenuHandler instance that handles an ItemList named "main"
Menu::handler('main');
```

When we call a method on this menu handler, it will simply forward the call to all the item lists that it handles.
In order to find out what we can do now that we have a handler, we need to take a look at the methods on the ItemList class.

The _ItemList_ class has a method called **add** that you are probably going to use a lot. It adds an _Item_ of type "link" to the _ItemList_.

```php
Menu::handler('main')->add('home', 'Homepage');

/* The add method takes these arguments
  $url  string  The URL to another page
  $title  string  The visible string on the link
  $children (default = null)  ItemList  (optional) The children of this page
  $link_attributes (default = array())  array (optional) HTML attributes for the <a> element
  $item_attributes (default = array())  array (optional) HTML attributes for the list element (usually <li>)
  $item_element (default = 'li')  string  (optional) The type of the list element
*/
```

Let's take a look at the **raw** method, for adding "anything" to the list.

```php
Menu::handler('main')->raw('<img src="img/seperator.gif">');

/* The raw method takes these arguments
  $html string  The contents of the item
  $children (default = null)  ItemList  (optional) The children of this item
  $item_attributes (default = array())  array (optional) HTML attributes for the list element (usually <li>)
  $item_element (default = 'li')  string  (optional) The type of the list element
*/
```

Great! Now that we have learned how to add items to an item list, let's have a look at how we add children to a item.
Every item can have children, the children object is just another _ItemList_. As we have seen before, we can create item lists via the **handler** method, but this method returns a _MenuHandler_, making it unusable for item children.
So what do we use? the **items** method returns a fresh _ItemList_ object. Let's have a look.

```php
Menu::handler('main')
    ->add('home', 'Homepage', Menu::items()
        ->add('sub-of-home', 'Sub of homepage'));

/* The items method takes these arguments
  $name (default = null)  string  (optional) The name (=identifier) of this ItemList
  $attributes (default = array()) array (optional) HTML attributes for the ItemList element (usually <ul>)
  $element (default = 'ul') string  (optional) The type of the ItemList element
*/
```

So now we know how to build menus, add items and items with children.
Let's find out how to display the menus.
The MenuHandler and _ItemList_ classes implement the "__toString" method, that calls the **render** method.
This means you can simply echo the MenuHandler or _ItemList_ object.
Here is an example to make things more clear.

```php
echo Menu::handler('main');

// Is the same as

echo Menu::handler('main')->render();
```

Now that we have the basics under control, we are going to explore some other cool features this package provides.

# Class diagram

[![Class diagram](http://rawgithub.com/vespakoen/menu/master/menu-class-diagram.svg)](http://rawgithub.com/vespakoen/menu/master/menu-class-diagram.svg)

# Some last words

Thanks for following along and using this package.
Special thanks to @Anahkiasen for refactoring this package and boosting new life into it!
