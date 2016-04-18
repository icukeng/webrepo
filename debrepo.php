<?php
include "exec.php";

// =================================================================================
class DebRepo {
	public $name;
	public function __construct($name) {
		$this->name = $name;
	}
	function cmdName() { return $this->name; }
	function content() {
		// aptly repo show -with-packages et
		$cmd = cmdcall('cat dummy/aptly-repo-show-'.$this->name);
		$start = false;	
		$out = array();
		foreach ($cmd->responce[1] as $str) {
			if(preg_match('/^Packages:/', $str)) {
				$start = true;
				continue;
			}
			if(!$start) continue;
			$obj = explode('_', ltrim($str));
			$arch = array_pop($obj);
			$vers = array_pop($obj);
			$name = implode('_', $obj);
			$out[] = array(
				'name' => $name,
				'vers' => $vers,
				'arch' => $arch,
			);
		}
		//print "<pre>";print_r($out);die;
		return $out;
	}
}

class Transfer {
	public $rcv;
	public $trx;
	public function __construct($tx, $rx) {
		$this->rcv = $rx;
		$this->trx = $tx;
	}
	function copy ($pkg, $vers) {
		// $cmd = cmdcall('aptly copy rx tx pkg');
		sleep(3);
		return true;
	}
}