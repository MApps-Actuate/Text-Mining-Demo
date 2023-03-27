<!------------------------------------------------------------->
<!-- P L E A S E   R E A D   T H I S   B E F O R E           -->
<!--                                                         -->
<!-- This is a localized page                                -->
<!-- It uses localizion propertie file in the locale folder  -->
<!-- If you modify the link to the page, do it carefully     -->
<!------------------------------------------------------------->	
    <?php  include 'process_TME.php';
			process( $_REQUEST['lang'] ); ?>
    <?php if ($error) { ?>
    <div class="messages error"><?php print_r($error); ?></div>
    <?php }?>			

    
<script>
    var myWords = '[';
    var myCats = '[';
    var myConceptRels = '[';
    var myEntityRels = '[';
    var myConcepts = '[';
    var myEntities = '[';
    var negativeSent = "<span data-l10n-id='no-negative'>No negative sentence found.</span>";
    var positiveSent = "<span data-l10n-id='no-positive'>No positive sentence found.</span>";
</script>	

<!-------------------------------------------------------->
<!-- D I S P L A Y   R E S U L T S                      -->
<!-------------------------------------------------------->
<div id="result_container">
	<div class="card">

<!-------------------------------------------------------->
<!-- H E A D E R                                        -->
<!-------------------------------------------------------->
<!-- summary -->
<div class="summary">
	<div class="summary__header">
		<h2 class="summary__headline" data-l10n-id="summary__headline">Analysis summary</h2>
			<div><span class="summary__language" data-l10n-id="summary__language">Language</span>: <span class="summary__language--language-name" data-l10n-id="language-<?php echo $language_response_data['languageName'] ?>"><?php echo $language_response_data['languageName'] ?></span>
			</div>

			<div><span class="summary__tone" data-l10n-id="summary__tone">Overall tone</span>: <span class="<?php 
				if ($response_data['sentiment_analyzed']) {
					if ($response_data['sentiment']->Tone == "negative") { echo "summary__tone--negative"; 
					} else if ($response_data['sentiment']->Tone == "positive") { echo "summary__tone--positive";  
					} else { echo "summary__tone--neutral"; } 
					?>" data-l10n-id="<?php 
					if ($response_data['sentiment']->Tone == "negative") { 
						echo "summary__tone--negative"; 
					} else if ($response_data['sentiment']->Tone == "positive") { 
						echo "summary__tone--positive";  
					} else { 
						echo "summary__tone--neutral"; 
					}
					?>"><?php echo ucfirst($response_data['sentiment']->Tone); 
				} ?></span>
			</div>
	</div>

	<div class="summary__most-popular">
	
		<?php if ($response_data['entities_total']==0) { ?>
			<div data-l10n-id="no-entity">No entities were detected.</div>
		<?php } else {
			$entityGroups = array();
			if ($configuration == 'standard') {
				$entityGroups["PN"] = "People";
				$entityGroups["GL"] = "Places";
				$entityGroups["ON"] = "Organizations";
			}
			$counterthis = 0;
/* TODO remove those that don't apply, per language? */
			foreach ($entityGroups as $groupId => $groupText) {
				$top = count($response_data["entities_" . $groupId]);
				if (count($response_data["entities_" . $groupId]) == 0) { ?>
					<div class="summary__popular-col">
						<h3 class="summary__popular-col-heading" data-l10n-id="most-relevant-<?php echo $groupText; ?>">Most Relevant <?php echo $groupText; ?></h3>
						<p class="summary__popular-col-csv"><span data-l10n-id="no-entry-<?php echo $groupText; ?>">No entry found for <?php echo $groupText; ?></span></p></div>
						<?php 
				} else {?>
					<div class="summary__popular-col">
						<h3 class="summary__popular-col-heading" data-l10n-id="most-relevant-<?php echo $groupText; ?>">Most Relevant <?php echo $groupText; ?></h3>
						<p class="summary__popular-col-csv">
				
					<?php 
					foreach ($response_data["entities_" . $groupId] as $oEntity) {?>
						<script>myWords+='"<?php echo addslashes($oEntity->Name);?>",'</script><?php
						if($counterthis < 5){
							$counterthis++;
							echo $oEntity->Name;
							if($counterthis < 4 && $counterthis < $top){
								echo ",";
							}
						} 
					}  
					$counterthis = 0;?>
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
			<h3 class="summary__sentiment-heading summary__sentiment-heading--positive" data-l10n-id="summary__sentiment-heading--positive">Most positive sentence</h3>
			<p class="summary__sentence_positive"></p>
		</div>

		<!-- Most negative sentence -->
		<div class="summary__sentiment-col">
			<h3 class="summary__sentiment-heading summary__sentiment-heading--negative" data-l10n-id="summary__sentiment-heading--negative">Most negative sentence</h3>
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
				<li><a href="#topics" data-l10n-id="topics">Topics & Summary</a></li>
			<?php };
			if($analyze_entity) {?>
				<li><a href="#entities" data-l10n-id="entitites">Entities</a></li>
			<?php };
			if($analyze_concept) {?>
				<li><a href="#concepts" data-l10n-id="concepts">Concepts</a></li>
			<?php };
			if($analyze_sentiment) {?>
				<li><a href="#sentiment" data-l10n-id="sentiment">Sentiment</a></li>
			<?php }; ?>
		</ul>
<!-------------------------------------------------------->
<!-- T O P I C S   T A B                                -->  
<!-------------------------------------------------------->
		<div id="topics" <?php if(!$analyze_category && !$analyze_summary) {?> style="display:none"<?php } ?>>
			<div class="tab-topics">
				<div class="tabsummary">
					<h2 class="summarizer__heading" data-l10n-id="summarizer__heading">Magellan Topics and Summary</h2>
					<p class="topicsummarizer__description" data-l10n-id="topicsummarizer__description">OpenText Magellan automatically identifies high level topics (if any) and also summarize and entire document or excerpt of text. Topics are broader overarching concepts, while the summary provides a high level conceptual view.</p>
				</div>
				<h2 class="tab-heading" data-l10n-id="topics-found">Topics Found</h2>
				<div class="chart">
					<div class="chart__section">
						<?php if ($response_data['categories_total']==0) { ?>
						<h3 data-l10n-id="no-topics-found">No topics were detected.</h3>
						<?php } 
						else {
							$topicGroups = array();
    						if ($configuration == 'standard') {$topicGroups["IPTC"] = "Main Topics";}
							
							foreach ($topicGroups as $groupId => $groupText) {
								if (count($response_data["categories"][$groupId]) == 0) {
								} else { 
								?>
				<!-- section header row -->
						<div class="chart__header-row">
							<h3 class="chart__section-name"><span data-l10n-id="IPTC"><?php echo $groupText; ?></span><span class="chart__count-badge"><?php echo count($response_data["categories"][$groupId]) ?></span></h3>
							<div class="chart__col-head"></div>
							<div class="chart__col-head"></div>
							<div class="chart__col-head" data-l10n-id="weight">Weight</div>
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
						<h2 class="summarizer__heading" data-l10n-id="summarizer__heading">Magellan article summary</h2>
						<p class="subsummarizer__description" data-l10n-id="summarizer__description">OpenText Magellan has the ability to analyze and distill large amounts of textual content into a short summary.</p>
						<p class="summarizer__summary">
							<?php if ($response_data['summary']) {
								echo $response_data['summary'];
                              } else {
                                echo "<div data-l10n-id='no-summary'>No summary available.</div>";
                              }
							?></p>
					</div><!-- end summary -->
					<div class="summarizer-stats">
						<!-- Max number of sentences -->
						<div class="summarizer-stats__stat">
							<div class="summarizer-stats__stat-name" data-l10n-id="summarizer-max-sentence">Max number of sentences:</div>
							<div class="summarizer-stats__stat-value">5</div>
						</div>

					<!-- Max percent of input -->
						<div class="summarizer-stats__stat">
								<div class="summarizer-stats__stat-name" data-l10n-id="summarizer-max-input">Max percent of input text:</div>
								<div class="summarizer-stats__stat-value">10%</div>
						</div>

					<!-- Type of content -->
						<div class="summarizer-stats__stat">
								<div class="summarizer-stats__stat-name" data-l10n-id="summarizer-type-of-content">Type of content:</div>
								<div class="summarizer-stats__stat-value">News</div>
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
					<h2 class="summarizer__heading" data-l10n-id="entity-heading">Magellan Entities</h2>
					<p class="summarizer__description" data-l10n-id="entity-description">OpenText Magellan automatically identifies entities (people, places, and organizations) in the text.</p>
				</div>
				
				<div id="wordcloud" class="wordcloud"></div>

				<h2 class="tab-heading" data-l10n-id="entity-breakdown">Entity breakdown</h2>
				<div class="chart">
					<div class="chart__section">

						<?php if ($response_data['entities_total']==0) { ?>
							<div class="chart__header-row">
								<h3 class="chart__section-name" data-l10n-id="no-entity">No entities were detected.</h3>
							</div>
							<div class="chart__row">
								<div class="chart__data-name"></div>
								<script>myEntities+='"No Entities Detected",'</script>
							</div>
						<?php } else {
									$entityGroups = array();
								
									if ($configuration == 'standard') {
										$entityGroups["PN"] = "People";
										$entityGroups["GL"] = "Places";
										$entityGroups["ON"] = "Organizations";																			
									}
									$counterthis = 0;
									/* TODO remove those that don't apply, per language? */
									foreach ($entityGroups as $groupId => $groupText) {
										if (count($response_data["entities_" . $groupId]) == 0) {
										} else { ?>
										<div class="chart__header-row">
											<h3 class="chart__section-name"><span data-l10n-id="entity-<?php echo $groupText; ?>"><?php echo $groupText; ?></span><span class="chart__count-badge"><?php echo count($response_data["entities_".$groupId]) ?></span></h3>
											<div class="chart__col-head" data-l10n-id="relevancy">Relevancy</div>
											<div class="chart__col-head"></div>
											<div class="chart__col-head" data-l10n-id="frequency">Frequency</div>
										</div>
													
										<?php
											foreach ($response_data["entities_" . $groupId] as $oEntity) {
												if(count($response_data["entities_" . $groupId]) != 0){
											?>
												<script>myEntities+='"<?php echo addslashes($oEntity->Name);?>",';
												myEntityRels+='"<?php echo $oEntity->Frequency;?>",';
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
						<h2 class="summarizer__heading" data-l10n-id="concept-heading">Magellan Concepts</h2>
						<p class="summarizer__description" data-l10n-id="concept-description">OpenText Magellan automatically identifies complex and simple concepts in the text.</p>
				</div>
					
				<div id="wordcloud2" class="wordcloud"></div>
				<h2 class="tab-heading" data-l10n-id="concept-breakdown">Concept breakdown</h2>
				<div class="chart">
				<div class="chart__section">

				<?php if ($response_data['concepts_total'] == 0) { ?>
					<div class="chart__header-row"><h3 class="chart__section-name" data-l10n-id="no-concept">No concepts were detected.</h3></div>
					<script>myConcepts+='"No Concepts Detected",'</script>
				<?php
				} else {
				$conceptGroups = array(
				"complex" => "Complex Concepts",
				"simple" => "Simple Concepts"
				);
				foreach ($conceptGroups as $groupId => $groupText) {
				if (count($response_data["concepts_" . $groupId]) == 0) {
				echo "<div class='chart__header-row'><h3 class='chart__section-name'><span data-l10n-id='no-concept-'". $groupId ."'>" . $groupText . " None detected.</span><h3 class='chart__section-name'></h3></div>";
				} else {
				?>
					<!-- section header row -->
					<div class="chart__header-row">
						<h3 class="chart__section-name"><span data-l10n-id="concept-<?php echo $groupId; ?>"><?php echo $groupText; ?></span><span class="chart__count-badge"><?php echo count($response_data["concepts_" . $groupId]) ?></span></h3>
						<div class="chart__col-head" data-l10n-id="relevancy">Relevancy</div>
						<div class="chart__col-head"></div>
						<div class="chart__col-head" data-l10n-id="frequency">Freq.</div>
					</div>

				<?php
				foreach ($response_data["concepts_" . $groupId] as $oConcept) {
				?>
					<script>myConcepts+='"<?php echo addslashes($oConcept->Name) ;?>",';
						myConceptRels+='"<?php echo $oConcept->Frequency;?>",';
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
<!-- S E N T I M E N T S   T A B                         -->
<!-------------------------------------------------------->	
		<!-- sentiment tab-->
		<div id="sentiment" <?php if(!$analyze_sentiment) {?> style="display:none"<?php } ?>>
			<div class="tab-sentiment">
			<div class="tabsummary">
					<h2 class="summarizer__heading" data-l10n-id="sentiment-heading">Magellan Sentiment</h2>
					<p class="summarizer__description" data-l10n-id="sentiment-description">OpenText Magellan analyzes the sentences in the text for sentiment tonality.</p>
			</div>
			<div id="summarypies" class="wordcloud"></div>
			<!-- tab content container -->
			<h2 class="tab-heading" data-l10n-id="sentiment-breakdown">Sentiment breakdown</h2>
			<div class="chart">
				<div class="chart__section">
					<?php
					if ($response_data['sentiment_sentences_total'] == 0) {
						?>
					<div class="chart__header-row"><h3 class="chart__section-name" data-l10n-id="no-sentence">No sentences were detected.</h3>
					</div>
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
						"positive" => "Positive",
						"negative" => "Negative",
						"neutral" => "Neutral"
					);
					$scoreLabels = array(
						"positive" => "Positive Tone Score",
						"negative" => "Negative Tone Score",
						"neutral" => "Subjectivity Score"
					);
					$scoreFields = array(
						"positive" => "PositiveToneScore",
						"negative" => "NegativeToneScore",
						"neutral" => "SubjectivityScore"
					);
					

					
				//ram - Added missing > on second to last closing div tag
					foreach ($sentenceGroups as $groupId => $groupText) {
						if (count($response_data["sentimentSentences_" . $groupId]) == 0) {
							if($groupText != "Neutral"){ echo "<div class='chart__header-row'><h3 class='chart__section-name' data-l10n-id='no-". $groupId ."'>No ". $groupText ."sentences found.</h3></div><div class='chart__row'><div class='chart__data-name chart_sentence'></div></div>"; }
							
						} else {
							
											if($groupText != "Neutral"){
											?>
											<!-- section header row -->
											<div class="chart__header-row">
												<h3 class="chart__section-name"><span data-l10n-id="<?php echo $groupId; ?>"><?php echo $groupText; ?></span><span class="chart__count-badge"><?php echo count($response_data["sentimentSentences_".$groupId]); ?></span></h3>
												<div class="chart__col-head" data-l10n-id="sentiment-type">Type</div>
												<div class="chart__col-head"></div>
												<div class="chart__col-head" data-l10n-id="sentiment-score">Score</div>
											</div>
											<?php
											}           				
							foreach ($response_data["sentimentSentences_" . $groupId] as $oSentence) {
							   
											 if($groupText == "Positive" && $posCounter < 1){
												 $posCounter++;
												 ?><script>positiveSent = "<?php echo addslashes($oSentence->Text) ?>";</script>
											<?php 
											 } else if($groupText == "Negative" && $negCounter < 1){
												 $negCounter++;
												 ?><script>negativeSent = "<?php echo addslashes($oSentence->Text) ?>";</script> 
											<?php } 
											
											if($groupText != "Neutral"){
											?>    
											<!-- section data rows -->
											<div class="chart__row">
												<div class="chart__data-name"><?php echo $oSentence->Text; ?></div>
												<div class="chart__col-relevancy" data-l10n-id="<?php echo ucfirst($oSentence->Subjectivity); ?>"><?php echo ucfirst($oSentence->Subjectivity); ?></div>
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






	<script src="js/drawCharts.js"></script>	
	<script src="js/mtm-i10n.js"></script>
	<script>
		jQuery.when( i10n_init() ).done(function() {
		i10n_section("#result_container");
		})
	</script>	
	</div><!-- end responsive tab control -->
	</div><!-- end results tabs -->
</div>