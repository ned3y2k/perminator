<?php
namespace classes\webpage\menu;

class Menu {
	public $code;
	public $link;
	public $title;
	public $icon;
	public $active = false;
	public $childPages;

	public $reservedDic;

	/**
	 * @param int                                   $code
	 * @param string                                $title
	 * @param                                       $link
	 * @param \classes\webpage\menu\MenuIcon|string $icon icon path
	 * @param array                                 $reservedDic
	 * @param array                                 $childPages
	 */
	function __construct($code, $title, $link, MenuIcon $icon = null, array $reservedDic = array(), array $childPages = array()) {
		$this->code        = $code;
		$this->title       = $title;
		$this->link        = $link;
		$this->icon        = $icon == null ? new MenuIcon(null, null, null) : $icon;
		$this->reservedDic = $reservedDic;
		$this->childPages  = $childPages;
	}

	public function putReserved($name, $value) {
		$this->reservedDic[ $name ] = $value;

		return $this;
	}

	public function getReserved($name) {
		if (!array_key_exists($name, $this->reservedDic)) return null;

		return $this->reservedDic[ $name ];
	}
}

class MenuIcon {
	public $normal;
	public $over;
	public $active;

	public $reservedIconDic = array();

	function __construct($normal, $over, $active) {
		$this->normal = $normal;
		$this->over   = $over;
		$this->active = $active;
	}

	public function putReservedIcon($name, $value) {
		$this->reservedIconDic[ $name ] = $value;

		return $this;
	}

	public function getReservedIcon($name) {
		if (!array_key_exists($name, $this->reservedIconDic)) throw new \OutOfBoundsException("{$name} icon define not found");

		return $this->reservedIconDic[ $name ];
	}
}

class MenuContainer implements \Iterator {
	/** @var MenuGroup[] */
	private $menuGroups = array();
	private $position = 0;

	public function addGroup(MenuGroup $group) {
		$this->menuGroups[ ] = $group;
	}

	/** @return MenuGroup */
	public function current() { return $this->menuGroups[ $this->position ]; }

	public function key() { return $this->position; }

	public function valid() { return array_key_exists($this->position, $this->menuGroups); }

	public function next() { ++$this->position; }

	public function rewind() { $this->position = 0; }
}

class MenuGroup implements \Iterator {
	private $title;

	/** @var Menu[] */
	private $menus = array();
	private $groupCode;
	private $position = 0;
	private $menuIcon;
	public $active = false;

	public function __construct($title, $groupCode = 0, MenuIcon $menuIcon = null) {
		$this->title     = $title;
		$this->groupCode = $groupCode;

		$this->menuIcon = $menuIcon == null ? new MenuIcon(null, null, null) : $menuIcon;
	}

	/**
	 * @throws \InvalidArgumentException
	 * @return MenuGroup
	 */
	public function addMenus() {
		$menus = func_get_args();
		foreach ($menus as $menu) {
			if ($menu instanceof Menu) $this->menus[ ] = $menu;
			else throw new \InvalidArgumentException();
		}

		return $this;
	}

	public function addMenu(Menu $menu) {
		$this->menus[ $menu->code ] = $menu;

		return $this;
	}

	public function getTitle() { return $this->title; }

	public function getMenus() { return $this->menus; }

	public function getGroupCode() { return $this->groupCode; }

	public function getMenuIcon() { return $this->menuIcon; }

	/** @return Menu */
	public function current() { return $this->menus[ $this->position ]; }

	public function key() { return $this->position; }

	public function valid() { return array_key_exists($this->position, $this->menus); }

	public function next() { ++$this->position; }

	public function rewind() { $this->position = 0; }
}