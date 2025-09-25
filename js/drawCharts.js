	if (myEntityRels == '[') {
		myEntityRels = '[""]';
	} else {
		myEntityRels = myEntityRels.substring(0, myEntityRels.length - 1) + ']';
	};
	if (myConceptRels == '[') {
		myConceptRels = '[""]';
	} else {
		myConceptRels = myConceptRels.substring(0, myConceptRels.length - 1) + ']';
	};
    myEntities = myEntities.substring(0, myEntities.length - 1) + ']';
    myConcepts = myConcepts.substring(0, myConcepts.length - 1) + ']';

    var posBox = $('.summary__sentence_positive');
    var negBox = $('.summary__sentence_negative');
	//ram - added try/catch to skip over error and allow word cloud to be created
	
    try {
        posBox.empty().append(positiveSent);
        negBox.empty().append(negativeSent);
    }catch (err){
        console.error(err, err.stack);
    }

	if (!actuate.isInitialized()) {
		actuate.load('viewer');
		var reqOps = new actuate.RequestOptions( );
		reqOps.setRepositoryType(actuate.RequestOptions.REPOSITORY_ENCYCLOPEDIA);
		reqOps.setCustomParameters({});
		actuate.initialize( 'https://td-mag.eimdemo.com/OTMJC/', reqOps, null,null, afterInit, afterFailure); 
	} else {
		afterInit()
	};
	

	function afterFailure(){

		//alert("initialize failed!");
		
	}
	
  	function afterInit(){
		//alert("initialize succeeded!");
		if (typeof viewer1 != 'undefined') {
			viewer1.cleanup();
			viewer2.cleanup();
			viewer3.cleanup();
		}
		var uiConfig = new actuate.viewer.UIConfig();
		var panel = new actuate.viewer.ScrollPanel();
		panel.setMouseScrollingEnabled(false);
		panel.setPanInOutEnabled(false);
		uiConfig.setContentPanel(panel);
		viewer1 = new actuate.Viewer("wordcloud", uiConfig);

		viewer1.setReportName("Responsive Word Cloud.rptdesign");
		viewer1.setParameters({pTerms:myEntities,pCounts:myEntityRels,pDocID:"wordcloud"});
		viewer1.setReportletBookmark("wordcloud");
		viewer1.setWidth(1050);
		viewer1.setHeight(375);
		var uiOpts = new actuate.viewer.UIOptions();
		uiOpts.enableToolBar(false);
		viewer1.setUIOptions(uiOpts);
		viewer1.setContentMargin(20);
		viewer1.submit(afterFirstCloud);
	}


  	function afterFirstCloud(){
		var uiConfig = new actuate.viewer.UIConfig();
		var panel = new actuate.viewer.ScrollPanel();
		panel.setMouseScrollingEnabled(false);
		panel.setPanInOutEnabled(false);
		uiConfig.setContentPanel(panel);
		viewer2 = new actuate.Viewer("wordcloud2", uiConfig);    				

    	viewer2.setReportName("Responsive Word Cloud.rptdesign");
    	viewer2.setParameters({pTerms:myConcepts,pCounts:myConceptRels,pDocID:"wordcloud2"});
    	viewer2.setReportletBookmark("wordcloud");
    	viewer2.setWidth(1050);
    	viewer2.setHeight(375);
    	var uiOpts = new actuate.viewer.UIOptions();
		uiOpts.enableToolBar(false);
    	viewer2.setUIOptions(uiOpts);
		viewer2.setContentMargin(20);
    	viewer2.submit(afterSecCloud);
  	}

  	function afterSecCloud(){
		var uiConfig = new actuate.viewer.UIConfig();
		var panel = new actuate.viewer.ScrollPanel();
		panel.setMouseScrollingEnabled(false);
		panel.setPanInOutEnabled(false);
		uiConfig.setContentPanel(panel);
		viewer3 = new actuate.Viewer("summarypies", uiConfig);

		viewer3.setReportName("OTCASummaryPies.rptdesign");
		viewer3.setParameters({Subjectivity:subjVals,Tone:toneVals});
		viewer3.setReportletBookmark("pies");
		viewer3.setWidth(1050)
		viewer3.setHeight(300);
		var uiOpts = new actuate.viewer.UIOptions();
		uiOpts.enableToolBar(false);
		viewer3.setUIOptions(uiOpts);
		viewer3.setContentMargin(20);
		loaded = true;
		viewer3.submit();
  	
  	}