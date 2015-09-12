<?php
class CmdResp{
	public function __construct($s, $r) {
		$this->status = $s;
		$this->responce = $r;
	}
	public $status;
	public $responce;
}

function cmdcall($cmd, $ENV = null) {
	$io = array(
		0 =>array("pipe", "r"),
		1 =>array("pipe", "w"),
		2 =>array("pipe", "w"),
	);
	$process = proc_open($cmd, $io, $pipes, NULL, $ENV);
	fclose($pipes[0]);// close stdin
	$numpipes = 2;
	$write = $except = NULL; // dummies for select
	$resp = array( 0 => null, 1 => null, 2 => null ); // responce storage
	do {
		$read = array( 1 => $pipes[1],  2 => $pipes[2], );
		if(false === ($num = @stream_select($read, $write, $except, 30))) break;
		foreach ($read as $pipe) {
			$key = array_search($pipe, $pipes);
			$value = fgets($pipe);
			if($value) {
				$resp[0] .= ($key == 1 ? 'out: ' : 'err: ').$value;
				$resp[$key][] = rtrim($value);
			}
			if(feof($pipe)) {
				$numpipes--;
				fclose($pipes[$key]);
				continue;
			}
		}
	} while ($numpipes);
	$st = proc_close($process);
	// return array(pcntl_wexitstatus($st), $resp);
	return new CmdResp($st, $resp);
}
