<?php

namespace App\Services\Import;

class ImportLaravel extends ImportAbstract
{
	public function exportSheetToDestination($dir, $lang, $local, $data)
	{
		$path = $dir . '/' . $lang . '/' . $local . '.php';
		@mkdir(pathinfo($path, PATHINFO_DIRNAME));

		file_put_contents($path, '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($data, true) . ';' . PHP_EOL);
	}
}
