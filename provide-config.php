<?php

/**
 * This script takes configuration file passed through as an environment variable INPUT_CONFIG_FILE
 * and replaces secret placeholders for a real values passed through as env variables in 'remote', 'user' and 'password' keys
 * E.g. in 'remote' key with value sftp://{{SECRET_USER}}:{{SECRET_PASSWORD}@hostname.example.com/dir
 * {{SECRET_USER}} is replaced for env variable SECRET_USER (if set), and
 * {{SECRET_PASSWORD}} is replaced for env variable SECRET_PASSWORD (if set)
 */

$filename = getenv('INPUT_CONFIG_FILE');
if ($filename === FALSE) {
	echo "Environment variable INPUT_CONFIG_FILE was not set. Please check that you provided config_file in your GitHub Actions yml file or that you passed the env variable to your docker container.";
	exit(1);
}
if (!file_exists($filename)) {
	echo "Filename provided in environment variable INPUT_CONFIG_FILE {$filename} does not exist. Please check your path.";
	exit(1);
}
if (pathinfo($filename, PATHINFO_EXTENSION) == 'php') {
	$p = include $filename;
} else {
	$p = parse_ini_file($filename, true);
}

$secret = replace_secrets($p);
if (array_key_exists('ignore', $secret)) {
	$secret['ignore'] .= "\n/provide-config.php";
} else {
	$secret['ignore'] = "provide-config.php";
}

return $secret;

/* ******************* FUNCTIONS ******************* */

function replace_secrets(array $ini): array
{
	$koi = array_search_deep($ini, ['remote', 'user', 'password']);
	$pattern = '/{{([a-zA-Z0-9_\-]+)}}/';
	foreach ($koi as $key) {
		$value = get_deep_key($ini, $key);
		preg_match_all($pattern, $value, $matches);
		if (array_key_exists(1, $matches)) {
			foreach ($matches[1] as $m) {
				$env = getenv($m);
				if ($env !== FALSE) {
					$value = str_replace('{{' . $m . '}}', $env, $value);
					set_deep_key($ini, $key, $value);
				}
			}
		}
	}

	return $ini;
}

/**
 * Search for all the key "addresses" with last key same as one of $keys
 * And address of item is an array of string, each string is one key down the path
 * E.g. address ['section', 'subsection', 'item'] represents an item $array['section']['subsection']['item']
 */
function array_search_deep(array $array, array $keys = []): array
{
	$retval = [];

	foreach ($array as $k => $value) {
		if (is_array($value)) {
			$levelDown = array_search_deep($value, $keys);
			foreach ($levelDown as $kk => $l) {
				if (in_array($l, $keys)) {
					$retval[] = [$k, $l];
				}
			}
		} else {
			if (in_array($k, $keys)) {
				$retval[] = $k;
			}
		}
	}

	return $retval;
}

/**
 * Returns the value of multidimensional array item specified by address $key
 *
 * @param array $array
 * @param string|array $key
 * @return mixed
 */
function get_deep_key(array $array, $key)
{
	if (is_string($key)) {
		$key = [$key];
	}

	$first = array_shift($key);
	return !empty($key) ? get_deep_key($array[$first], $key) : $array[$first];
}

/**
 * Sets the value of multidimensional array item specified by address $key
 *
 * @param array $array
 * @param string|array $key
 * @param mixed $value
 */
function set_deep_key(array &$array, $key, $value)
{
	if (is_string($key)) {
		$key = [$key];
	}

	$first = array_shift($key);
	if (!empty($key)) {
		set_deep_key($array[$first], $key, $value);
	} else {
		$array[$first] = $value;
	}
}