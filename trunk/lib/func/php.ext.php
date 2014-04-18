<?php
if (! function_exists ( 'http_response_code' )) {
	/**
	 * Get or Set the HTTP response code
	 *
	 * @link http://www.php.net/manual/en/function.http-response-code.php
	 * @param response_code int[optional] <p>The optional response_code will set the response code.</p><p>]]></p>
	 * @return int The current response code. By default the return value is int(200).
	 */
	function http_response_code($code = NULL) {
		if ($code !== NULL) {

			switch ($code) {
 				case 100 : $text = 'Continue'; break;
				case 101 : $text = 'Switching Protocols'; break;
				case 200 : $text = 'OK'; break;
				case 201 : $text = 'Created'; break;
				case 202 : $text = 'Accepted'; break;
				case 203 : $text = 'Non-Authoritative Information'; break;
				case 204 : $text = 'No Content'; break;
				case 205 : $text = 'Reset Content'; break;
				case 206 : $text = 'Partial Content'; break;
				case 300 : $text = 'Multiple Choices'; break;
				case 301 : $text = 'Moved Permanently'; break;
				case 302 : $text = 'Moved Temporarily'; break;
				case 303 : $text = 'See Other'; break;
				case 304 : $text = 'Not Modified'; break;
				case 305 : $text = 'Use Proxy'; break;
				case 400 : $text = 'Bad Request'; break;
				case 401 : $text = 'Unauthorized'; break;
				case 402 : $text = 'Payment Required'; break;
				case 403 : $text = 'Forbidden'; break;
				case 404 : $text = 'Not Found'; break;
				case 405 : $text = 'Method Not Allowed'; break;
				case 406 : $text = 'Not Acceptable'; break;
				case 407 : $text = 'Proxy Authentication Required'; break;
				case 408 : $text = 'Request Time-out'; break;
				case 409 : $text = 'Conflict'; break;
				case 410 : $text = 'Gone'; break;
				case 411 : $text = 'Length Required'; break;
				case 412 : $text = 'Precondition Failed'; break;
				case 413 : $text = 'Request Entity Too Large'; break;
				case 414 : $text = 'Request-URI Too Large'; break;
				case 415 : $text = 'Unsupported Media Type'; break;
				case 500 : $text = 'Internal Server Error'; break;
				case 501 : $text = 'Not Implemented'; break;
				case 502 : $text = 'Bad Gateway'; break;
				case 503 : $text = 'Service Unavailable'; break;
				case 504 : $text = 'Gateway Time-out'; break;
				case 505 : $text = 'HTTP Version not supported'; break;
				default  :exit ( 'Unknown http status code "' . htmlentities ( $code ) . '"' ); break;
			}

			$protocol = (isset ( $_SERVER ['SERVER_PROTOCOL'] ) ? $_SERVER ['SERVER_PROTOCOL'] : 'HTTP/1.0');

			header ( $protocol . ' ' . $code . ' ' . $text );

			$GLOBALS ['http_response_code'] = $code;
		} else {
			$code = (isset ( $GLOBALS ['http_response_code'] ) ? $GLOBALS ['http_response_code'] : 200);
		}

		return $code;
	}
}

if (! function_exists ( 'array_column' )) {
	/**
	 *
	 * @param unknown $input
	 * @param unknown $column_key
	 * @param string $index_key
	 * @return multitype: Ambigous multitype:unknown >
	 */
	function array_column($input, $column_key, $index_key = null) {
		if ($index_key !== null) {
			// Collect the keys
			$keys = array ();
			$i = 0; // Counter for numerical keys when key does not exist

			foreach ( $input as $row ) {
				if (array_key_exists ( $index_key, $row )) {
					// Update counter for numerical keys
					if (is_numeric ( $row [$index_key] ) || is_bool ( $row [$index_key] )) {
						$i = max ( $i, ( int ) $row [$index_key] + 1 );
					}

					// Get the key from a single column of the array
					$keys [] = $row [$index_key];
				} else {
					// The key does not exist, use numerical indexing
					$keys [] = $i ++;
				}
			}
		}

		if ($column_key !== null) {
			// Collect the values
			$values = array ();
			$i = 0; // Counter for removing keys

			foreach ( $input as $row ) {
				if (array_key_exists ( $column_key, $row )) {
					// Get the values from a single column of the input array
					$values [] = $row [$column_key];
					$i ++;
				} elseif (isset ( $keys )) {
					// Values does not exist, also drop the key for it
					array_splice ( $keys, $i, 1 );
				}
			}
		} else {
			// Get the full arrays
			$values = array_values ( $input );
		}

		if ($index_key !== null) {return array_combine ( $keys, $values );}

		return $values;
	}
}

if (! function_exists ( 'hex2bin' )) {
	/**
	 * Convert hex to binary
	 * @link http://www.php.net/manual/en/function.hex2bin.php
	 * @param data string <p>Hexadecimal representation of data.</p>
	 * @return string the binary representation of the given data.
	 */
	function hex2bin($data) {
		static $old;
		if ($old === null) {
		$old = version_compare ( PHP_VERSION, '5.2', '<' );
		}
		$isobj = false;
		if (is_scalar ( $data ) || (($isobj = is_object ( $data )) && method_exists ( $data, '__toString' ))) {
			if ($isobj && $old) { // FIXME OB 사용하면 안됨...
				ob_start ();
				echo $data;
				$data = ob_get_clean ();
			} else {
				$data = ( string ) $data;
			}
		} else {
			trigger_error ( __FUNCTION__ . '() expects parameter 1 to be string, ' . gettype ( $data ) . ' given', E_USER_WARNING );
			return; // null in this case
		}
		$len = strlen ( $data );
		if ($len % 2) {
			trigger_error ( __FUNCTION__ . '(): Hexadecimal input string must have an even length', E_USER_WARNING );
			return false;
		}
		if (strspn ( $data, '0123456789abcdefABCDEF' ) != $len) {
			trigger_error ( __FUNCTION__ . '(): Input string must be hexadecimal string', E_USER_WARNING );
			return false;
		}
		return pack ( 'H*', $data );
	}
}

if (! function_exists ( 'array_replace' )) {
	/**
	 * Replaces elements from passed arrays into the first array
	 *
	 * @link http://www.php.net/manual/en/function.array-replace.php
	 * @param $array array <p>The array in which elements are replaced.</p>
	 * @param $array1 array <p>The array from which elements will be extracted.</p>
	 * @param _ array[optional]
	 * @return array an array, or &null; if an error occurs.
	 */
	function array_replace(array &$array, array &$array1, $filterEmpty = false) {
		$args = func_get_args ();
		$count = func_num_args () - 1;

		for($i = 0; $i < $count; ++ $i) {
			if (is_array ( $args [$i] )) {
				foreach ( $args [$i] as $key => $val ) {
					if ($filterEmpty && empty ( $val )) continue;
					$array [$key] = $val;
				}
			} else {
				trigger_error ( __FUNCTION__ . '(): Argument #' . ($i + 1) . ' is not an array', E_USER_WARNING );
				return NULL;
			}
		}

		return $array;
	}
}

if (! function_exists ( 'array_replace_recursive' )) {
	/**
	 * Replaces elements from passed arrays into the first array recursively
	 *
	 * @link http://www.php.net/manual/en/function.array-replace-recursive.php
	 * @param $array array <p>The array in which elements are replaced.</p>
	 * @param $array1 array <p>The array from which elements will be extracted.</p>
	 * @return array an array, or &null; if an error occurs.
	 */
	function array_replace_recursive($array, $array1) {
		function recurse($array, $array1) {
			foreach ( $array1 as $key => $value ) {
				// create new key in $array, if it is empty or not an array
				if (! isset ( $array [$key] ) || (isset ( $array [$key] ) && ! is_array ( $array [$key] ))) {
					$array [$key] = array ();
				}

				// overwrite the value in the base array
				if (is_array ( $value )) {
					$value = recurse ( $array [$key], $value );
				}
				$array [$key] = $value;
			}
			return $array;
		}

		// handle the arguments, merge one by one
		$args = func_get_args ();
		$array = $args [0];
		if (! is_array ( $array )) {return $array;}
		for($i = 1; $i < count ( $args ); $i ++) {
			if (is_array ( $args [$i] )) {
				$array = recurse ( $array, $args [$i] );
			}
		}
		return $array;
	}
}

if (! function_exists ( 'class_alias' )) {
	/**
	 * Creates an alias for a class
	 *
	 * @link http://www.php.net/manual/en/function.class-alias.php
	 * @param $original string[optional] <p>The original class.</p>
	 * @param $alias string[optional] <p>The alias name for the class.</p>
	 * @return bool Returns true on success or false on failure.
	 */
	function class_alia2s($original, $alias) {
		eval ( 'abstract class ' . $alias . ' extends ' . $original . ' {}' );
	}
}

if (false === function_exists ( 'lcfirst' )) {
	/**
	 * Make a string's first character lowercase
	 *
	 * @link http://www.php.net/manual/en/function.lcfirst.php
	 * @param $str string <p>The input string.</p>
	 * @return string the resulting string.
	 */
	function lcfirst($str) {
		return ( string ) (strtolower ( substr ( $str, 0, 1 ) ) . substr ( $str, 1 ));
	}
}

if (! function_exists ( 'parse_ini_string' )) {
	/**
	 * Parse a configuration string
	 *
	 * @link http://www.php.net/manual/en/function.parse-ini-string.php
	 * @param $string string <p> The contents of the ini file being parsed.</p>
	 * @param $process_sections bool[optional] <p>By setting the process_sections parameter to true, you get a multidimensional array, with the section names and settings included. The default for process_sections is false</p>
	 * @return array The settings are returned as an associative array on success,
	 *         and false on failure.
	 */
	function parse_ini_string($string, $process_sections) {
		if (! class_exists ( 'parse_ini_filter' )) {
			/* Define our filter class */
			class parse_ini_filter extends php_user_filter {
				static $buf = '';
				function filter($in, $out, &$consumed, $closing) {
					$bucket = stream_bucket_new ( fopen ( 'php://memory', 'wb' ), self::$buf );
					stream_bucket_append ( $out, $bucket );
					return PSFS_PASS_ON;
				}
			}
			/* Register our filter with PHP */
			// stream_filter_register("parse_ini", "parse_ini_filter") or return false;
			stream_filter_register ( "parse_ini", "parse_ini_filter" );
			return false;
		}
		parse_ini_filter::$buf = $string;
		return parse_ini_file ( "php://filter/read=parse_ini/resource=php://memory", $process_sections );
	}
}

if (! function_exists ( 'str_getcsv' )) {
	function str_getcsv4($input, $delimiter = ',', $enclosure = '"') {
		if (! preg_match ( "/[$enclosure]/", $input )) {return ( array ) preg_replace ( array ("/^\\s*/","/\\s*$/" ), '', explode ( $delimiter, $input ) );}

		$token = "##";
		$token2 = "::";
		// alternate tokens "\034\034", "\035\035", "%%";
		$t1 = preg_replace ( array ("/\\\[$enclosure]/","/$enclosure{2}/","/[$enclosure]\\s*[$delimiter]\\s*[$enclosure]\\s*/","/\\s*[$enclosure]\\s*/" ), array ($token2,$token2,$token,$token ), trim ( trim ( trim ( $input ), $enclosure ) ) );

		$a = explode ( $token, $t1 );
		foreach ( $a as $k => $v ) {
			if (preg_match ( "/^{$delimiter}/", $v ) || preg_match ( "/{$delimiter}$/", $v )) {
				$a [$k] = trim ( $v, $delimiter );
				$a [$k] = preg_replace ( "/$delimiter/", "$token", $a [$k] );
			}
		}
		$a = explode ( $token, implode ( $token, $a ) );
		return ( array ) preg_replace ( array ("/^\\s/","/\\s$/","/$token2/" ), array ('','',$enclosure ), $a );
	}

	/**
	 *
	 * @link https://github.com/insteps/phputils (for updated code) Parse a CSV string into an array for php 4+.
	 * @param string $input String
	 * @param string $delimiter String
	 * @param string $enclosure String
	 * @return array
	 */
	function str_getcsv($input, $delimiter = ',', $enclosure = '"') {
		return str_getcsv4 ( $input, $delimiter, $enclosure );
	}
}

if (! function_exists ( 'quoted_printable_encode' )) {
	/**
	 * Convert a 8 bit string to a quoted-printable string
	 *
	 * @link http://www.php.net/manual/en/function.quoted-printable-encode.php
	 * @param $input string <p> The input string. </p>
	 * @return string the encoded string.
	 */
	function quoted_printable_encode($input, $line_max = 75) {
		$hex = array ('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F' );
		$lines = preg_split ( "/(?:\r\n|\r|\n)/", $input );
		$linebreak = "=0D=0A=\r\n";
		/*
		 * the linebreak also counts as characters in the mime_qp_long_line rule of spam-assassin
		 */
		$line_max = $line_max - strlen ( $linebreak );
		$escape = "=";
		$output = "";
		$cur_conv_line = "";
		$length = 0;
		$whitespace_pos = 0;
		$addtl_chars = 0;

		// iterate lines
		for($j = 0; $j < count ( $lines ); $j ++) {
			$line = $lines [$j];
			$linlen = strlen ( $line );

			// iterate chars
			for($i = 0; $i < $linlen; $i ++) {
				$c = substr ( $line, $i, 1 );
				$dec = ord ( $c );

				$length ++;

				if ($dec == 32) {
					// space occurring at end of line, need to encode
					if (($i == ($linlen - 1))) {
						$c = "=20";
						$length += 2;
					}

					$addtl_chars = 0;
					$whitespace_pos = $i;
				} elseif (($dec == 61) || ($dec < 32) || ($dec > 126)) {
					$h2 = floor ( $dec / 16 );
					$h1 = floor ( $dec % 16 );
					$c = $escape . $hex ["$h2"] . $hex ["$h1"];
					$length += 2;
					$addtl_chars += 2;
				}

				// length for wordwrap exceeded, get a newline into the text
				if ($length >= $line_max) {
					$cur_conv_line .= $c;

					// read only up to the whitespace for the current line
					$whitesp_diff = $i - $whitespace_pos + $addtl_chars;

					/*
					 * the text after the whitespace will have to be read again ( + any additional characters that came into existence as a result of the encoding process after the whitespace) Also, do not start at 0, if there was *no* whitespace in the whole line
					 */
					if (($i + $addtl_chars) > $whitesp_diff) {
						$output .= substr ( $cur_conv_line, 0, (strlen ( $cur_conv_line ) - $whitesp_diff) ) . $linebreak;
						$i = $i - $whitesp_diff + $addtl_chars;
					} else {
						$output .= $cur_conv_line . $linebreak;
					}

					$cur_conv_line = "";
					$length = 0;
					$whitespace_pos = 0;
				} else {
					// length for wordwrap not reached, continue reading
					$cur_conv_line .= $c;
				}
			} // end of for

			$length = 0;
			$whitespace_pos = 0;
			$output .= $cur_conv_line;
			$cur_conv_line = "";

			if ($j <= count ( $lines ) - 1) {
				$output .= $linebreak;
			}
		} // end for

		return trim ( $output );
	}
}

if (! function_exists ( 'array_walk_recursive' )) {
	/**
	 * Apply a user function recursively to every member of an array
	 * @link http://www.php.net/manual/en/function.array-walk-recursive.php
	 * @param $input array <p>The input array.</p>
	 * @param $funcname callback <p>
	 * Typically, funcname takes on two parameters.
	 * The input parameter's value being the first, and
	 * the key/index second.
	 * </p>
	 * <p>
	 * If funcname needs to be working with the
	 * actual values of the array, specify the first parameter of
	 * funcname as a
	 * reference. Then,
	 * any changes made to those elements will be made in the
	 * original array itself.
	 * </p>
	 * @param $userdata mixed[optional] <p>
	 * If the optional userdata parameter is supplied,
	 * it will be passed as the third parameter to the callback
	 * funcname.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	function array_walk_recursive(&$input, $funcname, $userdata = "") {
		if (! is_callable ( $funcname )) {return false;}

		if (! is_array ( $input )) {return false;}

		foreach ( $input as $key => $value ) {
			if (is_array ( $input [$key] )) {
				array_walk_recursive ( $input [$key], $funcname, $userdata );
			} else {
				$saved_value = $value;
				if (! empty ( $userdata )) {
					$funcname ( $value, $key, $userdata );
				} else {
					$funcname ( $value, $key );
				}

				if ($value != $saved_value) {
					$input [$key] = $value;
				}
			}
		}
		return true;
	}
}

if (!function_exists('json_last_error_msg')) {
    function json_last_error_msg() {
        static $errors = array(
            JSON_ERROR_NONE             => null,
            JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
            JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
            JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
        );
        $error = json_last_error();
        return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
    }
}