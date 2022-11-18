<?php

class DomDocumentParser {

	private $doc;

	public function __construct($html_strings)
	{
			$this->doc = new DomDocument();
		
			$html_strings = html_entity_decode($html_strings);

			libxml_use_internal_errors(true);
			return $this->doc->loadHTML(mb_convert_encoding($html_strings, 'HTML-ENTITIES', 'UTF-8'));
			libxml_clear_errors();
	}

	public function getElementsByClassName($className)
	{
	    $finder = new DomXPath($this->doc);
	    $spaner = $finder->query("//*[contains(@class, '$className')]");

		return $spaner;
	}

	public function getElementById($id)
	{
	    $this->doc->preserveWhiteSpace = false;
	    $belement = $this->doc->getElementById($id);
	}

	public function getLinks()
	{
		return $this->doc->getElementsByTagName('a');
	}

	public function getImgs()
	{
		return $this->doc->getElementsByTagName('img');
	}

	public function getElementsByTagName($tag) 
	{
		return $this->doc->getElementsByTagName($tag);
	}
}