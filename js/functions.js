jQuery( document ).ready(function( $ ) {
	
    //define state-container
    var emptyState = $('.empty-state');
    var resultState = $('.result-state');
    var textarea = $('.magellan-input__textarea');

    //click functions for Analyze button
    $('#analyze-now').click(function(e) {
        //prevent href from functioning normally w/ page refresh
    	e.preventDefault();
        if(textarea.val().length > 100000){
        	
        	$('.alert').show();
        	return;
        } else {
        	$('.alert').hide();
        }
        var url = 'process.php?op=AnalyzeText&configuration=standard&lang=' + (language == null ? '' : language);
        
        var posting = $.post( url, { text_to_analyze: textarea.val() } );
        
        // Put the results in a div and reset tabs
        posting.done(function( data ) {      	
			resultState.empty().append( data );
			$('.tab-control').responsiveTabs({
					startCollapsed: 'tabs',
					setHash: false
			});
        });        
        
        // switch between empty state and result state
        emptyState.addClass('empty-state--is-hidden');
        setTimeout(function() {
            emptyState.addClass('empty-state--is-none');
        }, 400);

        setTimeout(function() {
            resultState.addClass('result-state--is-block');
        }, 410);

        setTimeout(function() {
            resultState.addClass('result-state--is-visible');
        }, 450);
        	//$("#thisthat").html(string);
        //scroll to results
        scrollTo('.results-container');
        
    });
    
    //scroll to function
    function scrollTo(target) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 50
            }, 1200);
    }

    //initialize tabs
    $('.tab-control').responsiveTabs({
        startCollapsed: 'tabs',
        setHash: false
    });
    

});