<!--------------------------------------------------------->
<!-- P L E A S E   R E A D   T H I S   B E F O R E       -->
<!--                                                     -->
<!-- This is a specific page for a defined language      -->
<!-- It reuses script and php page from the root folder  --> 
<!-- If you modify the link to the page, do it carefully -->
<!--------------------------------------------------------->	
    <?php  include '../process_TME.php';
			process('nld'); ?>
    <?php if ($error) { ?>
    <div class="messages error">ERROR: <?php print_r($error); ?></div>
    <?php }?>			

<script>
	var myWords = '[';
	var myCats = '[';
	var myConceptRels = '[';
	var myEntityRels = '[';
	var myConcepts = '[';
	var myEntities = '[';
	var negativeSent = "Geen negatieve zin gevonden.";
	var positiveSent = "Geen positieve zin gevonden.";
</script>	

<?php if ($show_results) { ?>							 
					<div>
    					<div class="card">
<!-------------------------------------------------------->
<!-- H E A D E R                                        -->
<!-------------------------------------------------------->						
	<div class="summary">
			<div class="summary__header">
				<h2 class="summary__headline">Samenvatting van de analyse</h2>

				<!-- Language -->
				<div class="summary__language">Taal: <span class="summary__language--language-name"><?php echo $language_response_data['languageName'] ?></span></div>
				<!-- Tone -->
				<div class="summary__tone">Algemene toon: <span class="<?php 
				if ($response_data['sentiment_analyzed']) {
					if($response_data['sentiment']->Tone == "negative") { echo "summary__tone--negative"; 
					} else if ($response_data['sentiment']->Tone == "positive") { echo "summary__tone--positive"; 
					} else { echo "summary__tone--neutral"; } ?>"><?php echo ucfirst($response_data['sentiment']->Tone); 
				}	?></span>
				</div>
			</div>

			<div class="summary__most-popular">
				
				<?php if ($response_data['entities_total']==0) { ?>
			<div>Er werden geen entiteiten ontdekt.</div>

	<?php

} else {
 
	$entityGroups = array();
	
	if ($configuration == 'standard') {
		$entityGroups["PN"] = "Mensen";
		$entityGroups["GL"] = "Plaatsen";
		$entityGroups["ON"] = "Organisaties";
	}
	$counterthis = 0;
	/* TODO remove those that don't apply, per language? */
	foreach ($entityGroups as $groupId => $groupText) {
		$top = count($response_data["entities_" . $groupId]);
		if (count($response_data["entities_" . $groupId]) == 0) {
		?>
				<div class="summary__popular-col">
										<h3 class="summary__popular-col-heading">Meest Relevant <?php echo $groupText; ?></h3>
										<p class="summary__popular-col-csv">Geen  <?php echo $groupText; ?> gevonden.</p></div>
		<?php 
		} else {
			?>
					<div class="summary__popular-col">
										<h3 class="summary__popular-col-heading">Meest Relevant <?php echo $groupText; ?></h3>
										<p class="summary__popular-col-csv">
	
			<?php
			foreach ($response_data["entities_" . $groupId] as $oEntity) {
				?><script>myWords+='"<?php echo addslashes($oEntity->Name);?>",'</script><?php
				if($counterthis < 5){
					$counterthis++;
					echo $oEntity->Name;
					if($counterthis < 4 && $counterthis < $top){
						echo ",";
					}
				}
			?> 
			<?php 
			
			}  
			$counterthis = 0;
			?>
			</div>
<?php
			} // end else	   
		} // end loop
	} // end else
?>
	</div>    

	<div class="summary__sentiment">

		<!-- Most positive sentence -->
		<div class="summary__sentiment-col">
			<h3 class="summary__sentiment-heading summary__sentiment-heading--positive">Meest positieve zin</h3>
			<p class="summary__sentence_positive"></p>
		</div>

		<!-- Most negative sentence -->
		<div class="summary__sentiment-col">
			<h3 class="summary__sentiment-heading summary__sentiment-heading--negative">Meest negatieve zin</h3>
			<p class="summary__sentence_negative"></p>
		</div>
	</div>

</div>

<!-------------------------------------------------------->
<!-- R E S U L T   T A B S                              -->
<!-------------------------------------------------------->
<div class="card">
<div class="result-tabs">
	<!-- responsive tab nav -->
	<div class="tab-control">
		<ul>
			<?php 
			if($analyze_category || $analyze_summary) {?>
				<li><a href="#topics">Onderwerpen en samenvatting</a></li>
			<?php };
			if($analyze_entity) {?>
				<li><a href="#entities">Entiteiten</a></li>
			<?php };
			if($analyze_concept) {?>
				<li><a href="#concepts">Concepten</a></li>
			<?php };
			if($analyze_sentiment) {?>
				<li><a href="#sentiment">Sentiment</a></li>
			<?php }; ?>
		</ul>
<!-------------------------------------------------------->
<!-- T O P I C S   T A B                                -->  
<!-------------------------------------------------------->			
		<div id="topics" <?php if(!$analyze_category && !$analyze_summary) {?> style="display:none"<?php } ?>>
			
			<!-- tab content container -->
			<div class="tab-topics">
			<div class="tabsummary">
					<h2 class="summarizer__heading">Magellan Onderwerpen en samenvatting</h2>
					<p class="topicsummarizer__description">OpenText Magellan identificeert automatisch onderwerpen op hoog niveau (indien aanwezig) en vat ook een volledig document of tekstfragment samen. Onderwerpen zijn bredere overkoepelende concepten, terwijl de samenvatting een conceptueel overzicht op hoog niveau biedt.</p>
			</div>
			<h2 class="tab-heading">Gevonden onderwerpen</h2>
			<div class="chart">
				<div class="chart__section">
					<?php if ($response_data['categories_total']==0) { ?>
						<h3>Er zijn geen onderwerpen ontdekt.</h3>
					<?php
					} else {
					$topicGroups = array();
						if ($configuration == 'standard') {$topicGroups["IPTC"] = "Belangrijkste onderwerpen";}

						foreach ($topicGroups as $groupId => $groupText) {
							if (count($response_data["categories"][$groupId]) == 0) {
							} else {
						?>
						<!-- section header row -->
							<div class="chart__header-row">
								<h3 class="chart__section-name"><?php echo $groupText; ?><span class="chart__count-badge"><?php echo count($response_data["categories"][$groupId]) ?></span></h3>
								<div class="chart__col-head"></div>
								<div class="chart__col-head"></div>
								<div class="chart__col-head">Gewicht</div>
							</div>
						<?php
							foreach ($response_data["categories"][$groupId] as $oCategory) {
						?>
							<script>myCats+='"<?php echo addslashes($oCategory->Name);?>",'</script>
						<!-- section data rows -->
								<div class="chart__row">
									<div class="chart__data-name"><?php echo $oCategory->Name; ?></div>
									<div class="chart__col-relevancy"></div>
									<div class="chart__col-sentiment chart__col-sentiment--positive"></div>
									<div class="chart__col-relevancy"><?php echo $oCategory->Weight; ?></div>
								</div>  
						<?php
							} // end loop
							} // end else
						} // end loop
					} // end else
					?>
				</div><!-- end chart section -->
			</div><!-- end chart -->
		</div><!-- end div -->
		
		<div id="article-summarizer">
			<div class="tab-article-summarizer">
				<!-- Summary -->
				<div class="summarizer">
					<h2 class="summarizer__heading">Magellan artikel samenvatting</h2>
					<p class="subsummarizer__description">OpenText Magellan kan grote hoeveelheden tekst analyseren en distilleren tot een korte samenvatting.</p>
					<p class="summarizer__summary">
					<?php
					if ($response_data['summary']) {
						echo $response_data['summary'];
					} else {
						echo "<div>Geen samenvatting beschikbaar.</div>";
					}
					?></p>
				</div><!-- end summary -->

				<div class="summarizer-stats">
				<!-- Max number of sentences -->
					<div class="summarizer-stats__stat">
						<div class="summarizer-stats__stat-name">Maximum aantal zinnen:</div>
						<div class="summarizer-stats__stat-value">5</div>
					</div>

				<!-- Max percent of input -->
					<div class="summarizer-stats__stat">
						<div class="summarizer-stats__stat-name">Maximumpercentage van de ingevoerde tekst:</div>
						<div class="summarizer-stats__stat-value">10%</div>
					</div>

				<!-- Type of content -->
					<div class="summarizer-stats__stat">
						<div class="summarizer-stats__stat-name">Type inhoud:</div>
						<div class="summarizer-stats__stat-value">Nieuws</div>
					</div>
				</div><!-- end stats -->
			</div><!-- tab content container -->
		</div>
	</div><!-- end topics tab -->
								
<!-------------------------------------------------------->
<!-- E N T I T I E S   T A B                            -->
<!-------------------------------------------------------->
	<div id="entities" <?php if(!$analyze_entity) {?> style="display:none"<?php } ?>>
		<!-- tab content container -->
		<div class="tab-entities">
			<div class="tabsummary">
				<h2 class="summarizer__heading">Magellan Entiteiten</h2>
				<p class="summarizer__description">OpenText Magellan identificeert automatisch entiteiten (mensen, plaatsen en organisaties) in de tekst.</p>
			</div>
			
			<div id="wordcloud" class="wordcloud"></div>

			<h2 class="tab-heading">Uitsplitsing naar entiteit</h2>
			<div class="chart">
					<div class="chart__section">

					<?php if ($response_data['entities_total']==0) { ?>
						<div class="chart__header-row">
							<h3 class="chart__section-name">Er werden geen entiteiten ontdekt.</h3>
						</div>
						<div class="chart__row">
							<div class="chart__data-name"></div>
							<script>myEntities+='"Geen entiteiten gedetecteerd",'</script>
						</div>
						<?php
						} else {
  
							$entityGroups = array();
							
							if ($configuration == 'standard') {
								$entityGroups["PN"] = "Mensen";
								$entityGroups["GL"] = "Plaatsen";
								$entityGroups["ON"] = "Organisaties";
							}
							$counterthis = 0;
							/* TODO remove those that don't apply, per language? */
							foreach ($entityGroups as $groupId => $groupText) {
								if (count($response_data["entities_" . $groupId]) == 0) {
									//echo "<div class='row-fluid'><div class='alert alert-info alert-neutral'><h3 class='subtitle'>" . $groupText . "</h3><span class='normalft_blue'>None detected.</span></div></div>";
								} else {
									?>
											 <!-- section header row -->
								<div class="chart__header-row">
									<h3 class="chart__section-name"><?php echo $groupText; ?><span class="chart__count-badge"><?php echo count($response_data["entities_".$groupId]) ?></span></h3>
									<div class="chart__col-head">Relevantie</div>
									<div class="chart__col-head"></div>
									<div class="chart__col-head">Frequentie</div>
								</div>
											
									<?php
									foreach ($response_data["entities_" . $groupId] as $oEntity) {
										if(count($response_data["entities_" . $groupId]) != 0){
									?>
										<script>myEntities+='"<?php echo addslashes($oEntity->Name);?>",';
										myEntityRels+='"<?php echo $oEntity->RelevancyScore;?>",';
										</script>
										<div class="chart__row">
											<div class="chart__data-name"><?php echo $oEntity->Name; ?></div>
											<div class="chart__col-relevancy"><?php if($oEntity->RelevancyScore !='0') {echo $oEntity->RelevancyScore;}else{echo '&nbsp;';} ?>%</div>
											<div class="chart__col-sentiment"></div>
											<div class="chart__col-relevancy"><?php echo $oEntity->Frequency; ?></div>
										</div> 
						<?php
										}
										}
									}
								 } // end else
							} // end loop
						?>
								
					</div><!-- end chart section -->

			</div><!-- end chart -->

		</div><!-- end tab content container -->
	</div><!-- end entities tab -->
	
<!-------------------------------------------------------->
<!-- C O N C E P T S   T A B                            -->
<!-------------------------------------------------------->			
	<!-- concepts tab-->
	<div id="concepts" <?php if(!$analyze_concept) {?> style="display:none"<?php } ?>>						
		<!-- tab content container -->
		<div class="tab-concepts">
		<div class="tabsummary">
				<h2 class="summarizer__heading">Magellan Concepten</h2>
				<p class="summarizer__description">OpenText Magellan identificeert automatisch complexe en eenvoudige concepten in de tekst.</p>
				</div>
			
			<div id="wordcloud2" class="wordcloud"></div>
				<h2 class="tab-heading">Concept uitsplitsing</h2>
				<div class="chart">					
					<div class="chart__section">

					<?php
					if ($response_data['concepts_total'] == 0) {
					?>
						<div class="chart__header-row"><h3 class="chart__section-name">Er werden geen concepten ontdekt.</h3>
						</div>
						<script>myConcepts+='"Geen concepten gedetecteerd",'</script>
					<?php
					} else {
						$conceptGroups = array(
						"complex" => "Complexe concepten",
						"simple" => "Simpele concepten"
						);
						foreach ($conceptGroups as $groupId => $groupText) {
							if (count($response_data["concepts_" . $groupId]) == 0) {
								echo "<div class='chart__header-row'><h3 class='chart__section-name'>" . $groupText . " Geen gevonden.<h3 class='chart__section-name'></h3></div>";
							} else {
							?>
					<!-- section header row -->
							<div class="chart__header-row">
								<h3 class="chart__section-name"><?php echo $groupText; ?><span class="chart__count-badge"><?php echo count($response_data["concepts_" . $groupId]) ?></span></h3>
								<div class="chart__col-head">Relevantie</div>
								<div class="chart__col-head"></div>
								<div class="chart__col-head">Frequentie</div>
							</div>

							<?php
								foreach ($response_data["concepts_" . $groupId] as $oConcept) {
								?>
								<script>myConcepts+='"<?php echo addslashes($oConcept->Name) ;?>",';
									myConceptRels+='"<?php echo $oConcept->Relevancy ;?>",';
								</script>                                                         
										 
								<!-- section data rows -->
								<div class="chart__row">
									<div class="chart__data-name"><?php echo $oConcept->Name; ?></div>
									<div class="chart__col-relevancy"><?php echo $oConcept->Relevancy; ?>%</div>
									<div class="chart__col-sentiment chart__col-sentiment--positive"></div>
									<div class="chart__col-relevancy"><?php echo $oConcept->Frequency; ?></div>
								</div>                                                       
							<?php 
							}
					?>
																		
					<?php
					} // end else
					} // end loop
					} // end else
					?>

						
					</div><!-- end chart section -->
				</div><!-- end chart -->
		</div><!-- end tab content container -->
	</div><!-- end concepts tab -->

<!-------------------------------------------------------->
<!-- S E N T I M EN T S   T A B                         -->
<!-------------------------------------------------------->	
	<!-- sentiment tab-->
	<div id="sentiment" <?php if(!$analyze_sentiment) {?> style="display:none"<?php } ?>>
		<div class="tab-sentiment">
		<div class="tabsummary">
			<h2 class="summarizer__heading">Magellan Sentiment</h2>
			<p class="summarizer__description">OpenText Magellan analyseert de zinnen in de tekst op sentimentaliteit.</p>
		</div>
		
		<div id="summarypies" class="wordcloud">
		</div>
		<!-- tab content container -->
			<h2 class="tab-heading">Sentiment verdeling</h2>
			<div class="chart">
				<div class="chart__section">
			<?php

			if ($response_data['sentiment_sentences_total'] == 0) {
			?>
			<div class="chart__header-row"><h3 class="chart__section-name">Er werden geen zinnen ontdekt.</h3></div>
			<?php
			} else {
			?>

			<script type="text/javascript">
			//Data for summary iHub-based report. Report design takes the data in with every execution.
			subjVals = 'fact|<?php echo $response_data['sentiment']->Percentages["fact"]; ?>,opinion|<?php echo $response_data['sentiment']->Percentages["opinion"]; ?>';
			toneVals = 'positive|<?php echo $response_data['sentiment']->Percentages["positive"]; ?>,negative|<?php echo $response_data['sentiment']->Percentages["negative"]; ?>,neutral|<?php echo $response_data['sentiment']->Percentages["neutral"]; ?>';

			</script>

			<?php

			$negCounter = 0;
			$posCounter = 0;
			$sentenceGroups = array(
			"positive" => "Positief",
			"negative" => "Negatief",
			"neutral" => "Neutraal"
			);
			$scoreLabels = array(
			"positive" => "Positieve toon Score",
			"negative" => "Negatieve toone Score",
			"neutral" => "Subjectiviteitsscore"
			);
			$scoreFields = array(
			"positive" => "PositiveToneScore",
			"negative" => "NegativeToneScore",
			"neutral" => "SubjectivityScore"
			);
			//ram - Added missing > on second to last closing div tag
			foreach ($sentenceGroups as $groupId => $groupText) {
			if (count($response_data["sentimentSentences_" . $groupId]) == 0) {
			if($groupText != "Neutraal"){ echo "<div class='chart__header-row'><h3 class='chart__section-name'>Geen <?php echo $groupText; ?> zinnen gevonden.</h3></div><div class='chart__row'><div class='chart__data-name chart_sentence'></div></div>"; }
			} else {

			if($groupText != "Neutraal"){
			?>
			<!-- section header row -->
			<div class="chart__header-row">
				<h3 class="chart__section-name"><?php echo $groupText; ?><span class="chart__count-badge"><?php echo count($response_data["sentimentSentences_".$groupId]); ?></span></h3>
				<div class="chart__col-head">Type</div>
				<div class="chart__col-head"></div>
				<div class="chart__col-head">Score</div>
			</div>
			<?php
			}           				
			foreach ($response_data["sentimentSentences_" . $groupId] as $oSentence) {

			 if($groupText == "Positief" && $posCounter < 1){
				 $posCounter++;
				 ?><script>positiveSent = "<?php echo addslashes($oSentence->Text) ?>";</script>
			<?php 
			 } else if($groupText == "Negatief" && $negCounter < 1){
				 $negCounter++;
				 ?><script>negativeSent = "<?php echo addslashes($oSentence->Text) ?>";</script> 
			<?php } 

			if($groupText != "Neutraal"){
			?>    
			<!-- section data rows -->
			<div class="chart__row">
				<div class="chart__data-name"><?php echo $oSentence->Text; ?></div>
				<div class="chart__col-relevancy"><?php echo ucfirst($oSentence->Subjectivity); ?></div>
				<div class="chart__col-sentiment chart__col-sentiment--positive"></div>
				<div class="chart__col-relevancy"><?php 
					if($scoreFields[$groupId] == "PositiveToneScore") {
						echo (float)$oSentence->PositiveToneScore; 
					} elseif ($scoreFields[$groupId] == "NegativeToneScore") {
						echo (float)$oSentence->NegativeToneScore; 
					} else {
						echo (float)$oSentence->SubjectivityScore; 
					}    
					?></div>
			</div> 
					 
			<?php
			}
			}
			} // end else
			} // end loop
			} // end else
			?>

			</div><!-- end chart section -->
			</div><!-- end chart -->
		</div><!-- end tab content container -->
	</div><!-- end sentiment tab -->




<script src="../js/drawCharts.js"></script>


	</div><!-- end responsive tab control -->
	</div><!-- end results tabs -->
</div>
<?php } ?>