<?php

class JConfig
{
	// Database settings
	public $dbtype   = 'pdomysql';
	public $host     = 'localhost';
	public $user     = 'root';
	public $password = '';
	public $db       = 'windwalker_test';
	public $dbprefix = 'wind_';

	// Paths
	public $log_path = '../log';
	public $tmp_path = '../tmp';

	// Cache settings
	public $caching       = '0';
	public $cache_handler = 'file';
	public $cachetime     = '15';

	// Error reporting settings
	public $error_reporting = 'development';

	// Debug settings
	public $debug      = '1';
	public $debug_lang = '0';

	// Site Information
	public $sitename = '{{Site Name}}';
	public $fromname = '{{Site Name}}';
	public $MetaDesc = 'A site by Joomla CMF';
	public $MetaKeys = 'joomla, cmf';

	// Other config
	public $offline = '0';
	public $offline_message = 'This site is down for maintenance.<br /> Please check back again soon.';
	public $display_offline_message = '1';
	public $offline_image = '';
	public $editor = 'tinymce';
	public $captcha = '0';
	public $list_limit = '100';
	public $access = '1';
	public $live_site = '';
	public $secret = 'vbmm04985fj034fj8504hy9yt54y';
	public $gzip = '0';
	public $helpurl = 'http://help.joomla.org/proxy/index.php?option=com_help&keyref=Help{major}{minor}:{keyref}';
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = '0';
	public $offset = 'Asia/Taipei';
	public $mailonline = '1';
	public $mailer = 'mail';
	public $mailfrom = 'test@windwalker.io';
	public $sendmail = '/usr/sbin/sendmail';
	public $smtpauth = '0';
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';
	public $smtpsecure = 'none';
	public $smtpport = '25';
	public $MetaTitle = '1';
	public $MetaAuthor = '1';
	public $MetaVersion = '0';
	public $robots = '';
	public $sef = '1';
	public $sef_rewrite = '1';
	public $sef_suffix = '1';
	public $unicodeslugs = '1';
	public $feed_limit = '100';
	public $lifetime = '150';
	public $session_handler = 'database';
	public $MetaRights = '';
	public $sitename_pagetitles = '0';
	public $force_ssl = '0';
	public $frontediting = '1';
	public $feed_email = 'author';
	public $cookie_domain = '';
	public $cookie_path = '';
	public $asset_id = '1';
	public $language = 'en-GB';
	public $useStrongEncryption = '1';

	public function __construct()
	{
		$this->log_path = JPATH_ROOT . '/log';
		$this->tmp_path = JPATH_ROOT . '/tmp';
	}
}