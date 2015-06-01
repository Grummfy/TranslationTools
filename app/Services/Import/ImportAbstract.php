<?php

namespace App\Services\Import;

abstract class ImportAbstract implements ImportInterface
{
	public function loadFile($file, $dir, array $languages = null, $sheetName = null)
	{
		// did we have a specific sheet to load?
		if (empty($sheetName))
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
			$data = \Excel::selectSheets($sheetName)->load($file)->get();
			$data = [$data->getTitle() => $data->toArray()];

			if ($data instanceof SheetCollection)
			{
				$t = [];
				foreach ($data as $sheet)
				{
					$t[ $sheet->getTitle() ] = $sheet->toArray();
				}
				$data = $t;
			}
		}

		$i = 0;
		foreach ($data as $local => $sheet)
		{
			if ($local == self::INFO_SHEET)
			{
				continue;
			}

			Log::info('Translation import: processing ' . $local);

			$export = $this->processSheet($sheet, $languages);
			$this->exportSheetsToDestination($dir, $export, $local);

			Log::info('Translation import: done');
			$i++;
		}

		Log::info($i . 'translation(s) type processed');
	}

	public function processSheet($sheet, $languages)
	{
		$export = array();
		foreach ($sheet as $row)
		{
			foreach ($languages as $lang)
			{
				$export[ $lang ][ $row[ 'key' ] ] = $row[ $lang ];
			}
		}

		return $export;
	}

	public function exportSheetsToDestination($dir, $export, $local)
	{
		foreach ($export as $lang => $data)
		{
			// assure order of key => easier to compare on diff
			ksort($data);

			$array = array();
			foreach ($data as $key => $value)
			{
				// convert dot notation in real array
				array_set($array, $key, $value);
			}

			// export it
			$this->exportSheetToDestination($dir, $lang, $local, $data);
		}
	}
}
