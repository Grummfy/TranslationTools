<?php

namespace App\Services\Export;

abstract class ExportAbstract implements ExportInterface
{
	abstract protected function _getData($containerDirectory, $translation, $languages, $referenceLanguage);

	public function saveToFile($dirname, $filename, $data)
	{
		$this->info('Create xls to ' . $filename);
		\Excel::create($filename, function ($excel) use ($data)
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

			foreach ($data as $name => $translation)
			{
				$excel->sheet($name,
					function (LaravelExcelWorksheet $sheet) use ($translation)
					{
						$sheet->fromArray($translation);
					});
			}
		})->store('xls', $dirname);
	}

	public function loadDatas($translations, $languages, $containerDirectory, $referenceLanguage)
	{
		$data = [];
		foreach ($translations as $translation)
		{
			$data[ $translation ] = $this->_getData($containerDirectory, $translation, $languages, $referenceLanguage);
		}

		return $data;
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
