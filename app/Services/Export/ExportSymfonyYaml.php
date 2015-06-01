<?php

namespace App\Services\Export;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Yaml\Yaml;

class ExportSymfonyYaml extends ExportAbstract
{
	public function getTranslations($containerDirectory, $referenceLanguage)
	{
		return array_map(function ($v)
		{
			return pathinfo($v, PATHINFO_FILENAME);
		}, glob($containerDirectory . '/*.' . $referenceLanguage . '.yml'));
	}

	protected function _getData($containerDirectory, $translation, $languages, $referenceLanguage)
	{
		$data = array();
		foreach ($this->_flattenYaml($containerDirectory . '/' . $translation . '.' . $referenceLanguage . '.yml') as $k => $v)
		{
			$data[ $k ] = $this->_emptyLine($k, $v, $languages);
		}

		foreach ($languages as $lang)
		{
			foreach ($this->_flattenYaml($containerDirectory . '/' . $translation . '.' . $lang . '.yml') as $k => $v)
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

	protected function _flattenYaml($file)
	{
		try
		{
			Info::info('Treat : ' . $file);
			$messages = Yaml::parse($file, true, true);

			// empty file
			if (null === $messages || !is_array($messages))
			{
				$messages = array();
			}

			$messages = array_dot($messages);
			if (is_array($messages) && !empty($messages))
			{
				// never forget to sort => easier to diff
				ksort($messages);
				return $messages;
			}
			return array();
		}
		catch (\Exception $e)
		{
			Info::error($e->getCode() . ' : ' . $e->getMessage());
			Info::error($e->getTraceAsString());
			return array();
		}
	}
}
