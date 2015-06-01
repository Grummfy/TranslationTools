<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Collections\SheetCollection;
use Symfony\Component\Console\Input\InputOption;

class ImportTranslation extends Command
{
	protected $name = 'translation:import';

	protected $description = 'Import the translation from an excel to php files';

	public function fire()
	{
		$file = $this->option('file');
		$out = rtrim($this->option('out'), '/');
		$locals = $this->option('local');
		$langs = $this->option('lang');

		if ($file == null)
		{
			$this->error('The file parameter is mandatory.');

			return 1;
		}

		if ($out == null)
		{
			$this->error('The out parameter is mandatory.');

			return 1;
		}

		if (empty($langs))
		{
			$this->error('The language parameter is mandatory.');

			return 1;
		}

		if (empty($locals))
		{
			/* @var $data \Maatwebsite\Excel\Readers\LaravelExcelReader */
			$data = \Excel::load($file);

			// XXX avoid range error like [PHPExcel_Exception] Row 2 is out of range (2 - 1)
			$sheetNumber = $data->getExcel()->getSheetCount();
			$data->setSelectedSheetIndices(range(1, $sheetNumber));

			$data = $data->get();
		}
		else
		{
			$data = \Excel::selectSheets($locals)->load($file)->get();
		}

		if ($data instanceof SheetCollection)
		{
			$t = [];
			foreach ($data as $sheet)
			{
				$t[ $sheet->getTitle() ] = $sheet->toArray();
			}
			$data = $t;
		}
		else
		{
			$data = [$data->getTitle() => $data->toArray()];
		}

		$i = 0;
		foreach ($data as $local => $sheet)
		{
			if ($local == '__info_')
			{
				continue;
			}

			$this->output->write('processing ' . $local);

			$export = [];
			foreach ($sheet as $row)
			{
				foreach ($langs as $lang)
				{
					$export[ $lang ][ $row[ 'key' ] ] = $row[ $lang ];
				}
			}

			foreach ($export as $lang => $e)
			{
				// export it
				$this->_export($out, $lang, $local, $e);
			}
			$this->info(' Done');
			$i++;
		}

		$this->info($i . ' local(s) processed');
	}

	protected function getOptions()
	{
		return [
			['file', 'f', InputOption::VALUE_REQUIRED, 'the path to the file to analyse'],
			['local', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The translations to import (if not present, all)'],
			['lang', 'l', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The languages to import'],
			['out', 'o', InputOption::VALUE_REQUIRED, 'The output directory name'],
		];
	}

	protected function _export($dir, $lang, $local, $data)
	{
		// assure order of key => easier to compare on diff
		ksort($data);

		$path = $dir . '/' . $lang . '/' . $local . '.php';
		@mkdir(pathinfo($path, PATHINFO_DIRNAME));

		$array = array();
		foreach ($data as $key => $value)
		{ // convert dot notation in real array
			array_set($array, $key, $value);
		}

		file_put_contents($path, '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($array, true) . ';' . PHP_EOL);
	}
}
