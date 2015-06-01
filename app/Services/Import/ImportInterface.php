<?php

namespace App\Services\Import;

interface ImportInterface
{
	const INFO_SHEET = '__info_';

	public function loadFile($file, $dir, array $languages = null, $sheetName = null);

	public function processSheet($sheet, $languages);

	public function exportSheetToDestination($dir, $lang, $local, $data);

	public function exportSheetsToDestination($dir, $export, $local);
}
