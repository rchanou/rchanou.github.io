<?php

class Translations
{
    public $restler;

    protected function index($desiredData, $sub = null) {
				switch($desiredData) {
            case 'getTranslations':
								return $this->getTranslations(@$_GET['language']);
								break;
						case 'getNamespace':
								return $this->getNamespace(@$_GET['namespace'], @$_GET['language']);
								break;
						case 'translate':
								return $this->translate(@$_GET['keys'], @$_GET['language'], @$_GET['namespace']);
								break;
        }
    }

		protected function getTranslations($language = null)
		{
				// Initialize default values (if we were not given one)
				$language = empty($language) ? 'en-US' : $language;
				$output = array();
				
				if($language === 'en-US') {
					$tsql = "SELECT ResourceSetName AS namespace, ResourceName AS langKey, ResourceValue AS langValue, Culture AS language FROM ResourceSets WHERE Culture IS NULL ORDER BY ResourceName";
					$tsql_params = array();
				} else {
					$tsql = "SELECT ResourceSetName AS namespace, ResourceName AS langKey, ResourceValue AS langValue, Culture AS language FROM ResourceSets WHERE Culture = ? ORDER BY ResourceName";
					$tsql_params = array(&$language);
				}
				$rows = $this->run_query($tsql, $tsql_params);
	
				foreach($rows as $translation) {
					$culture = $translation['language'] === null ? 'en-US' : $translation['language'];
					$output[$culture][$translation['langKey']] = array('value' => $translation['langValue']);
				}
	
				return array('translation' => $output); 
		}
		
		protected function getNamespace($namespace, $language = null)
		{
				if(empty($namespace)) throw new RestException(412, 'No namespace given to translate.');
	
				$output = array();		

				if($language === 'en-US') {
					$tsql = "SELECT ResourceSetName AS namespace, ResourceName AS langKey, ResourceValue AS langValue, Culture AS language FROM ResourceSets WHERE ResourceSetName = ? AND Culture IS NULL ORDER BY ResourceName";
					$tsql_params = array(&$namespace);
				} elseif($language === null) {
					$tsql = "SELECT ResourceSetName AS namespace, ResourceName AS langKey, ResourceValue AS langValue, Culture AS language FROM ResourceSets WHERE ResourceSetName = ? ORDER BY ResourceName";
					$tsql_params = array(&$namespace);
				} else {
					$tsql = "SELECT ResourceSetName AS namespace, ResourceName AS langKey, ResourceValue AS langValue, Culture AS language FROM ResourceSets WHERE ResourceSetName = ? AND Culture = ? ORDER BY ResourceName";
					$tsql_params = array(&$namespace, &$language);
				}
	
				$rows = $this->run_query($tsql, $tsql_params);
				
				foreach($rows as $translation) {
					$culture = $translation['language'] === null ? 'en-US' : $translation['language'];
					$output[$culture][$translation['langKey']] = array('value' => $translation['langValue']);
				}
				
				return array('translation' => $output);  
		}

    protected function translate($keys, $language = null, $namespace = null)
    {
        if(empty($keys)) throw new RestException(412, 'No key(s) given to translate.');

				// Initialize default values (if we were not given one)
				$resourceKeys = !is_array($keys) ? array($keys) : $keys;				
				$language     = empty($language) ? 'en-US' : $language;
				$namespace    = empty($namespace) ? 'Interfaces.Common' : $namespace;	
				$output       = array();			
				
				// Create prepared SQL placeholders for key(s)
				$resourceKeysPlaceholders = array();
				for($i = 0; $i < count($resourceKeys); $i++) {
					$resourceKeysPlaceholders[] = '?';
				}
				$resourceKeysPlaceholders = implode(',', $resourceKeysPlaceholders);
				
				if($language === 'en-US') {
					$tsql = "SELECT ResourceSetName AS namespace, ResourceName AS langKey, ResourceValue AS langValue, Culture AS language FROM ResourceSets WHERE ResourceName IN (" . $resourceKeysPlaceholders . ") AND ResourceSetName = ? AND Culture IS NULL ORDER BY ResourceName";
					$tsql_params = array_merge($resourceKeys, array($namespace));
				} else {
					$tsql = "SELECT ResourceSetName AS namespace, ResourceName AS langKey, ResourceValue AS langValue, Culture AS language FROM ResourceSets WHERE ResourceName IN (" . $resourceKeysPlaceholders . ") AND ResourceSetName = ? AND Culture = ? ORDER BY ResourceName";
					$tsql_params = array_merge($resourceKeys, array($namespace, $language));				
				}

        $rows = $this->run_query($tsql, $tsql_params);

				foreach($rows as $translation) {
					$culture = $translation['language'] === null ? 'en-US' : $translation['language'];
					$output[$culture][$translation['langKey']] = array('value' => $translation['langValue']);
				}

        return array('translation' => $output);   
    }

    private function run_query($tsql, $params = array()) {
        $tsql_original = $tsql . ' ';
				$translationDatabase = empty($GLOBALS['translationDatabase']) ? 'ClubspeedResource' : $GLOBALS['translationDatabase'];
        // Connect
        try {
            $conn = new PDO( "sqlsrv:server=(local) ; Database=" . $translationDatabase, "", "");
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            // Prepare statement
            $stmt = $conn->prepare($tsql);

            // Execute statement
            $stmt->execute($params);

            // Put in array
            $output = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {
            die('Exception Message:'  . $e->getMessage()  . '<br/>(Line: '. $e->getLine() . ')' . '<br/>Passed query: ' . $tsql_original . '<br/>Parameters passed: ' . print_r($params,true));
        }

        return $output;
    }
}