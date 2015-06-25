<?php

namespace App\Services\Import;

class ImportLaravel extends ImportAbstract
{
	public function exportSheetToDestination($dir, $lang, $local, $data)
	{
		$path = $dir . '/' . $lang . '/' . $local . '.php';
		@mkdir(pathinfo($path, PATHINFO_DIRNAME));

		// invert array_dot
		$array = array();
		foreach ($data as $key => $value)
		{
			array_set($array, $key, $value);
		}

		file_put_contents($path, '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($array, true) . ';' . PHP_EOL);
	}
}
