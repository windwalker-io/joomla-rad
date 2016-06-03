<?php
/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2016 LYRASOFT. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace Windwalker\Helper;

use JFactory;
use JFolder;
use Joomla\Uri\Uri;
use Windwalker\Data\Data;
use Windwalker\Data\DataSet;
use Windwalker\DataMapper\DataMapperFacade;
use Windwalker\DI\Container;
use Windwalker\Facade\AbstractFacade;
use Windwalker\String\Utf8String;
use Windwalker\System\JClient;

/**
 * Language Helper
 *
 * @since 2.0
 */
class LanguageHelper extends AbstractFacade
{
	/**
	 * Property The DI key.
	 *
	 * @var  string
	 */
	protected static $_key = 'language';

	/**
	 * An API key for Google translate.
	 *
	 * @var string
	 */
	const APT_KEY = 'AIzaSyC04nF4KXjfR2VQ0jsFm5vEd9LbyiXqbKw';

	/**
	 * getLocaleTag
	 *
	 * @return  string
	 * 
	 * @since   2.1.5
	 */
	public static function getLocale()
	{
		/** @var \JLanguage $lang */
		$lang = static::getInstance();

		return $lang->getTag();
	}

	/**
	 * getCurrentLanguage
	 *
	 * @return  \stdClass
	 * 
	 * @since   2.1.5
	 */
	public static function getCurrentLanguage()
	{
		$tag = static::getLocale();

		return static::getContentLanguage($tag);
	}

	/**
	 * Tries to detect the language.
	 *
	 * @return  string  locale or null if not found
	 *
	 * @since   2.1.5
	 */
	public static function detectLanguageFromBrowser()
	{
		return \JLanguageHelper::detectLanguage();
	}

	/**
	 * getLanguageProfile
	 *
	 * @param string $code
	 * @param string $key
	 *
	 * @return  \stdClass|null
	 * 
	 * @since   2.1.5
	 */
	public static function getContentLanguage($code, $key = 'lang_code')
	{
		$langs = static::getContentLanguages($key);

		return isset($langs[$code]) ? $langs[$code] : null;
	}

	/**
	 * Get available languages
	 *
	 * @param   string  $key  Array key
	 *
	 * @return  array  An array of published languages
	 * 
	 * @since   2.1.5
	 */
	public static function getContentLanguages($key = null)
	{
		if (!in_array($key, array('sef', 'lang_code')))
		{
			$key = null;
		}

		$key = $key ? : 'default';

		return \JLanguageHelper::getLanguages($key);
	}

	/**
	 * getSefPath
	 *
	 * @return  string
	 * 
	 * @since   2.1.5
	 */
	public static function getSefPath()
	{
		$lang = static::getCurrentLanguage();

		if (!$lang)
		{
			return null;
		}

		return $lang->sef;
	}

	/**
	 * getInstalledLanguages
	 *
	 * @param   integer  $client
	 *
	 * @return  DataSet|Data[]
	 * 
	 * @since   2.1.5
	 */
	public static function getInstalledLanguages($client = JClient::BOTH)
	{
		$client = strtolower($client);

		if ($client == 'site')
		{
			$client = JClient::SITE;
		}
		elseif ($client == 'admin' || $client == 'administrator')
		{
			$client = JClient::ADMINISTRATOR;
		}
		elseif ($client == 'both')
		{
			$client = JClient::BOTH;
		}

		$conditions = array('type' => 'language');

		if ($client != JClient::BOTH)
		{
			$conditions['client_id'] = $client;
		}

		$langs = DataMapperFacade::find('#__extensions', $conditions);

		return $langs;
	}

	/**
	 * Translate a long text by Google, if it too long, will separate it..
	 *
	 * @param   string  $text      String to translate.
	 * @param   string  $SourceLan Translate from this language, eg: 'zh-TW'. Empty will auto detect.
	 * @param   string  $ResultLan Translate to this language, eg: 'en'. Empty will auto detect.
	 * @param   integer $separate  Separate text by a number of words, batch translate them and recombine to return.
	 *
	 * @return  string    Translated text.
	 */
	public static function translate($text, $SourceLan = null, $ResultLan = null, $separate = 0)
	{
		// If text too big, separate it.
		if ($separate)
		{
			if (Utf8String::strlen($text) > $separate)
			{
				$text = Utf8String::str_split($text, $separate);
			}
			else
			{
				$text = array($text);
			}
		}
		else
		{
			$text = array($text);
		}

		$result = '';

		// Do translate by google translate API.
		foreach ($text as $txt)
		{
			$result .= self::gTranslate($txt, $SourceLan, $ResultLan);
		}

		return $result;
	}

	/**
	 * A method to do Google translate.
	 *
	 * @param   string $text      String to translate.
	 * @param   string $SourceLan Translate from this language, eg: 'zh-tw'. Empty will auto detect.
	 * @param   string $ResultLan Translate to this language, eg: 'en'. Empty will auto detect.
	 *
	 * @return  string|bool Translated text.
	 */
	public static function gTranslate($text, $SourceLan, $ResultLan)
	{
		$url = new Uri;

		// For Google APIv2
		$url->setHost('https://www.googleapis.com/');
		$url->setPath('language/translate/v2');

		$query['key']    = self::APT_KEY;
		$query['q']      = urlencode($text);
		$query['source'] = $SourceLan;
		$query['target'] = $ResultLan;

		if (!$text)
		{
			return false;
		}

		$url->setQuery($query);
		$url->toString();
		$response = CurlHelper::get((string) $url);

		if (empty($response->body))
		{
			return '';
		}

		$json = new \JRegistry;
		$json->loadString($response->body, 'json');

		$r = $json->get('data.translations');

		return $r[0]->translatedText;
	}

	/**
	 * Load all language files from component.
	 *
	 * @param   string $lang   Language tag.
	 * @param   string $option Component option.
	 *
	 * @return  boolean
	 */
	public static function loadAll($lang = 'en-GB', $option = null)
	{
		$folder = PathHelper::getAdmin($option) . '/language/' . $lang;

		if (is_dir($folder))
		{
			$files = JFolder::files($folder);
		}
		else
		{
			return false;
		}

		$language = static::getInstance();

		foreach ($files as $file)
		{
			$file = explode('.', $file);

			if (array_pop($file) != 'ini')
			{
				continue;
			}

			array_shift($file);

			if (count($file) != 1 && $file[1] == 'sys')
			{
				continue;
			}

			$language->load(implode('.', $file), PathHelper::getAdmin($option));
		}

		return true;
	}

	/**
	 * Load language from an extension.
	 *
	 * @param   string $ext    Extension element name, eg: com_content, plg_group_name.
	 * @param   string $client site or admin.
	 *
	 * @return  boolean
	 */
	public static function loadLanguage($ext, $client = 'site')
	{
		$lang = Container::getInstance()->get(static::getDIKey());

		return $lang->load($ext, JPATH_BASE, null, false, false)
			|| $lang->load($ext, PathHelper::get($ext, $client), null, false, false)
			|| $lang->load($ext, JPATH_BASE, null, true)
			|| $lang->load($ext, PathHelper::get($ext, $client), null, true);
	}
}
