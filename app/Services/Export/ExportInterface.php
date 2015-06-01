<?php

namespace App\Services\Export;

interface ExportInterface
{
	/**
	 * Get the array of all translation types
	 * @param string $containerDirectory
	 * @param string $referenceLanguage
	 *
	 * @return array
	 */
	public function getTranslations($containerDirectory, $referenceLanguage);

	public function saveToFile($dirname, $filename, $data);

	public function loadDatas($translations, $languages, $containerDirectory, $referenceLanguage);
}
