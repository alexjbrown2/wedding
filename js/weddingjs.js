$(function(){

	var party = [];

	$.getJSON('quote.js', function(data){
		var json =$.parseJSON(data);
		party = json;
		console.log(party);
	})

$('.button-collapse').sideNav();
$('.side-nav-button').on('click', function(){
	$('.button-collapse')[0].click();
	})

$(function(){
	var height = $('header').height();
	var width = $('header').width();

	$('header img').css({'height' : height});
	console.log(height, width);
	})
		
	
	$('.scrollSpy').scrollSpy();	
	$('.btn-quick').on('click', function(){

	var hash = this.hash;
	console.log(hash);	
	$('html, body').animate({
      		scrollTop: $(hash).offset().top
    	}, 800, function(){
		window.location.hash = hash;
    		});


	})



})

$('.diamond').on('click', function(){
		
})
		
})
