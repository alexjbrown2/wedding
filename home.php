<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
  <title>Parallax Template - Materialize</title>

  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  
  
  <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <link href="https://fonts.googleapis.com/css?family=Amatic+SC|Dancing+Script:700|Sacramento" rel="stylesheet">
  <link href="css/map.css" type="text/css" rel="stylesheet" media="screen,projection"/>


</head>
<body>
  <nav class="white" role="navigation">
    <div class="nav-wrapper container">
      <a id="logo-container" href="#" class="brand-logo"><p>Alex and Amanda</p><span class='small-date'>10.14.2017</span></a>
      <ul class="right hide-on-med-and-down unburger-menu">
	<li><a href="#">About Us</a></li>
        <li><a href="#">The Venue</a></li>
	<li><a href="#">Wedding Party</a></li>
	<li><a href="#">Accommodations</a></li>
	<li><a href="#">Weekend Events</a></li>
      </ul>

      <ul id="nav-mobile" class="side-nav">
	<li><a href="#">About Us</a></li>
        <li><a href="#">The Venue</a></li>
      	<li><a href="#">Wedding Party</a></li>
	<li><a href="#">Accomodations</a></li>
	<li><a href="#footer" class="btn-quick">Weekend Events</a></li>
      </ul>
      <a data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
     <a class="side-nav-button-container"><div class="side-nav-button">&#x22CF;     Menu      &#x22CF;</div></a>
	</div>
  </nav>

  <div id="index-banner" class="parallax-container">
    <div class="section no-pad-bot">
      <div class="container-head">
        <br><br>
        <h1 class="header center amatic">We're getting married!</h1>
        <div class="row center">
          <h5 class="header col s12 light">Join us October 17, 2017 in Sterling, Virginia as we celebrate our special day!</h5>
        </div>
        <div class="row center">
          <a href="#footer" id="download-button" class="btn-quick btn-large waves-effect waves-light teal lighten-1">Quick Info</a>
        </div>
        <br><br>

      </div>
    </div>
    <div class="parallax"><img src="background1.jpg" alt="Unsplashed background img 1"></div>
  </div>


  <div class="container first-container">
    <div class="section">

      <!--   Icon Section   -->
      <div class="row">
        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center icon-head"><div class="icons first-icon"></div></h2>
            <h5 class="center">Schedule</h5>
	    <p>10.13.2017</p>
         	<ul class="light">
			<li><span>9:00a-3:00p</span>Wedding Party Golf Outing</li>
			<li><span>5:30p-8:30p</span>Rehearsal Dinner</li>
			<li><span>8:30-10:00p</span>Party at the Groomsmen cabin</li>
		</ul>
	    <p>10.14.2017</p>
		<ul class="light">
                        <li><span>8:00a-12:00p</span>More Golf. Just Groomsmen this time.</li>
                        <li><span>1:00p-3:00p</span>Everyone gets ready.</li>
                        <li><span>4:00p-5:00p</span>Pictures</li>
                        <li><span>5:00p-5:15p</span>Wedding Ceremony</li>
                        <li><span>5:20p-11:00p</span>Greatest party of all time.</li>
                </ul>
	    <p>10.15.2017</p>
		<ul class="light">
                        <li><span>9:30a-11:00a</span>Light Brunch</li>
                </ul>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center icon-head"><div class="icons second-icon"></div></h2>
            <h5 class="center">Places to Stay</h5>

            <p class="light">By utilizing elements and principles of Material Design, we were able to create a framework that incorporates components and animations that provide more feedback to users. Additionally, a single underlying responsive system across all platforms allow for a more unified user experience.</p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center icon-head"><div class="icons third-icon"></div></h2>
            <h5 class="center">Registry Information</h5>

            <p class="light">We have provided detailed documentation as well as specific code examples to help new users get started. We are also always open to feedback and can answer any questions a user may have about Materialize.</p>
          </div>
        </div>
      </div>

    </div>
  </div>


  <div class="parallax-container valign-wrapper">
    <div class="section no-pad-bot">
      <div class="container-para">
        <div class="row center">
          <h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
        </div>
      </div>
    </div>
    <div class="parallax"><img src="background2.jpg" alt="Unsplashed background img 2"></div>
  </div>

  <div class="container directions-container">
    <div class="section">

      
	<div class="row amatic">
	<h2>Directions</h2>
	</div>
	<div class="row">

        <div class="col s12 google-maps">
          
	    <div id="floating-panel">
      
     		<input type="text" id="origin-input" placeholder="Starting Point"></input> 
      		<select id="end">
		<option>Select Endpoint</option>
        	<option value="47001 Fairway Dr, Sterling, VA 20165">Algonkian Regional Park</option>
        	<option value="21481 Ridgetop Cir, Sterling, VA 20166">Hyatt Sterling Dulles North</option>
      		<option value=" 45500 Majestic Drive,  Dulles, VA 20166">Courtyard Dulles Town Center</option>
		</select>
		<div id="get-directions" class="get-directions btn">Get Directions</div>
    	</div>
    <div id="right-panel"></div>
    <div id="map"></div>
          
        </div>
      </div>

    </div>
  </div>


  <div class="parallax-container valign-wrapper">
    <div class="section no-pad-bot">
      <div class="container-para">
        <div class="row center">
          <h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
        </div>
      </div>
    </div>
    <div class="parallax"><img src="background3.jpg" alt="Unsplashed background img 3"></div>
  </div>



  <div class="container party-container">
    <div class="section">
	<div class="row amatic">
        <h2>Wedding Party</h2>
        </div>
        
	<div class="row row-quote">
		
		<div class="close-x">X</div>
		
		<div class="person-box"></div>
		
		<div class="controls">
			<div class="go-left">&lArr;</div>
			<div class="go-right">&rArr;</div>
		</div>

		</div>
      <div class="row row-diamonds">
        
          <div class="col s6 picture-div">
		<div class="diamond d-left pic1 bm-purp" data-index="0" data-name="Kristin_Zook"></div>
		<div class="diamond d-right pic2 bm-purp" data-index="1" data-name="Alex_Roderick"></div>
		<div class="diamond d-left pic3 bm-purp" data-index="2" data-name="Jennifer_Zook"></div>
		<div class="diamond d-right pic4 bm-purp"data-index="3" data-name="Alexa_Kirland"></div>
		<div class="diamond d-left pic5 bm-purp" data-index="4" data-name="Alyssa_Romero"></div>
		<div class="diamond d-right pic6 gm-blue" data-index="5" data-name="Austin_Brown"></div>
		<div class="diamond d-left pic7 gm-blue" data-index="6" data-name="Chris_Kirland"></div>
		<div class="diamond d-right pic8 gm-blue" data-index="7" data-name="Pat_Jewell"></div>
		<div class="diamond d-left pic9 gm-blue" data-index="8" data-name="Matt_Pingol"></div>
		<div class="diamond d-right pic10 gm-blue" data-index="9" data-name="Jay_Kirland"></div>
			
		</div>
        
      </div>

    </div>
  </div>


  <div class="parallax-container valign-wrapper">
    <div class="section no-pad-bot">
      <div class="container-para">
        <div class="row center">
          <h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
        </div>
      </div>
    </div>
    <div class="parallax"><img src="background3.jpg" alt="Unsplashed background img 3"></div>
  </div>


  <div class="container fb-container">
    <div class="section">
	<div class="row amatic">
	<h2>Get Connected</h2>
	</div>
      <div class="row">
        <div class="col s12 center">
          <h3><i class="mdi-content-send brown-text"></i></h3>
          <h4></h4>
          <p class="left-align light">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam scelerisque id nunc nec volutpat. Etiam pellentesque tristique arcu, non consequat magna fermentum ac. Cras ut ultricies eros. Maecenas eros justo, ullamcorper a sapien id, viverra ultrices eros. Morbi sem neque, posuere et pretium eget, bibendum sollicitudin lacus. Aliquam eleifend sollicitudin diam, eu mattis nisl maximus sed. Nulla imperdiet semper molestie. Morbi massa odio, condimentum sed ipsum ac, gravida ultrices erat. Nullam eget dignissim mauris, non tristique erat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>
        </div>
      </div>

    </div>
  </div>


  <div class="parallax-container valign-wrapper">
    <div class="section no-pad-bot">
      <div class="container-para">
        <div class="row center">
          <h5 class="header col s12 light">A modern responsive front-end framework based on Material Design</h5>
        </div>
      </div>
    </div>
    <div class="parallax"><img src="background3.jpg" alt="Unsplashed background img 3"></div>
  </div>



  <footer id="footer" class="scrollSpy page-footer teal">
    <div class="container-foot">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">Quick Info</h5>
          <div class="grey-text text-lighten-4">
		<ul>	
		<li><span>Who:</span> Alex & Amanda</li>
		<li><span>When:</span> Oct. 14, 2017 at 4:00pm</li>
		<li><span>Where:</span> 47001 Fairway Dr, Sterling, VA 20165</li>
		</ul>
		</div>


        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Book Hotel</h5>
          <ul>
            <li><a class="white-text" href="#!">Book Hotel - Hyatt Dulles North</a></li>
            <li><a class="white-text" href="#!">Book Hotel - Courtyard Dulles Town Center</a></li>
            <li><a class="white-text" href="#!">Link 3</a></li>
            <li><a class="white-text" href="#!">Link 4</a></li>
          </ul>
        </div>
        <div class="col l3 s12">
          <h5 class="white-text">Connect</h5>
          <ul>
            <li><a class="white-text" href="#!">Link 1</a></li>
            <li><a class="white-text" href="#!">Link 2</a></li>
            <li><a class="white-text" href="#!">Link 3</a></li>
            <li><a class="white-text" href="#!">Link 4</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
      Made by <a class="brown-text text-lighten-3" href="http://www.al3xbrown.com">Alex Brown</a>
      </div>
    </div>
  </footer>


  <!--  Scripts-->
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="js/materialize.js"></script>
  <script src="js/init.js"></script>
 <script src="js/wedding-directions.js"></script> 
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCj7_vOIhZlD5Ds-8RDXwFXh8xsnGydztQ&libraries=places&callback=initMap"></script>

  </body>
</html>
