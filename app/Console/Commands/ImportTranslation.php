<?php namespace App\Console\Commands;

use App\Services\Import\ImportLaravel;
use App\Services\Import\ImportSymfonyYaml;
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
		$type = $this->option('type');

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

		switch ($type)
		{
			default:
			case 'laravel':
				$importer = new ImportLaravel();
				break;
			case 'sf_yml':
				$importer = new ImportSymfonyYaml();
				break;
		}
		$importer->loadFile($file, $out, $langs, $locals);
	}

	protected function getOptions()
	{
		return [
			['file', 'f', InputOption::VALUE_REQUIRED, 'the path to the file to analyse'],
			['local', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The translations to import (if not present, all)'],
			['lang', 'l', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'The languages to import'],
			['out', 'o', InputOption::VALUE_REQUIRED, 'The output directory name'],
			['type', 't', InputOption::VALUE_REQUIRED, 'The type of import: laravel, sf_yml', 'laravel'],
		];
	}
}
