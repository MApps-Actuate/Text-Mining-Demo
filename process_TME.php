<?php

function process($lang) {
    global $error, $show_results, $text_to_analyze, $language_response_data, $response_data, $error, $configuration, 
    $analyze_concept, $analyze_entity, $analyze_category, $analyze_sentiment, $analyze_summary;


	if ($_REQUEST['op'] == 'AnalyzeText') {
        $text_to_analyze = $_REQUEST['text_to_analyze'];
        $configuration = $_REQUEST['configuration'];
			

		$show_results=true;

// Step1 - Detect Language
        $language_response_data = detect_language($text_to_analyze, $lang);

		if ($show_results) {
			$analyze_concept = analyze_concept($language_response_data['languageId']);
			$analyze_entity = analyze_entity($language_response_data['languageId']);
			$analyze_category = analyze_category($language_response_data['languageId']);
			$analyze_sentiment = analyze_sentiment($language_response_data['languageId']);
			$analyze_summary = analyze_summary($language_response_data['languageId']);

// Step2 - Analyze Text
			$data = generate_request($language_response_data['languageId']);
			$response = generate_REST_call($data);
			$response_data = new SimpleXMLElement($response);

			if ((string) $response_data->ErrorID != '0') {
				$show_results=false;
				$error = 'Error MTM Engine';
			} else {
				intialize_response_data();
				
				if ($analyze_category && $show_results) {
					get_category($response);
				};
				if ($analyze_summary && $show_results) {
				    get_summary($response);
				};
				if ($analyze_concept && $show_results) {
					get_simple_concept($response);
					get_complex_concept($response);
				};
				if ($analyze_sentiment && $show_results) {
					get_sentiment($response);
				};
				if ($analyze_entity && $show_results) {	
					get_entity($response);
				};
			};
		};
	} else {
		$show_results=false;		
	};
};

// ***********************************************************
//           P H P   F U N C T I O N S
// ***********************************************************
function detect_language($text_to_analyze, $forced_language) {
	global $show_results;

    if ($forced_language == "") {
        $data = " <Nserver Version='4.0'>
                <NSTEIN_Text>".$text_to_analyze."</NSTEIN_Text>"; 
        $data .= "  <Methods>
                        <languagedetector>
                            <Mode>text</Mode>
                        </languagedetector>
                    </Methods>       
                </Nserver>";

        $response = generate_REST_call($data);   
		$response_data = new SimpleXMLElement($response);

		if ((string) $response_data->ErrorID != '0') {
			$show_results=false;
			return 'ERROR Language Detection';
		};	

		$Language = $response_data->Results->languagedetector->Languages->Language;
		$language_response_data['languageId'] = (string) $Language->Id ;
		$language_response_data['languageConfidenceScore'] = (integer) (100.0 * (float) $Language->ConfidenceScore);

	} else {
		$language_response_data['languageId'] = $forced_language ;
		$language_response_data['languageConfidenceScore'] = 100;
	};
		
	$ma = array(
		'eng' => 'English',
		'deu' => 'German',
		'fra' => 'French',
		'spa' => 'Spanish',
		'por' => 'Portugese',
		'zho-Hans' => 'Chinese (Simplified)',
		'zho-Hant' => 'Chinese (Traditional)',
		'ara' => 'Arabic',
		'tur' => 'Turkish',
		'tha' => 'Thai',
		'est' => 'Estonian',
		'rus' => 'Russian',
		'pol' => 'Polish',
		'ces' => 'Czech',
		'dan' => 'Danish',
		'heb' => 'Hebrew',
		'jpn' => 'Japanese',
		'kor' => 'Korean',
		'fas' => 'Persian',
		'nld' => 'Nederlands',
		'und' => 'Unknown'
	);
	if (isset($ma[$language_response_data['languageId']]))  {
		$language_response_data['languageName']=$ma[$language_response_data['languageId']] ;
	} else {
		$language_response_data['languageName']=$language_response_data['languageId'] ;
	};

    return $language_response_data;
};

function analyze_concept($lang) {
    $ma = array('eng','deu','fra','spa','por','zho-Hans','ara','zho-Hant','tur','pol','rus','ces', 'und', 'nld');
    return in_array($lang,$ma);
};

function analyze_entity($lang) {
    $ma = array('eng','deu','fra','spa','por','zho-Hans','ara','zho-Hant','tur','pol','rus','ces', 'und', 'nld');
    return in_array($lang,$ma);
};

function analyze_category($lang) {
    $ma = array('eng','deu','fra','spa','por');
    return in_array($lang,$ma);
};

function analyze_sentiment($lang) {
    $ma = array('eng','deu','fra','spa','por', 'und', 'nld');
    return in_array($lang,$ma);
};

function analyze_summary($lang) {
    $ma = array('eng','deu','fra','spa','por');
    return in_array($lang,$ma);
};

function generate_request($lang) {

    global $text_to_analyze, $analyze_concept, $analyze_entity, $analyze_category, $analyze_sentiment, $analyze_summary;

    $header = " <Nserver Version='4.0'>
                <NSTEIN_Text>".$text_to_analyze."</NSTEIN_Text>
				<LanguageID>".$lang."</LanguageID>";
	
    $profile = "<Methods>";

    if ($analyze_concept) {                
        $profile .= "<nconceptextractor>
                        <Mode>NCONCEPT</Mode>
                        <ComplexConcepts>
                            <RelevancyLevel>FIRST</RelevancyLevel>
                            <NumberOfComplexConcepts>10</NumberOfComplexConcepts>
                        </ComplexConcepts>
                        <SimpleConcepts>
                            <NumberOfSimpleConcepts>10</NumberOfSimpleConcepts>
                        </SimpleConcepts>
                        <ResultLayout>NCONCEPTEXTRACTOR</ResultLayout>
                    </nconceptextractor>";
    };
    if ($analyze_category) {
        $profile .= "<ncategorizer>
                        <KnowledgeBase>
                            <KBid>IPTC</KBid>
                            <LegacyResult>true</LegacyResult>
                            <NumberOfCategories>5</NumberOfCategories>
                        </KnowledgeBase>
                    </ncategorizer>";
    };
    if ($analyze_entity) {                
        $profile .= "<nfinder>
                        <nfExtract>
                            <Cartridges>
                                <Cartridge>GL</Cartridge>
                                <Cartridge>ON</Cartridge>
                                <Cartridge>PN</Cartridge>
                            </Cartridges>
                        </nfExtract>
                    </nfinder>";
    };
    if ($analyze_sentiment) {                
        $profile .= "<nsentiment>
                        <Name>Nstein</Name>
                    </nsentiment>" ;
    };
    if ($analyze_summary) {
        $profile .= "<nsummarizer>
                        <KBid>IPTC</KBid>
                        <Percentage>10</Percentage>
                    </nsummarizer> ";
    };
    $profile .= "</Methods>";

	$footer = "</Nserver>";

	return $header.$profile.$footer;
};

function generate_REST_call($data) {
	// API URL to send data
	$url = "http://td-riskguard1.eastus.cloudapp.azure.com/rs/v2";

	// curl initiate
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml', 'Authorization: Basic YWRtaW46YWRtaW4='));
	
	// SET Method as a POST
	curl_setopt($ch, CURLOPT_POST, 1);
	
	// Pass user data in POST command
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// Execute curl and assign returned data
	$response  = curl_exec($ch);
	
	// Close curl
	curl_close($ch);
	
	// See response if data is posted successfully or any error
	return $response;
};

function intialize_response_data() {
	global $response_data;

    $response_data = array();
	$response_data['categories']=array();
	$response_data['summary']="";
	$response_data['concepts_simple']=array();
	$response_data['concepts_complex']=array();
    $response_data['entities']=array();
	$response_data['entities_ON'] = array();
	$response_data['entities_GL'] = array();
	$response_data['entities_PN'] = array();
	$response_data['entities_TM'] = array();
	$response_data['entities_EV'] = array();
	$response_data['sentimentSentences']=array();
	$response_data['sentimentSentences_positive'] = array();
	$response_data['sentimentSentences_neutral'] = array();
	$response_data['sentimentSentences_negative'] = array();
	$response_data['sentiment_analyzed'] = false;
	$response_data['sentiment'] = new stdClass();	
    $response_data['entities_total'] = 0;
	$response_data['concepts_total'] = 0;
	$response_data['categories_total'] = 0;
	$response_data['sentiment_sentences_total'] = 0;
}
		
// ****************************************
// C A T E G O R I E S   D A T A
// ****************************************
function get_category($response) {
	global $response_data;
	
	$xml_response = new SimpleXMLElement(urldecode($response));
	$xKnowledgeBases = $xml_response->Results->ncategorizer->KnowledgeBase;
	if (!is_null($xKnowledgeBases)) {
		foreach ($xKnowledgeBases as $xKnowledgeBase) {
			$sKbId = (string)$xKnowledgeBase->KBid;
			$xCategories = $xKnowledgeBase->Categories->Category;
			$oCategories = array();
			if (count($xCategories)>0 && (string)$xCategories[0]!="NO CATEGORIES") {
				foreach ($xCategories as $xCategory) {
					$oCategory = new stdClass();
					$oCategory->Name = (string)$xCategory;
					preg_match('~[a-z]~i', $oCategory->Name, $match, PREG_OFFSET_CAPTURE);
					$pos = $match[0][1];
					$oCategory->Name = ucwords(substr($oCategory->Name,$pos));
					$oCategory->Weight = (string)$xCategory->attributes()->Weight;
					$oCategories[] = $oCategory;
				}
			}
			$response_data['categories'][$sKbId]=$oCategories;
			fieldSort($response_data['categories'][$sKbId],"Weight") ;
		}
		$response_data['categories_total'] = count($response_data['categories']['IPTC']);
	}
	
};

// ****************************************
// S U M M A R Y   D A T A
// ****************************************
function get_summary($response) {
	global $response_data;
	
    $xml_response = new SimpleXMLElement(urldecode($response));
	$xSummary = $xml_response->Results->nsummarizer->Summary;        
	if (!is_null($xSummary)) {
		$sSummary = (string) $xSummary;
		$response_data['summary']=$sSummary;
	};
};

// ****************************************
// S I M P L E   C O N C E P T   D A T A
// ****************************************	
function get_simple_concept($response) {
	global $response_data;
		
	$xml_response = new SimpleXMLElement(urldecode($response));
	$xSimpleConcepts = $xml_response->Results->nconceptextractor->SimpleConcepts->Concept;
	if (!is_null($xSimpleConcepts)) {
		$oSimpleConcepts = array();
		if (count($xSimpleConcepts)>0 && (string)$xSimpleConcepts[0]!="NO SIMPLE CONCEPTS")
		{
			foreach ($xSimpleConcepts as $xSimpleConcept)
			{
				$sSimpleConcept = strtolower((string)$xSimpleConcept);
				$oSimpleConcept = new stdClass();
				$oSimpleConcept->Name = $sSimpleConcept;
				$oSimpleConcept->Frequency = (integer) $xSimpleConcept['Frequency'];
				$oSimpleConcept->Relevancy = (integer) $xSimpleConcept['Relevancy'];
				$oSimpleConcept->ConceptType = "simple";
				$oSimpleConcept->Id = (string) uniqid();
				$oSimpleConcepts[] = $oSimpleConcept;
			}
		}
		$response_data['concepts_simple']=$oSimpleConcepts;

		fieldSort($response_data['concepts_simple'],"Relevancy");		
		$response_data['concepts_total'] = count($response_data['concepts_simple']);
	};	
};

// ****************************************
// C O M P L E X   C O N C E P T   D A T A
// ****************************************	
function get_complex_concept($response) {	
	global $response_data;
	
	$xml_response = new SimpleXMLElement(urldecode($response));
	$xComplexConcepts = $xml_response->Results->nconceptextractor->ComplexConcepts->Concept;
	if (!is_null($xComplexConcepts)) {
		$oComplexConcepts = array();
		if (count($xComplexConcepts)>0 && (string)$xComplexConcepts[0]!="NO COMPLEX CONCEPTS")
		{
			foreach ($xComplexConcepts as $xComplexConcept)
			{
				$sComplexConcept = strtolower((string)$xComplexConcept);
				$oComplexConcept = new stdClass();
				$oComplexConcept->Name = $sComplexConcept;
				$oComplexConcept->Id = (string) uniqid();
				$oComplexConcept->ConceptType = "complex";
				$oComplexConcept->Frequency = (integer) $xComplexConcept['Frequency'];
				$oComplexConcept->Relevancy = (integer) $xComplexConcept['Relevancy'];
				$oComplexConcepts[] = $oComplexConcept;
			}
		}
		$response_data['concepts_complex']=$oComplexConcepts;

		fieldSort($response_data['concepts_complex'],"Relevancy");
		$response_data['concepts_total'] = $response_data['concepts_total'] + count($response_data['concepts_complex']);
	};
};

// ****************************************
// E N T I T I E S   D A T A
// ****************************************	
function get_entity($response) {	
	global $response_data;
	
	$xml_response = new SimpleXMLElement(urldecode($response));
	$xEntitiesNfExtract = $xml_response->Results->nfinder->nfExtract->ExtractedTerm;
	$xEntitiesNfFullTextSearch = $xml_response->Results->nfinder->nfFullTextSearch->ExtractedTerm;

	if (!is_null($xEntitiesNfExtract)) {
		foreach ($xEntitiesNfExtract as $xEntity){
			$xEntities[] = $xEntity;
		}
	};
	if (!is_null($xEntitiesNfFullTextSearch)) {
		foreach ($xEntitiesNfFullTextSearch as $xEntity){
			$xEntities[] = $xEntity;
		}
	};

	foreach ($xEntities as $xEntity) {
		$oEntity = new stdClass();
		if ((string)($xEntity->Id)!="") {
			$oEntity->Id = (string) $xEntity->Id;
		} else {
			$oEntity->Id = (string) uniqid();
		}
		$oEntity->CartridgeID = (string)$xEntity["CartridgeID"];
		$oEntity->ConfidenceScore = (string)$xEntity["ConfidenceScore"];
		$oEntity->RelevancyScore = (integer)$xEntity["RelevancyScore"];
		$oEntity->Frequency = (string)$xEntity["Frequency"];
		$oEntity->Subjectivity = (string)$xEntity["Subjectivity"];
		$oEntity->SubjectivityScore = (string)$xEntity["SubjectivityScore"];
		$oEntity->SubjectivityDistribution = (string)$xEntity["SubjectivityDistribution"];
		$oEntity->Tone = (string)$xEntity["Tone"];
		$oEntity->ToneConfidenceScore = (string)$xEntity["ToneConfidenceScore"];
		$oEntity->PositiveToneScore = (string)$xEntity["PositiveToneScore"];
		$oEntity->PositiveToneDistribution = (string)$xEntity["PositiveToneDistribution"];
		$oEntity->NegativeToneScore = (string)$xEntity["NegativeToneScore"];
		$oEntity->NegativeToneDistribution = (string)$xEntity["NegativeToneDistribution"];


		if (isset($xEntity->nfinderNormalized)) {
			$oEntity->Name = (string) $xEntity->nfinderNormalized;
		} elseif (isset($xEntity->MainTerm)) {
			$oEntity->Name = (string) $xEntity->MainTerm;
		} else {
			// get first subterm
			if (!is_null($xEntity->Subterms->Subterm)) {
				$xSubterm = $xEntity->Subterms->Subterm[0];
			} else {
				$xSubterm = "";
			};
			$oEntity->Name = (string) (string)$xSubterm;
		}

		$xSubterms = $xEntity->Subterms->Subterm;
		$oEntity->Subterms=array();
		if (!is_null($xSubterms)) {
			foreach ($xSubterms as $xSubterm) {
				$oSubterm = new stdClass();
				$oSubterm->Id = (string)$xSubterm->Id;
				$oSubterm->EntityId = (string)$oEntity->Id;
				$oSubterm->Text = (string)$xSubterm;
				$oSubterm->EntityName = $oEntity->Name;
				$oSubterm->EntityType = $oEntity->CartridgeID;
				$oSubterm->Position = (integer)$xSubterm["Position"];
				$oSubterm->Length = (integer)$xSubterm["Length"];
				$oSubterm->SentenceBeginIndex = (integer)$xSubterm["SentenceBeginIndex"];
				$oSubterm->SentenceEndIndex = (integer)$xSubterm["SentenceEndIndex"];
				$oSubterm->Sentence = (string)$xSubterm["Sentence"];
				$oSubterm->Candidate = (integer)$xSubterm["Candidate"];
				$oSubterm->Subjectivity = (string)$xSubterm["Subjectivity"];
				$oSubterm->SubjectivityScore = (string)$xSubterm["SubjectivityScore"];
				$oSubterm->SubjectivityConfidenceScore = (string)$xSubterm["SubjectivityConfidenceScore"];
				$oSubterm->Tone = (string)$xSubterm["Tone"];
				$oSubterm->ToneConfidenceScore = (string)$xSubterm["ToneConfidenceScore"];
				$oSubterm->PositiveToneScore = (string)$xSubterm["PositiveToneScore"];
				$oSubterm->NegativeToneScore = (string)$xSubterm["NegativeToneScore"];
				$oEntity->Subterms[]=$oSubterm;
			}
		};
		$response_data['entities'][]=$oEntity;
	};

	// now create filtered entity lists for convenience
	foreach ($response_data['entities'] as $oEntity) {
		$response_data['entities_'.$oEntity->CartridgeID][] = $oEntity;
	}

	fieldSort($response_data['entities_ON'],"RelevancyScore") ;
	fieldSort($response_data['entities_GL'],"RelevancyScore") ;
	fieldSort($response_data['entities_PN'],"RelevancyScore") ;
	fieldSort($response_data['entities_TM'],"RelevancyScore") ;
	fieldSort($response_data['entities_EV'],"RelevancyScore") ;		

	$response_data['entities_total'] = count($response_data['entities_ON']) +
			   count($response_data['entities_PN']) +
			   count($response_data['entities_TM']) +
			   count($response_data['entities_GL']) +
			   count($response_data['entities_ON']) +
			   count($response_data['entities_EV']);	
};
       
// ****************************************
// S E N T I M E N T   D A T A
// ****************************************	
function get_sentiment($response) {
	global $response_data;
	
	$xml_response = new SimpleXMLElement(urldecode($response));
	$xSentimentSentences = $xml_response->Results->nsentiment->SentenceLevel->Sentence;
	if (!is_null($xSentimentSentences)) {
		foreach ($xSentimentSentences as $xSentimentSentence) {
			$oSentence = new stdClass();
			$oSentence->Text = (string) $xSentimentSentence->Text;
			$oSentence->Id = (string) uniqid();
			$oSentence->Begin = (integer) $xSentimentSentence->Text['begin'];
			$oSentence->End = (integer) $xSentimentSentence->Text['end'];
			$oSentence->Position = (integer) $xSentimentSentence->Text['begin'];
			$oSentence->Length = (integer) $xSentimentSentence->Text['end'] - (integer) $xSentimentSentence->Text['begin'];
			$oSentence->Subjectivity = (string) $xSentimentSentence->Subjectivity;
			$oSentence->SubjectivityScore = (string) $xSentimentSentence->Subjectivity['score'];
			$oSentence->Tone = (string) $xSentimentSentence->Tone;
			$oSentence->PositiveToneScore = (string) $xSentimentSentence->PositiveTone['score'];
			$oSentence->NegativeToneScore = (string) $xSentimentSentence->NegativeTone['score'];
			$response_data['sentimentSentences'][]=$oSentence;
		}
	};

	// now create filtered sentiment sentence lists for convenience
	foreach ($response_data['sentimentSentences'] as $oSentence) {
		$response_data['sentimentSentences_'.$oSentence->Tone][] = $oSentence;
	};

	fieldSort($response_data['sentimentSentences_positive'],"PositiveToneScore") ;
	fieldSort($response_data['sentimentSentences_negative'],"NegativeToneScore") ;
	fieldSort($response_data['sentimentSentences_neutral'],"SubjectivityScore") ;

	// Extract sentiment data - document level
	$xSentimentData = $xml_response->Results->nsentiment->DocumentLevel;
	$response_data['sentiment_analyzed'] = true;
	$response_data['sentiment']->Subjectivity = (string) $xSentimentData->Subjectivity;
	$response_data['sentiment']->Tone = (string) $xSentimentData->Tone;

	$response_data['sentiment']->SubjectivityScore = (integer) $xSentimentData->Subjectivity['score'];
	$response_data['sentiment']->SubjectivityDistribution = (integer) $xSentimentData->Subjectivity['distribution'];

	$response_data['sentiment']->PositiveToneScore = (integer) $xSentimentData->PositiveTone['score'];
	$response_data['sentiment']->PositiveToneDistribution = (integer) $xSentimentData->PositiveTone['distribution'];
	$response_data['sentiment']->NegativeToneScore = (integer) $xSentimentData->NegativeTone['score'];
	$response_data['sentiment']->NegativeToneDistribution = (integer) $xSentimentData->NegativeTone['distribution'];

	$response_data['sentiment']->Percentages = array();
	if ($response_data['sentiment']->Subjectivity=="opinion") {
		$response_data['sentiment']->Percentages["opinion"]=$response_data['sentiment']->SubjectivityScore;
		$response_data['sentiment']->Percentages["fact"]=100-$response_data['sentiment']->Percentages["opinion"];
	} else {
		$response_data['sentiment']->Percentages["fact"]=$response_data['sentiment']->SubjectivityScore;
		$response_data['sentiment']->Percentages["opinion"]=100-$response_data['sentiment']->Percentages["fact"];
	}
	
	$response_data['sentiment']->Percentages["neutral"]=100;
	$response_data['sentiment']->Percentages["positive"]=0;
	$response_data['sentiment']->Percentages["negative"]=0;
	$response_data['sentiment']->Percentages["positive"]=$response_data['sentiment']->PositiveToneScore;
	$response_data['sentiment']->Percentages["negative"]=$response_data['sentiment']->NegativeToneScore;
	$response_data['sentiment']->Percentages["neutral"]=100-$response_data['sentiment']->PositiveToneScore-$response_data['sentiment']->NegativeToneScore;
	
	$response_data['sentiment_sentences_total'] = count($response_data['sentimentSentences']);
};

// descending by default, if $asc is set then do ascending.
function fieldSort (&$array,$field,$asc=false)
{
    $temp = array();
    foreach ($array as $i=>$obj)
    {
        $sort_order = $obj->$field;
        if (!isset($temp[$sort_order]))
        {
            $temp[$sort_order]=array();
        }
        $temp[$sort_order][]=$obj;
    }
    $array = array();
    if ($asc)
    {
        ksort($temp);
    }
    else
    {
        krsort($temp);
    }
    foreach ($temp as $k => $valuesArray)
    {
        foreach ($valuesArray as $value)
        {
            $array[]=$value;
        }
    }
};

?>