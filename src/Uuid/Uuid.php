<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Windwalker\UUID;

/**
 * Simple UUID class.
 *
 * The following class generates VALID RFC 4122 COMPLIANT
 * Universally Unique IDentifiers (UUID) version 3, 4 and 5.
 *
 * UUIDs generated validates using OSSP UUID Tool, and output
 * for named-based UUIDs are exactly the same. This is a pure
 * PHP implementation.
 *
 * Based on Andrew Moore's work in php.net
 *
 * @link   https://gist.github.com/dahnielson/508447
 */
class Uuid
{
	/**
	 * When this namespace is specified, the name string is a fully-qualified domain name.
	 *
	 * @link   http://tools.ietf.org/html/rfc4122#appendix-C
	 * @const  string
	 */
	const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * When this namespace is specified, the name string is a URL.
	 *
	 * @link   http://tools.ietf.org/html/rfc4122#appendix-C.
	 * @const  string
	 */
	const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * When this namespace is specified, the name string is an ISO OID.
	 *
	 * @link   http://tools.ietf.org/html/rfc4122#appendix-C
	 * @const  string
	 */
	const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * When this namespace is specified, the name string is an X.500 DN in DER or a text output format.
	 *
	 * @link   http://tools.ietf.org/html/rfc4122#appendix-C
	 * @const  string
	 */
	const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

	/**
	 * The nil UUID is special form of UUID that is specified to have all 128 bits set to zero.
	 *
	 * @link   http://tools.ietf.org/html/rfc4122#section-4.1.7
	 * @const  string
	 */
	const NIL = '00000000-0000-0000-0000-000000000000';

	/**
	 * Generate v3 UUID
	 * Version 3 UUIDs are named based. They require a namespace (another
	 * valid UUID) and a value (the name). Given the same namespace and
	 * name, the output is always the same.
	 *
	 * @param  uuid   $namespace The namespace from other UUID.
	 * @param  string $name      The value to generate UUID.
	 *
	 * @return string UUID v3.
	 */
	public static function v3($namespace, $name)
	{
		if (!self::isValid($namespace))
		{
			return false;
		}

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-', '{', '}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for ($i = 0; $i < strlen($nhex); $i += 2)
		{
			$nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
		}

		// Calculate hash value
		$hash = md5($nstr . $name);

		return sprintf('%08s-%04s-%04x-%04x-%12s',

			// 32 bits for "time_low"
			substr($hash, 0, 8),

			// 16 bits for "time_mid"
			substr($hash, 8, 4),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 3
			(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

			// 48 bits for "node"
			substr($hash, 20, 12)
		);
	}

	/**
	 * Generate v4 UUID.
	 *
	 * Version 4 UUIDs are pseudo-random.
	 *
	 * @return string UUID v4.
	 */
	public static function v4()
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),

			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,

			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	/**
	 * Generate v5 UUID
	 * Version 5 UUIDs are named based. They require a namespace (another
	 * valid UUID) and a value (the name). Given the same namespace and
	 * name, the output is always the same.
	 *
	 * @param  uuid   $namespace The namespace from other UUID.
	 * @param  string $name      The value to generate UUID.
	 *
	 * @return string UUID v5.
	 */
	public static function v5($namespace, $name)
	{
		if (!self::isValid($namespace))
		{
			return false;
		}

		// Get hexadecimal components of namespace
		$nhex = str_replace(array('-', '{', '}'), '', $namespace);

		// Binary Value
		$nstr = '';

		// Convert Namespace UUID to bits
		for ($i = 0; $i < strlen($nhex); $i += 2)
		{
			$nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
		}

		// Calculate hash value
		$hash = sha1($nstr . $name);

		return sprintf('%08s-%04s-%04x-%04x-%12s',

			// 32 bits for "time_low"
			substr($hash, 0, 8),

			// 16 bits for "time_mid"
			substr($hash, 8, 4),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 5
			(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

			// 48 bits for "node"
			substr($hash, 20, 12)
		);
	}

	/**
	 * Validate UUID.
	 *
	 * @param string $uuid The UUID string.
	 *
	 * @return  boolean Valid or fail.
	 */
	public static function isValid($uuid)
	{
		return preg_match(
			'/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' .
			'[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i',
			$uuid
		) === 1;
	}
}
