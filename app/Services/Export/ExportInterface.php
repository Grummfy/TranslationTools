<?php

namespace App\Services\Export;

interface ExportInterface
{
	const REF_LANGUAGE = '__ref_';
	const REF_NO_VALUE = 'N/A';

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
