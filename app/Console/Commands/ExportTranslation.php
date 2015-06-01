<?php namespace App\Console\Commands;

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
		$locals = $this->option('local');
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

		if (empty($locals)) // if no local given we get all the locals of the reference.
		{
			$locals = array_map(function ($v)
			{
				return pathinfo($v, PATHINFO_FILENAME);
			}, glob($dir . '/' . $ref . '/*'));
		}

		$data = [];

		foreach ($locals as $local)
		{
			$data[ $local ] = $this->_getData($dir, $local, $langs, $ref);
		}

		$this->info('Create xls to ' . pathinfo($out, PATHINFO_BASENAME));

		\Excel::create(pathinfo($out, PATHINFO_BASENAME), function ($excel) use ($data)
		{

			$excel->sheet('__info_',
				function ($sheet)
				{
					$sheet->fromArray([
						[
							'Don\'t touch key and ref column'
						]
					],
					null,
					'A1',
					false);
				});

			foreach ($data as $name => $local)
			{
				$excel->sheet($name,
					function (LaravelExcelWorksheet $sheet) use ($local)
					{
						$sheet->fromArray($local);
					});
			}
		})->store('xls', pathinfo($out, PATHINFO_DIRNAME));
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

	protected function _getData($dir, $local, $langs, $ref)
	{
		$data = [];

		foreach ($this->_include($dir, $ref, $local) as $k => $v)
		{
			$data[ $k ] = $this->_emptyLine($k, $v, $langs);
		}

		foreach ($langs as $lang)
		{
			foreach ($this->_include($dir, $lang, $local) as $k => $v)
			{
				if (!isset($data[ $k ]))
				{
					$data[ $k ] = $this->_emptyLine($k, 'N/A', $langs);
				}

				$data[ $k ][ $lang ] = $v;
			}
		}

		return $data;
	}

	protected function _include($dir, $lang, $local)
	{
		try
		{
			return array_dot(include $dir . '/' . $lang . '/' . $local . '.php');
		}
		catch (\Exception $e)
		{
			return [];
		}
	}

	protected function _emptyLine($key, $ref, $langs)
	{
		$t = [
			'key' => $key,
			'ref' => $ref,
		];

		foreach ($langs as $lang)
		{
			$t[ $lang ] = null;
		}

		return $t;
	}
}
