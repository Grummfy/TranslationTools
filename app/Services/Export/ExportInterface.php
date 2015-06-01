<?php

namespace App\Services\Export;

interface ExportInterface
{
	public function getTranslations($containerDirectory, $referenceLanguage);

	public function saveToFile($dirname, $filename, $data);

	public function loadDatas($translations, $languages, $containerDirectory, $referenceLanguage);
}
