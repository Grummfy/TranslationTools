<?php

namespace App\Services\Import;

use Symfony\Component\Yaml\Yaml;

class ImportSymfonyYaml extends ImportAbstract
{
	public function exportSheetToDestination($dir, $lang, $local, $data)
	{
		$path = $dir . '/' . $local . '.' . $lang . '.yml';
		@mkdir(pathinfo($path, PATHINFO_DIRNAME));

		file_put_contents($path, Yaml::dump($data));
	}
}
