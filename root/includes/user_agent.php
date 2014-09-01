<?php

/*
	Returns relative path to icon
*/
function ua_get_filename($name, $folder)
{
	if (substr($name, 0, 7) == 'Nieznan')
		return '/foro/images/user_agent/desconocido.png';

	$name = strtolower($name);
	$name = str_replace(' ', '', $name); // remove spaces
	$name = preg_replace('/[^a-z0-9_]/', '', $name); // remove special characters
	return 'images/user_agent/'.$folder.'/'.$name.'.png';
}

/*
	Returns first found element in $useragent from $items array
*/
function ua_search_for_item($items, $useragent)
{
	$result = '';
	
	foreach ($items as $item)
	{
		if (strpos($useragent, strtolower($item)) !== false)
		{
			$result = $item;
			break;
		}
	}
	return $result;
}

/*
	Main function detecting browser and system
*/
function get_useragent_names($useragent)
{
	if (!$useragent)
	{
		$result = array(
			'system'			=> 'Desconocido',
			'browser'			=> 'Desconocido',
			'browser_version'	=> ''
		);
		return $result;
	}

	$useragent = strtolower($useragent);
	
	// Browser detection
	$browsers = array('AWeb', 'Camino', 'Epiphany', 'Galeon', 'HotJava', 'iCab', 'MSIE', 'Chrome', 'Safari', 'Konqueror', 'Flock', 'Iceweasel', 'SeaMonkey', 'Firefox', 'Firebird', 'Netscape', 'Mozilla', 'Opera', 'Maxthon', 'PhaseOut', 'SlimBrowser');
	
	$browser = ua_search_for_item($browsers, $useragent);

	$browser_version = '';
	if ($browser != '')
	{
		if ($browser == 'Opera' && strpos($useragent, 'version') !== false)
			$browser_version = substr($useragent, strpos($useragent, 'version') + 8);
		else
			$browser_version = substr($useragent, strpos($useragent, strtolower($browser)) + strlen($browser) + 1);
		
		if (preg_match('/([0-9\.]*)/', $browser_version, $matches))
			$browser_version = $matches[1];
	}

	// Detect IE version
	if ($browser == 'MSIE')
	{
		$browser = 'Internet Explorer';
		if ($browser_version != '')
		{
			$ver = substr($browser_version, 0, 1);
			
			if ($ver >= 6)
			{
				$browser .= ' '. $ver;
				$browser_version = '';
			}
		}
	}
	
	// System detection
	$systems = array('Amiga', 'BeOS', 'FreeBSD', 'HP-UX', 'Linux', 'NetBSD', 'OS/2', 'SunOS', 'Symbian', 'Unix', 'Windows', 'Sun', 'Macintosh', 'Mac');
	
	$system = ua_search_for_item($systems, $useragent);
	
	if ($system == 'Linux')
	{
		$systems = array('CentOS', 'Debian', 'Fedora', 'Freespire', 'Gentoo', 'Katonix', 'KateOS', 'Knoppix', 'Kubuntu', 'Linspire', 'Mandriva', 'Mandrake', 'RedHat', 'Slackware', 'Slax', 'Suse', 'Xubuntu', 'Ubuntu', 'Xandros', 'Arch', 'Ark');

		$system = ua_search_for_item($systems, $useragent);
		if ($system == '')
			$system = 'Linux';
		
		if ($system == 'Mandrake')
			$system = 'Mandriva';
	}
	elseif ($system == 'Windows')
	{
		$version = substr($useragent, strpos($useragent, 'windows nt ') + 11);
		if (substr($version, 0, 3) == 5.1)
			$system = 'Windows XP';
		elseif (substr($version, 0, 1) == 6)
		{
			if (substr($version, 0, 3) == 6.0)
				$system = 'Windows Vista';
			else
				$system = 'Windows 7';
		}
	}
	elseif ($system == 'Mac')
		$system = 'Macintosh';

	if (!$system)
		$system = 'Desconocido';
	if (!$browser)
		$browser = 'Desconocido';

	$result = array(
		'system'			=> $system,
		'browser'			=> $browser,
		'browser_version'	=> $browser_version
	);

	return $result;
}

/*
	Displays icons
*/
function get_useragent_icons($useragent)
{
	$agent = get_useragent_names($useragent);

	$result = '<img src="'.ua_get_filename($agent['system'], 'os').'" style="cursor: pointer" title="'.htmlspecialchars($agent['system']).'" alt="'.htmlspecialchars($agent['system']).'" />&nbsp;';
	$result .= '<img src="'.ua_get_filename($agent['browser'], 'browser').'" style="cursor: pointer" title="'.htmlspecialchars($agent['browser'].' '.$agent['browser_version']).'" alt="'.htmlspecialchars($agent['browser']).'" />';

	$description = addslashes($useragent) . '\n\nS.Oper:\t\t' . addslashes($agent['system']);
	$description .= '\nNavegador:\t' . addslashes($agent['browser'].' '.$agent['browser_version']);

	return '<span class="user-agent" onclick="alert(\'' . htmlspecialchars($description) . '\')">' . $result . '</span>';
}
