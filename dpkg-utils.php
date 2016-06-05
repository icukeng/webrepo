<?php namespace debrepo\dpkg;

function cisdigit($c) { 
	if(!is_string($c)) return false;
	return ((string)$c>='0') && ((string)$c<='9'); 
}
function cisalpha($c) { return (($c>='a') && ($c<='z')) || (($c>='A') && ($c<='Z')); }
function order($c) {
	if (is_string($c) && cisdigit($c)) return 0;
	if (is_string($c) && cisalpha($c)) return ord($c);
	if ($c === '~')     return -1;
	if (is_string($c))  return ord($c) + 256;
	return 0;
}
// =======================================================
function version_compare($a, $b) {
	if($a->epoch <> $b->epoch) {
		if($a->epoch > $b->epoch) return  1;
		if($a->epoch < $b->epoch) return -1;
	}
	
	foreach(['version', 'revision'] as $field) {
		$aa = $a->$field;
		$bb = $b->$field;
		$ai = 0;
		$bi = 0;
		while( isset($aa[$ai]) || isset($bb[$bi]) ) {
			$digit_diff = 0;
			$aaa = isset($aa[$ai]) ? $aa[$ai] : 0;
			$bbb = isset($bb[$bi]) ? $bb[$bi] : 0;

			// print "$aaa - $bbb\n";
			// сравнения с не-цифрами
			while( ($aaa !== 0 && !cisdigit($aaa)) || ($bbb !== 0 && !cisdigit($bbb)) ) {
				$ac = order($aaa);
				$bc = order($bbb);
				if($ac != $bc) return $ac - $bc;
				$aaa = isset($aa[++$ai]) ? $aa[$ai] : 0;
				$bbb = isset($bb[++$bi]) ? $bb[$bi] : 0;
			}
			// сравнение с цифрами - убираем ведущие нули
			while($aaa === '0') $aaa = isset($aa[++$ai]) ? $aa[$ai] : 0;
			while($bbb === '0') $bbb = isset($bb[++$bi]) ? $bb[$bi] : 0;
			// сравниваем первых разошедшийся разряд
			while (cisdigit($aaa) && cisdigit($bbb)) {
				if (!$digit_diff) $digit_diff = ord($aaa) - ord($bbb);
				$aaa = isset($aa[++$ai]) ? $aa[$ai] : 0;
				$bbb = isset($bb[++$bi]) ? $bb[$bi] : 0;
			}
			// проверяем какое число длиннее
			if (cisdigit($aaa)) return 1;
			if (cisdigit($bbb)) return -1;
			if ($digit_diff)    return $digit_diff;
		}
	}
	return 0;
}

function version_parse($str) {
	@list($epoch, $version) = explode(':', $str, 2);
	if(!$version) { $version = $epoch; $epoch = 0; }
	@list($version, $revision) = explode('-', $str, 2);
	return (object) ['epoch' => $epoch, 'version' => $version, 'revision' => $revision];
}

