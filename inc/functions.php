<?php

function toAscii($str, $replace=array(), $delimiter='-') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}

	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}

function isLocal() {
	//return false;
	if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') {
		return true;
	} else return false;
}



function numberHash($str) {
	return hexdec(substr(sha1($str), 0, 15));
}

function file_size($size) {
	$unit = array(
		'b',
		'kb',
		'mb',
		'gb',
		'tb',
		'pb'
	);
	return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

function timesince($tsmp) {
	if (!$tsmp) return "";
	$diffu = array(
		'seconds' => 2,
		'minutes' => 120,
		'hours'   => 7200,
		'days'    => 172800,
		'months'  => 5259487,
		'years'   => 63113851
	);
	$diff = time() - strtotime($tsmp);
	$dt = '0 seconds ago';
	foreach ($diffu as $u => $n) {
		if ($diff > $n) {
			$dt = floor($diff / (.5 * $n)) . ' ' . $u . ' ago';
		}
	}
	return $dt;
}

function debug(){
	$args =  func_get_args();

	$return = $args;
	$output = "screen";



	switch (func_num_args()){
		case 0:
		
			exit();
			break;
		case 1:
			$return = $args[0];
			break;
	}

	if (is_array($return)){
		$type = "array";
	} else if(is_object($return)) {
		$type = "array";

	} else {
		$type = "string";
	}



	switch ($type){
		default:

				if ($type=="object"){
					$return = json_decode(json_encode((array)$return),true);
				}
				test_array($return);
			break;
		case "string":
			if ($output=="error_log"){
				error_log(print_r($return,true));
			} else {
				test_string($return);
			}
			break;


	}



}

function test_array($array) {
	header("Content-Type: application/json");
	$f3 = \Base::instance();
	$f3->set("__testJson",true);
	echo json_encode($array);
	exit();
}

function test_string($array) {
	header("Content-Type: text/html");
	$f3 = \Base::instance();
	$f3->set("__testString",true);
	echo $array;
	exit();
}

function bt_loop($trace) {
	if (isset($trace['object'])) unset($trace['object']);
	if (isset($trace['type'])) unset($trace['type']);


	$args = array();
	foreach ($trace['args'] as $arg) {
		if (is_array($arg)) {
			if (count($arg) < 5) {
				$args[] = $arg;
			} else {
				$args[] = "Array " . count($arg);
			}

		} else {
			$args[] = $arg;
		}

	}
	$trace['args'] = $args;

	return $trace;
}
function xml_encode(array $arr, string $name_for_numeric_keys = 'val'): string {
	if (empty ( $arr )) {
		// avoid having a special case for <root/> and <root></root> i guess
		return '';
	}

	$arr = json_decode(json_encode($arr),true);

	$is_iterable_compat = function ($v): bool {
		// php 7.0 compat for php7.1+'s is_itrable
		return is_array ( $v ) || ($v instanceof \Traversable);
	};
	$isAssoc = function (array $arr): bool {
		// thanks to Mark Amery for this
		if (array () === $arr)
			return false;
		return array_keys ( $arr ) !== range ( 0, count ( $arr ) - 1 );
	};
	$endsWith = function (string $haystack, string $needle): bool {
		// thanks to MrHus
		$length = strlen ( $needle );
		if ($length == 0) {
			return true;
		}
		return (substr ( $haystack, - $length ) === $needle);
	};
	$formatXML = function (string $xml) use ($endsWith): string {
		// there seems to be a bug with formatOutput on DOMDocuments that have used importNode with $deep=true
		// on PHP 7.0.15...
		$domd = new DOMDocument ( '1.0', 'UTF-8' );
		$domd->preserveWhiteSpace = false;
		$domd->formatOutput = true;
		$domd->loadXML ( '<root>' . $xml . '</root>' );
		$ret = trim ( $domd->saveXML ( $domd->getElementsByTagName ( "root" )->item ( 0 ) ) );
		assert ( 0 === strpos ( $ret, '<root>' ) );
		assert ( $endsWith ( $ret, '</root>' ) );
		$full = trim ( substr ( $ret, strlen ( '<root>' ), - strlen ( '</root>' ) ) );
		$ret = '';
		// ... seems each line except the first line starts with 2 ugly spaces,
		// presumably its the <root> element that starts with no spaces at all.
		foreach ( explode ( "\n", $full ) as $line ) {
			if (substr ( $line, 0, 2 ) === '  ') {
				$ret .= substr ( $line, 2 ) . "\n";
			} else {
				$ret .= $line . "\n";
			}
		}
		$ret = trim ( $ret );
		return $ret;
	};

	// $arr = new RecursiveArrayIterator ( $arr );
	// $iterator = new RecursiveIteratorIterator ( $arr, RecursiveIteratorIterator::SELF_FIRST );
	$iterator = $arr;
	$domd = new DOMDocument ();
	$root = $domd->createElement ( 'root' );
	foreach ( $iterator as $key => $val ) {

		if (is_int ( $key )) {
		//	debug($name_for_numeric_keys,$key);
		}

		// var_dump ( $key, $val );
		$ele = $domd->createElement ( is_int ( $key ) ? $name_for_numeric_keys : $key );
		if (! empty ( $val ) || $val === '0') {
			if ($is_iterable_compat ( $val )) {
				$asoc = $isAssoc ( $val );
				$tmp = xml_encode ( $val, is_int ( $key ) ? $name_for_numeric_keys : $key );
				// var_dump ( $tmp );
				// die ();
				$tmp = @DOMDocument::loadXML ( '<root>' . $tmp . '</root>' );
				foreach ( $tmp->getElementsByTagName ( "root" )->item ( 0 )->childNodes ?? [ ] as $tmp2 ) {
					$tmp3 = $domd->importNode ( $tmp2, true );
					if ($asoc) {
						$ele->appendChild ( $tmp3 );
					} else {
						$root->appendChild ( $tmp3 );
					}
				}
				unset ( $tmp, $tmp2, $tmp3 );
				if (! $asoc) {
					// echo 'REMOVING';die();
					// $ele->parentNode->removeChild($ele);
					continue;
				}
			} else {
				$ele->textContent = $val;
			}
		}
		$root->appendChild ( $ele );
	}
	$domd->preserveWhiteSpace = false;
	$domd->formatOutput = true;
	$ret = trim ( $domd->saveXML ( $root ) );
	assert ( 0 === strpos ( $ret, '<root>' ) );
	assert ( $endsWith ( $ret, '</root>' ) );
	$ret = trim ( substr ( $ret, strlen ( '<root>' ), - strlen ( '</root>' ) ) );
	// seems to be a bug with formatOutput on DOMDocuments that have used importNode with $deep=true..
	$ret = $formatXML ( $ret );
	return $ret;
}


function makeNested($source_) {
	$source = array();
	foreach($source_ as $item){
		$source[$item['ID']] = $item;
	}



    $nested = array();
    foreach ( $source as &$s ) {
        if ( is_null($s['parentID']) || $s['parentID']=="0") {
            // no parent_id so we put it in the root of the array
            $nested[] = &$s;
        } else {
            $pid = $s['parentID'];
            if ( isset($source[$pid]) ) {
                // If the parent ID exists in the source array
                // we add it to the 'children' array of the parent after initializing it.

                if ( !isset($source[$pid]['sub']) ) {
                    $source[$pid]['sub'] = array();
                }

                $source[$pid]['sub'][] = &$s;
            }
        }
    }
    return $nested;
}

function arrayRecursiveDiff($aArray1, $aArray2) {
	$aReturn = array();

	foreach ($aArray1 as $mKey => $mValue) {
		if (array_key_exists($mKey, $aArray2)) {
			if (is_array($mValue)) {
				$aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
				if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
			} else {
				if ($mValue != $aArray2[$mKey]) {
					$aReturn[$mKey] = $mValue;
				}
			}
		} else {
			$aReturn[$mKey] = $mValue;
		}
	}
	return $aReturn;
}
