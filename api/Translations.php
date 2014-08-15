<?php

class Translations
{
    public $restler;

    public function getInsert($namespace, $name, $value, $language = null, $comment = null) {

				if(empty($namespace)) throw new RestException(412, 'No namespace given for translation.');
				if(!strstr($namespace, '.')) throw new RestException(412, 'A period (".") must exist in the namespace. Ex. "My.Banana"');
				if(empty($name)) throw new RestException(412, 'No name given for translation.');
				if(substr($name, 0, 3) !== 'str') throw new RestException(412, 'A name must begin with "str". Ex. "strMyBanana"');
				
				$existingTranslation = $this->translate($name, $language, $namespace);
				if(!empty($existingTranslation['translation'])) throw new RestException(412, "Translation already exists for {$namespace}:{$name}");
				
				$tsql = 'INSERT INTO ResourceSets (ResourceSetName, ResourceName, ResourceValue, Culture, ResourceComment) VALUES (?, ?, ?, ?, ?)';
				$tsql_params = array($namespace, $name, $value, $language, $comment);
				return $this->run_query($tsql, $tsql_params, false);
		}
		
		protected function index($desiredData, $sub = null) {
				switch($desiredData) {
            case 'getTranslations':
								return $this->getTranslations(@$_GET['language']);
								break;
						case 'getNamespace':
								return $this->getNamespace(@$_GET['namespace'], @$_GET['language']);
								break;
						case 'translate':
								return $this->translate(@$_GET['names'], @$_GET['language'], @$_GET['namespace']);
								break;
						/*case 'add': // Wes's method to bulk load in strings. This would be done from installer
								$translations = array(
									'strWelcomeMessage' => 'Welcome to our track!'
									);
								foreach($translations as $name => $value) {
									$this->getInsert('Booking.Strings', $name, $value);
								}
								break;*/
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

    protected function translate($names, $language = null, $namespace = null)
    {
        if(empty($names)) throw new RestException(412, 'No name(s) given to translate.');

				// Initialize default values (if we were not given one)
				$resourceKeys = !is_array($names) ? array($names) : $names;				
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

    private function run_query($tsql, $params = array(), $fetchResults = true) {
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

            // Put response in array
						$output = $fetchResults ? $stmt->fetchAll(PDO::FETCH_ASSOC) : array('result' => 'success');

        } catch(Exception $e) {
            die('Exception Message:'  . $e->getMessage()  . '<br/>(Line: '. $e->getLine() . ')' . '<br/>Passed query: ' . $tsql_original . '<br/>Parameters passed: ' . print_r($params,true));
        }

        return $output;
    }
}