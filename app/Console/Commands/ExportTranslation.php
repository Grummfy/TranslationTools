<?php namespace App\Console\Commands;

use App\Services\Export\ExportLaravel;
use App\Services\Export\ExportSymfonyYaml;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Symfony\Component\Console\Input\InputOption;

class ExportTranslation extends Command
{
	protected $name = 'translation:export';

	protected $description = 'Export the translation to an excel';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$dir = rtrim($this->option('dir'), '/');
		$out = $this->option('out');
		$translations = $this->option('local');
		$langs = $this->option('lang');
		$ref = $this->option('ref');
		$type = $this->option('type');

		if ($dir == null)
		{
			$this->error('The dir parameter is mandatory.');

			return 1;
		}

		if (empty($langs))
		{
			$this->error('The lang parameter is mandatory.');

			return 1;
		}

		switch ($type)
		{
			default:
			case 'laravel':
				$exporter = new ExportLaravel();
				break;
			case 'sf_yml':
				$exporter = new ExportSymfonyYaml();
				break;
		}

		if (empty($translations)) // if no local given we get all the locals of the reference.
		{
			$translations = $exporter->getTranslations($dir, $ref);
		}

		$data = $exporter->loadDatas($translations, $langs, $dir, $ref);

		$filename = pathinfo($out, PATHINFO_BASENAME);
		$dirname = pathinfo($out, PATHINFO_DIRNAME);

		$exporter->saveToFile($dirname, $filename, $data);
	}

	protected function getOptions()
	{
		return [
			['dir', 'd', InputOption::VALUE_REQUIRED, 'the path to the directory to analyse'],
			['local', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The translations file to use (if not present, all)'],
			['lang', 'l', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The languages to analyse'],
			['out', 'o', InputOption::VALUE_REQUIRED, 'The output file name (without extention)', 'local'],
			['ref', null, InputOption::VALUE_REQUIRED, 'The lang to use as reference', 'ref'],
			['type', 't', InputOption::VALUE_REQUIRED, 'The type of import: laravel, sf_yml'],
		];
	}
}
