# Menu builder for Laravel

## Example uses

Let's say we have 2 roles, ***reseller*** and ***store owner***

In the backend (***domain.com/backend/...***) of our application we have some controllers.


- ***accounts*** (shows some customer accounts for ***resellers***, and some store manager accounts for ***store owners***)

- ***pages*** (shows all the pages for a store, only available for ***store owners***)

Now we want to build a menu for our backend, but since every role gets different pages, we are going to use a menu container for every role.

	Menu::container(array('storeowner', 'reseller'))
		->add('backend/accounts', 'Accounts');
	
	Menu::container('storeowner')
		->add('backend/pages', 'Pages');

	echo Menu::container('reseller');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *     </li>
	 * </ul>
	 */


	echo Menu::container('storeowner');
	/* returns:
	 * <ul>
	 *     <li><a href="http://domain.com/backend/accounts">Accounts</a></li>
	 *     <li><a href="http://domain.com/backend/pages">Pages</a></li>
	 * </ul>
	*/

That was simple, but we don't have to specify "backend/" before every url, by setting the second container argument every url in the container (including subs) will be prefixed with the given string, as you can see in the next example

	Menu::container(array('storeowner', 'reseller'), 'backend')
		->add('accounts', 'Accounts');
	
	Menu::container('storeowner')
		->add('pages', 'Pages');

	echo Menu::container('reseller');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *     </li>
	 * </ul>
	 */


	echo Menu::container('storeowner');
	/* returns:
	 * <ul>
	 *     <li><a href="http://domain.com/backend/accounts">Accounts</a></li>
	 *     <li><a href="http://domain.com/backend/pages">Pages</a></li>
	 * </ul>
	*/

What if we want the url prefix to be the container name? we can do that by setting the second container argument to "true"

	Menu::container(array('storeowner', 'reseller'), true)
		->add('accounts', 'Accounts');
	
	Menu::container('storeowner')
		->add('pages', 'Pages');

	echo Menu::container('reseller');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/reseller/accounts">Accounts</a>
	 *     </li>
	 * </ul>
	 */


	echo Menu::container('storeowner');
	/* returns:
	 * <ul>
	 *     <li><a href="http://domain.com/storeowner/accounts">Accounts</a></li>
	 *     <li><a href="http://domain.com/storeowner/pages">Pages</a></li>
	 * </ul>
	*/

Here is an example that shows you how to add children to a menu, and how to set some additional attributes on the ***li*** tags.

	Menu::container(array('storeowner', 'reseller'), 'backend')
		->add('accounts', 'Accounts', array('class' => 'has_subs'), Menu::items()->add('accounts/add', 'Add Account'));
	
	$pages_subs = Menu::items()->add('pages/add', 'Add Page');
	Menu::container('storeowner')
		->add('pages', 'Pages', array(), $pages_subs);

	echo Menu::container('reseller'); //<ul><li><a href="http://local.shophub.io/backend/accounts">Accounts</a><ul><li><a href="http://local.shophub.io/backend/accounts/add">Add Account</a></li></ul></li></ul>
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *         <ul>
	 *             <li><a href="http://domain.com/backend/accounts/add">Add Account</a></li>
	 *         </ul>
	 *     </li>
	 * </ul>
	 */


	echo Menu::container('storeowner');
	/* returns:
	 * <ul>
	 *     <li>
	 *         <a href="http://domain.com/backend/accounts">Accounts</a>
	 *         <ul>
	 *             <li><a href="http://domain.com/backend/accounts/add">Add Account</a></li>
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

Instead of using "echo Menu::container('reseller');" you can also use the render method and pass some attributes for the ***ul*** and ***a*** tags, that would look like this

	echo Menu::container('reseller')->render(array('class' => 'nav'), array('class' => 'link'));



# Enjoy it, improvements are welcome!