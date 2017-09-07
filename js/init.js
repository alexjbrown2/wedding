$(function(){
var json;

var nameArray = [];
$.get( "js/quote.json", function( data ) {
  json = data;
  $.each(json, function(i, val){
	nameArray.push(Object.keys(val));
	})
 });
	$('.close-x').on('click', function(){
		$('.row-quote').css('display', 'none');
	});
	
		

	let loadThisPerson = (img, quote, name, current, arrayNum) => {
		$('.person-box').html('');
		if(arrayNum != undefined){
		var innerArray = nameArray[0];
		var name = innerArray[arrayNum];
                var stuff = json.party[name];
                var current = $(this);
                var img = stuff.img;
                var quote = stuff.quote;
		}else{
		var nonum = "true";
		}
		let image = $("<img />").attr('src', img).addClass('inner-image');
		let imageBox = $('<div></div>').addClass('image-box').append(image);			
		let cleanName = name.replace('_', ' ');
		
		let currentNum = nameArray[0].indexOf(name);
		let prevNum;
		let nextNum;
		let realArray = nameArray[0]
		if(currentNum == (realArray.length-1)){
		prevNum = currentNum - 1;
		nextNum = 0;}
		else if (currentNum == 0){
		prevNum = realArray.length - 1;
		nextNum = currentNum + 1;
		}else{
		prevNum = currentNum - 1;
		nextNum = currentNum + 1;
		}
		$('.go-left').on('click', function(){
			loadThisPerson(null,null,null,null,prevNum);
			})
		$('.go-right').on('click', function(){
			loadThisPerson(null,null,null,null,nextNum);
			})
		let nameTitle = $("<h2></h2>").addClass('name-title').html(cleanName);
		let quoteBox = $("<div></div>").addClass('quote-box').html('<p>' + quote + '</p>');
		$('.person-box').append(imageBox, nameTitle, quoteBox);
	}
	
	
	let openPerson = (img, quote, name, current) => {
	if($('.row-quote').css('display') == 'none'){
		$('.row-quote').css('display', 'flex');
		$('.person-box').html('');
		loadThisPerson(img, quote, name, current);
	}else{
		$('.person-box').html('');
		loadThisPerson(img, quote, name, current);
		}
	};

	$('.diamond').on('click', function(){
		var name = $(this).data('name');
		var stuff = json.party[name];
		var current = $(this);		
		var img = stuff.img;
		var quote = stuff.quote;
		openPerson(stuff.img, stuff.quote, name, current);
	})

    
	$('.button-collapse').sideNav();
    	$('.parallax').parallax();
	$('.side-nav-button').on('click', function(){
	$('.button-collapse').click();
	})
    $(window).on('scroll', function(){
	let scrtop = $(window).scrollTop();
	if (scrtop < 157){
		$('.side-nav-button').css({opacity : 0}).addClass('disabled');
		
	}
	else if (scrtop > 157){
		$('.side-nav-button').css({opacity : 1}).removeClass('disabled');


	}
	})    
$(function(){
        var height = $('.nav-wrapper').height();
        var width = $('.nav-wrapper').width();

        $('header img').css({'height' : height});
        console.log(height, width);
        })


        $('.scrollSpy').scrollSpy();
        $('.btn-quick').on('click', function(e){
	e.preventDefault();
        var hash = this.hash;
        console.log(hash);
        $('html, body').animate({
                scrollTop: $(hash).offset().top
        }, 800, function(){
                window.location.hash = hash;
                });


        })



}); // end of document ready

