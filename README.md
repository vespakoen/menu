# Menu builder for Laravel

## Why?

There are some great Menu builders out there already,
but this has some extra features that make it really cool and handy to use


## What?

A Menu bundle that will make managing links a breeze!
This Menu bundle allows you to have **item lists**, they basically allow you to make multiple Menus. As an added bonus, you interact with ItemLists via
a **menu handler**

Menu Handlers allow you to interact with multiple Menu Containers at once, or render multiple containers at once.

Don't let this scare you off, the interface for working with all these things is very simple!

## How?

### Example use

Let's say we have 2 roles, ***reseller*** and ***store owner***

In the backend (***domain.com/backend/...***) of our application we have some controllers.

- ***accounts*** (shows some customer accounts for ***resellers***, and some store manager accounts for ***store owners***)

- ***pages*** (shows all the pages for a store, only available for ***store owners***)

Now we want to build a menu for our backend, but since every role gets different pages, we are going to use a menu container for every role.

```php
	<?php
	Menu::handler(array('storeowner', 'reseller'))
		->add('backend/accounts', 'Accounts');
	
	Menu::handler('storeowner')
		->add('backend/pages', 'Pages');

	echo Menu::handler('reseller');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *     </li>
	 * </ul>
	 */


	echo Menu::handler('storeowner');
	/* returns:
	 * <ul>
	 *     <li><a href="http://domain.com/backend/accounts">Accounts</a></li>
	 *     <li><a href="http://domain.com/backend/pages">Pages</a></li>
	 * </ul>
	*/
```

As you can see, we added items to 2 different menu containers, and we interacted with them via a handler.

That was simple, but we don't have to specify "backend/" for every single url, we can simplify this by adding ´->prefix('backend')´ when showing the menu.

By doing this, every link (including subs) will be prefixed with the given string, and a "/" will automatically be added to the end.

```php
	<?php
	Menu::handler(array('storeowner', 'reseller'))
		->add('accounts', 'Accounts');
	
	Menu::handler('storeowner')
		->add('pages', 'Pages');

	echo Menu::handler('reseller')->prefix('backend');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *     </li>
	 * </ul>
	 */


	echo Menu::handler('storeowner')->prefix('backend');
	/* returns:
	 * <ul>
	 *     <li><a href="http://domain.com/backend/accounts">Accounts</a></li>
	 *     <li><a href="http://domain.com/backend/pages">Pages</a></li>
	 * </ul>
	*/
```

What if we want the url prefix to be the parent item's URLs? we can do that by adding `->prefix_parents()` at the end

```php
	<?php
	Menu::handler('reseller')
		->add('accounts', 'Accounts');

	echo Menu::handler('reseller')->prefix_parents();
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/reseller/accounts">Accounts</a>
	 *     </li>
	 * </ul>
	 */
```

Here is an example that shows you how to add children to a menu, how to set some additional attributes for the link and list item and how to specify a custom listitem element type. Wow that was a lot! Just take a close look at the example ;)

```php
	<?php
	Menu::handler(array('storeowner', 'reseller'))
		->add('accounts', 'Accounts', Menu::items()
			->add('accounts/add', 'Add Account', null, array('class' => 'link'), array('class' => 'listitem'), 'dt')
		);
	
	$pages_subs = Menu::items()->add('pages/add', 'Add Page');
	Menu::handler('storeowner')
		->add('pages', 'Pages', $pages_subs);

	echo Menu::handler('reseller')->prefix('backend');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *         <ul>
	 *             <dt class="listitem"><a class="link" href="http://domain.com/backend/accounts/add">Add Account</a></dt>
	 *         </ul>
	 *     </li>
	 * </ul>
	 */


	echo Menu::handler('storeowner')->prefix('backend');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *         <ul>
	 *             <dt class="listitem"><a class="link" href="http://domain.com/backend/accounts/add">Add Account</a></dt>
	 *         </ul>
	 *     </li>
	 *     <li>
	 *         <a href="http://domain.com/backend/pages">Pages</a>
	 *         <ul>
	 *             <li><a href="http://domain.com/backend/pages/add">Add Page</a></li>
	 *         </ul>
	 *     </li>
	 * </ul>
	 */
```

Instead of using "echo Menu::handler('reseller');" you can also use the render method and add some attributes to the list and use a custom element type

In this example, we use an "ol" (ordered list) element in stead of the default "ul" element and add the class "nav" to it

```php
	<?php
	Menu::handler('reseller')->add('accounts', 'Accounts');

	echo Menu::handler('reseller')->prefix('backend')->render(array('class' => 'nav'), 'ol');
	/* returns:
	 * <ol class="nav">
	 *     <li><a href="http://domain.com/backend/accounts">Accounts</a></li>
	 * </ol>
	*/
```

This Menu builder will automatically add a "active" class to a list item in case the URL matches, and will add an "active-children" class to all items that have a child item that is active.

## Some other examples

```php
<?php
	function initialize_menu($parent_id = 0)
	{
		$pages = DB::table('pages')
			->where_parent_id($parent_id)
			->get();

		foreach ($pages as $page)
		{
			if($parent_id == 0)
			{
				$menus = Menu::handler(explode(',', $page->menus));
			}
			else
			{
				$menus = Menu::all()
					->find($parent_id);
			}

			$menus->add($page->url, $page->menu, Menu::items($page->id));

			initialize_menu($page->id);
		}
	}

	
	$menu = Menu::handler('menu');
	
	$languages = array(
		'en' => 'English',
		'nl' => 'Nederlands'
	);

	foreach ($languages as $code => $name)
	{
		$menu->add($code, $name, Menu::items($code));
	}

	// Add some items to the dutch menu
	$menu
		->find('nl')
		->prefix_parents()
		->add('welkom', 'Welkom')
		->add('over-ons', 'Over ons');
		

	// Add some items to the english menu
	$menu
		->find('en')
		->prefix_parents()
		->add('welcome', 'Welcome')
		->add('about-us', 'About us');
		

	// Render the top level only (languages)
	echo $menu->render(array(
		'max_depth' => 1
	));

	// Render the english menu
	echo $menu->find('en');

	// I used the $menu variable (for less typing in this example)
	// If you want to render the menu in another file, just reference
	// It by the handler again.
	
	// Render the dutch menu
	echo Menu::handler('menu')->find('nl');

	// Render some categories
	Menu::handler('categories')
		->add('algorithms', 'Algorithms', Menu::items()->prefix_parents()
			->add('cryptography', 'Cryptography')
			->add('data-structures', 'Data Structures')
			->add('digital-image-processing', 'Digital Image Processing')
			->add('memory-management', 'Memory Management'))
		->add('graphics-and-multimedia', 'Graphics & Multimedia', Menu::items()->prefix_parents()
			->add('directx', 'DirectX')
			->add('flash', 'Flash')
			->add('opengl', 'OpenGL'));

	echo Menu::handler('categories');
```

# Enjoy it, improvements are welcome!