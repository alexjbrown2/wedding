$(function(){

function getMobileOperatingSystem() {
  var userAgent = navigator.userAgent || navigator.vendor || window.opera;

      // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return "Windows Phone";
    }

    if (/android/i.test(userAgent)) {
        return "Android";
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "iOS";
    }

    return "unknown";
}

var windowHeight = $(window).height();

$('.side-nav-button').css({'top': windowHeight / 2});
//Parrallax init
$(window).scroll(function () {
    if($(window).width() > 600){

    $(".image-one").css("background-position","65% " + (($(this).scrollTop() / 2) - 48) + "px");
    $(".image-two").css("background-position","47% " + ((($(this).scrollTop()- $('.image-two').offset().top) / 4)) + "px");
    $(".image-three").css("background-position","55% " + ((($(this).scrollTop()- $('.image-three').offset().top) / 4)) + "px");
    $(".image-four").css("background-position","66% " + ((($(this).scrollTop()- $('.image-four').offset().top) / 4)) + "px");
//$(".image-five").css("background-position","45% " + ((($(this).scrollTop()- $('.image-five').offset().top) /5)-80) + "px");
	}
})





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

