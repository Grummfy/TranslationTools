<?php

namespace App\Services\Export;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class ExportSymfonyYaml extends ExportAbstract
{
	public function getTranslations($containerDirectory, $referenceLanguage)
	{
		return array_map(function ($v)
		{
			$file = pathinfo($v, PATHINFO_FILENAME);
			$file = explode('.', $file);
			array_pop($file);
			return implode('.', $file);
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
					$data[ $k ] = $this->_emptyLine($k, self::REF_NO_VALUE, $languages);
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
			\Log::info('Treat : ' . $file);
			$input = file_get_contents($file);
	        $yaml = new Parser();
	        $messages = $yaml->parse($input, true, true, false);

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
			\Log::error($e->getCode() . ' : ' . $e->getMessage());
			\Log::error($e->getTraceAsString());
			return array();
		}
	}
}
