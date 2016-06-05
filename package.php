<?php
/*
Need to unify storing packages info. And to fetch some samples of them.
*/

class PackageList {
	private $list;
	public function register($repo, $name, $version, $arch) {
		$this->list[] = array(
			'name' => $name,
			'version' => $version,
			'repo' => $repo,
			'arch' => $arch,
		);
	}
	public function sort($callback) {
		usort($this->list, $callback);
	}
	public function find() {
		return new PackageIterator($this->list);
	}
	public function merge($id, $merge) {
		$this->list[$id] = array_merge($this->list[$id], $merge);
	}
}

class PackageIterator {
	private $list;
	private $filtered;
	public function __construct($list) {
		$this->list = $list;
		$this->filtered = array_keys($list);
	}
	public function filter($name, $value) {
		$list = $this->list;
		$this->filtered = array_filter($this->filtered, function($num) use($list, $name, $value) {
			return $list[$num][$name] == $value;
		});
		return $this;
	}
	public function group($bylist, $lastfield = null) {
		$out = array();
		$iout = null;
		foreach ($this->list as $item) {
			$iout = &$out;
			foreach ($bylist as $by) {
				if(!isset($iout[$item[$by]])) $iout[$item[$by]] = array();
				$iout = &$iout[$item[$by]];
			}
			if($lastfield)
				$iout = $item[$lastfield];
			else
				$iout[] = $item;
		}
		return $out;
	}
}
