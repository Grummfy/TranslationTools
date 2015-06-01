<?php

namespace App\Services\Export;

class ExportLaravel extends ExportAbstract
{
	public function getTranslations($containerDirectory, $referenceLanguage)
	{
		return array_map(function ($v)
		{
			return pathinfo($v, PATHINFO_FILENAME);
		}, glob($containerDirectory . '/' . $referenceLanguage . '/*.php'));
	}

	protected function _getData($containerDirectory, $translation, $languages, $referenceLanguage)
	{
		$data = [];

		foreach ($this->_include($containerDirectory, $referenceLanguage, $translation) as $k => $v)
		{
			$data[ $k ] = $this->_emptyLine($k, $v, $languages);
		}

		foreach ($languages as $lang)
		{
			foreach ($this->_include($containerDirectory, $lang, $translation) as $k => $v)
			{
				if (!isset($data[ $k ]))
				{
					$data[ $k ] = $this->_emptyLine($k, 'N/A', $languages);
				}

				$data[ $k ][ $lang ] = $v;
			}
		}

		return $data;
	}

	protected function _include($containerDirectory, $lang, $translation)
	{
		try
		{
			return array_dot(include $containerDirectory . '/' . $lang . '/' . $translation . '.php');
		}
		catch (\Exception $e)
		{
			return [];
		}
	}
}
