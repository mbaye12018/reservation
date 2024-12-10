
<?php
// Connexion à la base de données (vous devez adapter les détails de la connexion)
$host = 'localhost'; 
$dbname = 'reservation'; 
$username = 'root'; 
$password = ''; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer le nombre d'inscriptions dans la table 'participant'
    $stmt = $pdo->query("SELECT COUNT(*) AS total_participants FROM participant");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalParticipants = $row['total_participants'];

    // Nombre total de places disponibles
    $totalPlaces = 1000;
    // Calculer les places restantes
    $placesRestantes = $totalPlaces - $totalParticipants;

  
} catch (PDOException $e) {
    echo 'Erreur de connexion : ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html class="wide wow-animation" lang="en">
  <head>
    <title>Home</title>
    <meta name="format-detection" content="telephone=no">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="images/logo.jpeg" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dom-to-image-more@2.9.0/dist/dom-to-image-more.min.js"></script>




  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Inclure jQuery -->

    <!-- Stylesheets-->
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Poppins:400,500,600%7CTeko:300,400,500%7CMaven+Pro:500">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <link rel="stylesheet" href="css/style.css">
    <style>.ie-panel{display: none;background: black;padding: 10px 0;box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3);clear: both;text-align:center;position: relative;z-index: 1;} html.ie-10 .ie-panel, html.lt-ie-10 .ie-panel {display: block;}
 .text-width-large {
  font-size: 18px;
  display: flex;
  align-items: center;
  gap: 10px;
}
body
{
background-color:black;
}
.header
{
background-color:black;
}

.arrow-button {
  display: inline-flex;
  align-items: center;
  background-color: #007bff;
  color: #fff;
  padding: 10px;
  border-radius: 50%;
  font-size: 24px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.arrow-button .arrow {
  transition: transform 0.3s ease;
}

.arrow-button:hover {
  background-color: #0056b3;
}

.arrow-button:hover .arrow {
  transform: translateX(5px);
}

/* Styles de base pour le fond gris et la mise en page responsive */
.responsive-section {
  background-color: black; /* Couleur de fond gris */
  padding: 20px 0;
}

.swiper-slide-caption {
  color: #333;
  text-align: center;
  padding: 20px;
}

/* Largeur maximale pour les écrans moyens */
@media (max-width: 768px) {
  .swiper-slide-caption h1 {
    font-size: 24px;
  }

  .swiper-slide-caption h5 {
    font-size: 18px;
  }

  .text-width-large {
    font-size: 16px;
  }

  .button.button-primary.button-ujarak {
    padding: 10px 20px;
    font-size: 16px;
  }
}

/* Largeur maximale pour les petits écrans */
@media (max-width: 480px) {
  .swiper-slide-caption h1 {
    font-size: 20px;
  }

  .swiper-slide-caption h5 {
    font-size: 16px;
  }

  .text-width-large {
    font-size: 14px;
  }

  .button.button-primary.button-ujarak {
    padding: 8px 16px;
    font-size: 14px;
  }
}



@import url(https://fonts.googleapis.com/css?family=Bungee+Spice);


h2 {
  font-family: "Bungee Spice";
  font-size: 30px;
}
.alt2 {
  font-palette: --pinkAndGray;
}

#countdown {
  font-weight: bold;
  font-size: 5rem;
  color: #fff; 
  background: ;
  padding: 10px 20px;
  border-radius: 8px;
  display: inline-block;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  margin-top: 15px;
}


@keyframes countdownBlink {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

#countdown {
  animation: countdownBlink 1s infinite;
}


@media (max-width: 768px) {
  #countdown {
    font-size: 1.5rem;
    padding: 8px 16px;
  }
}

@media (max-width: 480px) {
  #countdown {
    font-size: 1.2rem;
    padding: 6px 12px;
  }
}
*/


  #generateBadgeButton {
    background-color: green !important; 
    color: green; /* Pour un meilleur contraste */
    border: none; /* Optionnel : pour supprimer une bordure si nécessaire */
    padding: 10px 20px; /* Optionnel : pour ajuster l'espacement interne */
    border-radius: 5px; /* Optionnel : pour des coins arrondis */
    cursor: pointer; /* Pour indiquer qu'il est cliquable */
  }

  #generateBadgeButton:hover {
    background-color: darkgreen; /* Couleur différente au survol */
  }

  .buttonadd{
    background-color: green !important; 
    color: white; /* Pour un meilleur contraste */
    border: none; /* Optionnel : pour supprimer une bordure si nécessaire */
    padding: 10px 20px; /* Optionnel : pour ajuster l'espacement interne */
    border-radius: 5px; /* Optionnel : pour des coins arrondis */
    cursor: pointer; /* Pour indiquer qu'il est cliquable */
  }





    
    </style>
    
  </head>
  <body>
    <div class="ie-panel"><a href="http://windows.microsoft.com/en-US/internet-explorer/"><img src="images/ie8-panel/warning_bar_0000_us.jpg" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today."></a></div>
    <div class="preloader">
      <div class="preloader-body">
        <div class="cssload-container"><span></span><span></span><span></span><span></span>
        </div>
      </div>
    </div>
    <div class="page">
      <div id="home">

        <!-- Page Header-->
        <header class="section page-header" style="background-color:black">
          <!-- RD Navbar-->
          <div class="rd-navbar-wrap"  style="background-color:black">
            <nav  style="background-color:#222222" class="rd-navbar rd-navbar-classic" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed" data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static" data-lg-device-layout="rd-navbar-fixed" data-xl-layout="rd-navbar-static" data-xl-device-layout="rd-navbar-static" data-xxl-layout="rd-navbar-static" data-xxl-device-layout="rd-navbar-static" data-lg-stick-up-offset="46px" data-xl-stick-up-offset="46px" data-xxl-stick-up-offset="46px" data-lg-stick-up="true" data-xl-stick-up="true" data-xxl-stick-up="true">
              <div class="rd-navbar-main-outer">
                <div class="rd-navbar-main">
                  <!-- RD Navbar Panel-->
                  <div class="rd-navbar-panel">
                    <!-- RD Navbar Toggle-->
                    <button class="rd-navbar-toggle" data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                    <!-- RD Navbar Brand-->
                    <div class="rd-navbar-brand"><a class="brand" href="index.html"><img src="images/10.png" alt="" width="223" height="50"/></a></div>
                   
                  </div>
                  <div class="rd-navbar-main-element">
                    <div class="rd-navbar-nav-wrap">
                      <!-- RD Navbar Share
                      <div class="rd-navbar-share fl-bigmug-line-share27" data-rd-navbar-toggle=".rd-navbar-share-list">
                        <ul class="list-inline rd-navbar-share-list">
                          <li class="rd-navbar-share-list-item"><a class="icon fa fa-facebook" href="#"></a></li>
                          <li class="rd-navbar-share-list-item"><a class="icon fa fa-twitter" href="#"></a></li>
                          <li class="rd-navbar-share-list-item"><a class="icon fa fa-google-plus" href="#"></a></li>
                          <li class="rd-navbar-share-list-item"><a class="icon fa fa-instagram" href="#"></a></li>
                        </ul>
                      </div>-->
                      <ul class="rd-navbar-nav">
                         <!-- Stylesheets
                        <li class="rd-nav-item active"><a class="rd-nav-link" href="#home">Home</a></li>
                        <li class="rd-nav-item"><a class="rd-nav-link" href="#services">Services</a></li>
                        <li class="rd-nav-item"><a class="rd-nav-link" href="#projects">Projects</a></li>
                        <li class="rd-nav-item"><a class="rd-nav-link" href="#team">Team</a></li>
                        <li class="rd-nav-item"><a class="rd-nav-link" href="#news">News</a></li>
                        <li class="rd-nav-item"><a class="rd-nav-link" href="#contacts">Contacts</a></li>
                      </ul>-->
                    </div>
                  </div>
                </div>
              </div>
            </nav>
          </div>
        </header>
        </br>
        <h3 style="color:#155a0a ;text-align:center;font-family:Bungee Spice;font-size:50px;font-weight:bold" > Finale et cérémonie de remise de lauréats</h3>
    
        <img src="images/10.png" alt="Gov'awards Logo" style="height: 70px;">
        <div class="timer">
        <div class="time-segment">
            <span id="days" class="time-value glow-effect">00</span>
            <span class="time-label">Jours</span>
        </div>
        <div class="separator glow-effect">:</div>
        <div class="time-segment">
            <span id="hours" class="time-value glow-effect">00</span>
            <span class="time-label">Heures</span>
        </div>
        <div class="separator glow-effect">:</div>
        <div class="time-segment">
            <span id="minutes" class="time-value glow-effect">00</span>
            <span class="time-label">Minutes</span>
        </div>
        <div class="separator glow-effect">:</div>
        <div class="time-segment">
            <span id="seconds" class="time-value glow-effect">00</span>
            <span class="time-label">Secondes</span>
        </div>
    </div>
    </div>
    <script>
      function startTimer() {
    const daysElement = document.getElementById('days');
    const hoursElement = document.getElementById('hours');
    const minutesElement = document.getElementById('minutes');
    const secondsElement = document.getElementById('seconds');
    // Durée initiale en secondes (exemple : 3 jours, 2 heures, 30 minutes)
    let totalSeconds = 16 * 24 * 3600 + 21 * 3600 + 54 * 60;
    function updateTimer() {
        const days = Math.floor(totalSeconds / (24 * 3600));
        const hours = Math.floor((totalSeconds % (24 * 3600)) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        // Mettre à jour le contenu des éléments
        daysElement.textContent = String(days).padStart(2, '0');
        hoursElement.textContent = String(hours).padStart(2, '0');
        minutesElement.textContent = String(minutes).padStart(2, '0');
        secondsElement.textContent = String(seconds).padStart(2, '0');
        // Réduire le temps si toujours actif
        if (totalSeconds > 0) {
            totalSeconds--;
        } else {
            clearInterval(timerInterval);
        }
    }
    updateTimer();
    const timerInterval = setInterval(updateTimer, 1000);
}
startTimer();
    </script>

    <style>.timer {
    display: flex;
    align-items: center;
    justify-content: center;
    background: black;
    padding: 20px 40px;
    border-radius: 15px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    text-align: center;
    color:white;
}
/* Time Segment */
.time-segment {
    margin: 0 10px;
}
.time-value {
  color:white;
    font-size: 8rem;
    font-weight: bold;
    display: block;
    position: relative;
    margin-top:-35%;
}
/* Glow Effect */
.glow-effect {
    text-shadow: 0 0 5px yellow, 0 0 10px yellow, 0 0 20px yellow, 0 0 30px yellow, 0 0 40px yellow;
    animation: glowing 2s infinite;
}
@keyframes glowing {
    0% {
        text-shadow: 0 0 5px yellow, 0 0 8px #ac2616 ;
    }
    50% {
        text-shadow: 0 0 10px yellow, 0 0 8px #ac2616 ;
    }
    100% {
        text-shadow: 0 0 5px yellow, 0 0 8px #ac2616 ;
    }
}
/* Separator */
.separator {
    font-size: 4rem;
    font-weight: bold;

}
/* Media Queries for Small Screens */
@media (max-width: 768px) {
    .timer {
        flex-direction: column; /* Passe sur plusieurs lignes pour petit écran */
        padding: 10px;
    }
    .time-segment {
        margin: 10px 0;
        text-align: center;
    }
    .separator {
        display: none; /* Cache les séparateurs pour simplifier sur petits écrans */
    }
    .time-value {
        font-size: 2.5rem; /* Ajuste la taille du texte */
    }
    .time-label {
        font-size: 0.8rem;
    }
}
/* Media Queries for Extra Small Screens */
@media (max-width: 480px) {
    .time-value {
        font-size: 2rem;
    }
    .time-label {
        font-size: 0.7rem;
    }
}</style>
        
    

        <!-- Swiper-->
        <section class="section swiper-container swiper-slider-classic responsive-section" data-loop="true" data-autoplay="4859" data-simulate-touch="true" data-direction="vertical" data-nav="false" style="margin-top:10px">
          <div class="swiper-wrapper text-center">
            <div class="swiper-slide" >
              <div class="swiper-slide-caption section-md" style="color:white">
                <div class="container" style="margin-top:-300px;">
                 <h5 data-caption-animate="fadeInLeft" data-caption-delay="0" style="color:white">
                   <!-- Swiper
                  <span id="countdown"></span>-->
                  </h5>
                  
                  <p class="text-width-large" data-caption-animate="fadeInRight" data-caption-delay="100" style="margin-top:50px">
                  Assistez à la cérémonie de remise des prix des lauréats du Gov'athon 2024, catalyseur de la réforme du service public.
                  </p>
                    <!-- Swiper
                  <a class="button button-primary " href="#modalCta" data-toggle="modal" data-caption-animate="fadeInUp" data-caption-delay="200">Réserver</a>-->
                  <br>
                  <h5 style="color:#fff">Places restantes:&nbsp;<?php echo  $placesRestantes ?> </h5>
                  <br>
                  <button type="button" style="background-color:#f1da18"  href="#modalCta" data-toggle="modal" data-caption-animate="fadeInUp" data-caption-delay="200" class="btn btn-success">Réserver ma place</button>
                </div>
              </div>
            </div>
          </div>
          
            
          <div class="swiper-pagination__module">
          
          </div>
          
        </section>
        <h5 style="color:yellow" style="margin-top:800%"> <i class="fa-solid fa-location-dot" style="color:green; font-size:30px"></i> &nbsp;CICAD, route de DIamniadio, Dakar,Sénégal</h5>  

      </div>
      <!-- See all ces
      <section class="section section-sm section-first bg-default text-center" id="services">
        <div class="container">
          <div class="row row-30 justify-content-center">
            <div class="col-md-7 col-lg-5 col-xl-6 text-lg-left wow fadeInUp"><img src="images/index-1-415x592.png" alt="" width="415" height="592"/>
            </div>
            <div class="col-lg-7 col-xl-6">
              <div class="row row-30">
                <div class="col-sm-6 wow fadeInRight">
                  <article class="box-icon-modern box-icon-modern-custom">
                    <div>
                      <h3 class="box-icon-modern-big-title">What We Offer</h3>
                      <div class="box-icon-modern-decor"></div><a class="button button-primary button-ujarak" href="#">View All Services</a>
                    </div>
                  </article>
                </div>
                <div class="col-sm-6 wow fadeInRight" data-wow-delay=".1s">
                  <article class="box-icon-modern box-icon-modern-2">
                    <div class="box-icon-modern-icon linearicons-phone-in-out"></div>
                    <h5 class="box-icon-modern-title"><a href="#">CORPORATE<br>SOLUTIONS</a></h5>
                    <div class="box-icon-modern-decor"></div>
                    <p class="box-icon-modern-text">Need specific software for your company? We are ready to develop it!</p>
                  </article>
                </div>
                <div class="col-sm-6 wow fadeInRight" data-wow-delay=".2s">
                  <article class="box-icon-modern box-icon-modern-2">
                    <div class="box-icon-modern-icon linearicons-headset"></div>
                    <h5 class="box-icon-modern-title"><a href="#">CALL CENTER<br>SOLUTIONS</a></h5>
                    <div class="box-icon-modern-decor"></div>
                    <p class="box-icon-modern-text">Our experts provide custom products of any complexity for call centers.</p>
                  </article>
                </div>
                <div class="col-sm-6 wow fadeInRight" data-wow-delay=".3s">
                  <article class="box-icon-modern box-icon-modern-2">
                    <div class="box-icon-modern-icon linearicons-outbox"></div>
                    <h5 class="box-icon-modern-title"><a href="#">CLOUD<br>DEVELOPMENT</a></h5>
                    <div class="box-icon-modern-decor"></div>
                    <p class="box-icon-modern-text">We can also offer you reliable cloud development solutions.</p>
                  </article>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>-->

      <!-- Cta-->
     
<!-- Years of experience
      <section class="section section-sm bg-default">
        <div class="container">
          <div class="row row-30 row-xl-24 justify-content-center align-items-center align-items-lg-start text-left">
            <div class="col-md-6 col-lg-5 col-xl-4 text-center"><a class="text-img" href="#">
                <div id="particles-js"></div><span class="counter">1ère</span></a></div>
            <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4">
              <div class="text-width-extra-small offset-top-lg-24 wow fadeInUp">
                <h3 class="title-decoration-lines-left">Edition 2024</h3>
                <p class="text-gray-500">Suite à un long processus de préselection et de suivi, voici le gov'athon 2024 en chiffres</p><a class="button button-secondary button-pipaluk" href="https://govathon2024.fpubliquesn.com/" target="_blank">Visitez le site officiel</a>
              </div>
            </div>
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4 wow fadeInRight" data-wow-delay=".1s">
              <div class="row justify-content-center border-2-column offset-top-xl-26">
                <div class="col-9 col-sm-6">
                  <div class="counter-amy">
                    <div class="counter-amy-number"><span class="counter">120</span>
                    </div>
                    <h6 class="counter-amy-title">Projets soummis</h6>
                  </div>
                </div>
                <div class="col-9 col-sm-6">
                  <div class="counter-amy">
                    <div class="counter-amy-number"><span class="counter">18</span>
                    </div>
                    <h6 class="counter-amy-title">Thèmes</h6>
                  </div>
                </div>
                <div class="col-9 col-sm-6">
                  <div class="counter-amy">
                    <div class="counter-amy-number"><span class="counter">6</span>
                    </div>
                    <h6 class="counter-amy-title">Secteurs</h6>
                  </div>
                </div>
                <div class="col-9 col-sm-6">
                  <div class="counter-amy">
                    <div class="counter-amy-number"><span class="counter">12</span>
                    </div>
                    <h6 class="counter-amy-title">Projets finalistes</h6>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </section>-->

      <h1 style="color: #fff;"></h1>
      <!-- What people Say
          <section class="section section-sm section-bottom-70 section-fluid bg-default">
            <div class="container-fluid">
              <h2>What people Say</h2>
              <div class="row row-50 row-sm">
                <div class="col-md-6 col-xl-4 wow fadeInRight">
              
                  <article class="quote-modern quote-modern-custom">
                    <div class="unit unit-spacing-md align-items-center">
                      <div class="unit-left"><a class="quote-modern-figure" href="#"><img class="img-circles" src="images/user-11-75x75.jpg" alt="" width="75" height="75"/></a></div>
                      <div class="unit-body">
                        <h4 class="quote-modern-cite"><a href="#">Catherine Williams</a></h4>
                        <p class="quote-modern-status">Regular client</p>
                      </div>
                    </div>
                    <div class="quote-modern-text">
                      <p class="q">RatherApp offers a high caliber of resources skilled in Microsoft Azure .NET, mobile and Quality Assurance. They became our true business partners over the past three years.</p>
                    </div>
                  </article>
                </div>
                <div class="col-md-6 col-xl-4 wow fadeInRight" data-wow-delay=".1s">
                
                  <article class="quote-modern quote-modern-custom">
                    <div class="unit unit-spacing-md align-items-center">
                      <div class="unit-left"><a class="quote-modern-figure" href="#"><img class="img-circles" src="images/user-12-75x75.jpg" alt="" width="75" height="75"/></a></div>
                      <div class="unit-body">
                        <h4 class="quote-modern-cite"><a href="#">Rupert Wood</a></h4>
                        <p class="quote-modern-status">Regular client</p>
                      </div>
                    </div>
                    <div class="quote-modern-text">
                      <p class="q">RatherApp powered us with a competent team to develop products for banking services. The team has been delivering results within budget and time, which is amazing.</p>
                    </div>
                  </article>
                </div>
                <div class="col-md-6 col-xl-4 wow fadeInRight" data-wow-delay=".2s">
                
                  <article class="quote-modern quote-modern-custom">
                    <div class="unit unit-spacing-md align-items-center">
                      <div class="unit-left"><a class="quote-modern-figure" href="#"><img class="img-circles" src="images/user-20-75x75.jpg" alt="" width="75" height="75"/></a></div>
                      <div class="unit-body">
                        <h4 class="quote-modern-cite"><a href="#">Samantha Brown</a></h4>
                        <p class="quote-modern-status">Regular client</p>
                      </div>
                    </div>
                    <div class="quote-modern-text">
                      <p class="q">RatherApp is a highly skilled and uniquely capable firm with multitudes of talent on-board. We have collaborated on a number of diverse projects that have been a great success.</p>
                    </div>
                  </article>
                </div>
              </div>
            </div>
          </section>-->
      <!-- Contact information
          <section class="section section-sm bg-default">
            <div class="container">
              <div class="row row-30 justify-content-center">
                <div class="col-sm-8 col-md-6 col-lg-4">
                  <article class="box-contacts">
                    <div class="box-contacts-body">
                      <div class="box-contacts-icon fl-bigmug-line-cellphone55"></div>
                      <div class="box-contacts-decor"></div>
                      <p class="box-contacts-link"><a href="tel:#">+1 323-913-4688</a></p>
                      <p class="box-contacts-link"><a href="tel:#">+1 323-888-4554</a></p>
                    </div>
                  </article>
                </div>
                <div class="col-sm-8 col-md-6 col-lg-4">
                  <article class="box-contacts">
                    <div class="box-contacts-body">
                      <div class="box-contacts-icon fl-bigmug-line-up104"></div>
                      <div class="box-contacts-decor"></div>
                      <p class="box-contacts-link"><a href="#">4730 Crystal Springs Dr, Los Angeles, CA 90027</a></p>
                    </div>
                  </article>
                </div>
                <div class="col-sm-8 col-md-6 col-lg-4">
                  <article class="box-contacts">
                    <div class="box-contacts-body">
                      <div class="box-contacts-icon fl-bigmug-line-chat55"></div>
                      <div class="box-contacts-decor"></div>
                      <p class="box-contacts-link"><a href="mailto:#">mail@demolink.org</a></p>
                      <p class="box-contacts-link"><a href="mailto:#">info@demolink.org</a></p>
                    </div>
                  </article>
                </div>
              </div>
            </div>
          </section>-->

          <!-- Contact Form
          <section class="section section-sm section-last bg-default text-left" id="contacts">
            <div class="container">
              <article class="title-classic">
                <div class="title-classic-title">
                  <h3>Get in touch</h3>
                </div>
                <div class="title-classic-text">
                  <p>If you have any questions, just fill in the contact form, and we will answer you shortly.</p>
                </div>
              </article>
              <form class="rd-form rd-form-variant-2 rd-mailform" data-form-output="form-output-global" data-form-type="contact" method="post" action="bat/rd-mailform.php">
                <div class="row row-14 gutters-14">
                  <div class="col-md-4">
                    <div class="form-wrap">
                      <input class="form-input" id="contact-your-name-2" type="text" name="name" data-constraints="@Required">
                      <label class="form-label" for="contact-your-name-2">Your Name</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-wrap">
                      <input class="form-input" id="contact-email-2" type="email" name="email" data-constraints="@Email @Required">
                      <label class="form-label" for="contact-email-2">E-mail</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-wrap">
                      <input class="form-input" id="contact-phone-2" type="text" name="phone" data-constraints="@Numeric">
                      <label class="form-label" for="contact-phone-2">Phone</label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-wrap">
                      <label class="form-label" for="contact-message-2">Message</label>
                      <textarea class="form-input textarea-lg" id="contact-message-2" name="message" data-constraints="@Required"></textarea>
                    </div>
                  </div>
                </div>
                <button class="button button-primary button-pipaluk" type="submit">Send Message</button>
              </form>
            </div>
          </section>-->
 
      <!-- Page Footer-->
      <footer class="section section-fluid footer-minimal context-dark">
    
</footer>
<!-- Modal pour réserver la place -->
<div class="modal fade" id="modalCta" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Réserver votre place</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="rd-form rd-form-variant-2 rd-mailform" method="post" id="formRegister">
                    <div class="row row-14 gutters-14">
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-firstname" type="text" name="firstname" required>
                                <label class="form-label" for="contact-modal-firstname">Prénom</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-lastname" type="text" name="lastname" required>
                                <label class="form-label" for="contact-modal-lastname">Nom</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-phone" type="tel" name="phone" required>
                                <label class="form-label" for="contact-modal-phone">Téléphone</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-email" type="email" name="email" required>
                                <label class="form-label" for="contact-modal-email">Email</label>
                            </div>
                        </div>   
                        <div class="col-12">
                            <div class="form-wrap">
                                <input class="form-input" id="contact-modal-role" type="text" name="role" required>
                                <label class="form-label" for="contact-modal-role">Fonction</label>
                            </div>
                        </div>
                    </div>
                    <br>
                   <button class="buttonadd" type="submit" id="generateBadgeButton">Réserver </button>
                </form>
                <div id="successMessage" style="display:none;">
                    <p>Vos données ont été soumises avec succès!</p>
                    <button id="viewBadgeButton" class="btn btn-primary" style="display:none;">Voir votre badge</button>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" 
     id="modalQrCodePreview" 
     tabindex="-1" 
     role="dialog" 
     aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content modal-background">
            <!-- Zone principale du badge -->
            <div class="modal-body modal-content-body">
                <!-- Conteneur principal -->
                <div class="badge-container">
                    <!-- Conteneur des informations de l'invité -->
                    <div class="badge-info">
                        <div class="badge-details">
                            <div><strong>Prénom :</strong> <span id="badgeFirstName">Jean</span></div>
                            <div><strong>Nom :</strong> <span id="badgeLastName">Dupont</span></div>
                            <div><strong>Téléphone :</strong> <span id="badgePhone">+123456789</span></div>
                            <div><strong>Fonction :</strong> <span id="badgeRole">Invité</span></div>
                        </div>
                    </div>

                    <!-- Conteneur séparé pour le QR Code -->
                    <div class="qr-code-section">
                        <img id="qrCodeImage" src="images/qr_code.png" alt="QR Code">
                    </div>
                </div>

                <!-- Bouton de téléchargement -->
                <button id="openDownloadModal" class="btn-download">Télécharger le Badge</button>
            </div>
        </div>
    </div>
</div>
<!-- dom-to-image -->
<script src="https://cdn.jsdelivr.net/npm/dom-to-image-more@2.9.4/dist/dom-to-image-more.min.js"></script>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>


<!-- Styles CSS -->
<style>
    /* Modal content */
    .modal-content {
        min-height: 500px;
        padding: 0;
        border-radius: 30px;
        position: relative;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }

    /* Corps principal du modal */
    .modal-content-body {
        background-image: url('images/Govathon2024.png'); /* Chemin de votre image */
        background-size: contain; /* Adapte l'image pour qu'elle soit entièrement visible */
        background-repeat: no-repeat; /* Évite la répétition de l'image */
        background-position: center; /* Centre l'image dans le conteneur */
        border-radius: 30px;
        padding: 20px;
        display: flex;
        flex-direction: column; /* Organisation verticale */
        justify-content: space-between; /* Espace entre les sections */
        align-items: center; /* Centrer horizontalement */
        height: 100%;
    }

    .modal-content-body {
    background-image: url('images/Govathon2024.png'); /* Chemin de votre image */
    background-size: contain; /* Adapte l'image pour qu'elle soit entièrement visible */
    background-repeat: no-repeat; /* Évite la répétition de l'image */
    background-position: center; /* Centre l'image dans le conteneur */
    border-radius: 30px;
    padding: 120px;
    text-align: center;
    color: #2c3e50;
}


    /* Conteneur principal */
    .badge-container {
        display: flex;
        flex-direction: column; /* Organisation verticale */
        justify-content: space-between; /* Espace entre informations et QR Code */
        align-items: center; /* Centrage horizontal */
        width: 100%;
        height: 100%;
    }

    /* Badge info */
    .badge-info {
        text-align: left;
        width: 100%;
    }

    .badge-details {
        font-size: 16px;
       
        color: black;
        font-weight: bold;
        padding: 10px;
        margin-top:300px;
        font-weight:bold;
        margin-left:-110px;

    }

    /* QR Code */
    .qr-code-section img {
        width: 180px;
        height: 180px;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
     
    }

    /* Bouton de téléchargement */
    .btn-download {
        background-color: #e67e22;
        border: none;
        color: #fff;
        font-weight: bold;
        padding: 10px 30px;
        border-radius: 30px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        margin-top:5px;
    }

    .btn-download:hover {
        background-color: #d35400;
    }
</style>


<!-- Modal de sélection du format -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
  // Afficher le modal pour choisir le format
  document.getElementById('openDownloadModal').addEventListener('click', function() {
    $('#downloadFormatModal').modal('show');
  });

  // Télécharger en PNG
  document.getElementById('downloadAsPng').addEventListener('click', function() {
    var badgeContent = document.getElementById('badgeContent');

    // Utiliser dom-to-image pour capturer le badge en PNG
    domtoimage.toPng(badgeContent, {
        filter: function(node) {
            // Exclure les boutons de téléchargement et de fermeture de l'image
            return !(node.classList && (node.classList.contains('btn-primary') || node.classList.contains('close')));
        },
        width: badgeContent.offsetWidth * 2, // Améliorer la résolution
        height: badgeContent.offsetHeight * 2, // Améliorer la résolution
        style: {
            transform: "scale(2)", // Appliquer un zoom pour une meilleure qualité
            transformOrigin: "top left"
        }
    })
    .then(function(dataUrl) {
        var link = document.createElement('a');
        link.href = dataUrl;
        link.download = 'badge.png'; // Nom du fichier PNG téléchargé
        link.click();
        $('#downloadFormatModal').modal('hide'); // Fermer le modal après téléchargement
    })
    .catch(function(error) {
        console.error('Erreur lors de la capture du badge:', error);
    });
  });

  // Télécharger en PDF
  document.getElementById('downloadAsPdf').addEventListener('click', function() {
    var badgeContent = document.getElementById('badgeContent');

    // Utiliser dom-to-image pour capturer le badge en PNG (nécessaire pour créer le PDF)
    domtoimage.toPng(badgeContent, {
        filter: function(node) {
            return !(node.classList && (node.classList.contains('btn-primary') || node.classList.contains('close')));
        },
        width: badgeContent.offsetWidth * 2,
        height: badgeContent.offsetHeight * 2,
        style: {
            transform: "scale(2)", // Appliquer un zoom pour une meilleure qualité
            transformOrigin: "top left"
        }
    })
    .then(function(dataUrl) {
        const { jsPDF } = window.jspdf;
        
        // Calculer la taille du PDF en fonction des dimensions du badge
        var pdfWidth = badgeContent.offsetWidth;
        var pdfHeight = badgeContent.offsetHeight;

        var pdf = new jsPDF('p', 'mm', [pdfWidth, pdfHeight]);
        // Ajouter l'image du badge au PDF, ajuster la taille de l'image pour qu'elle remplisse bien le PDF sans espace blanc
        pdf.addImage(dataUrl, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save('badge.pdf'); // Sauvegarder le PDF sous le nom "badge.pdf"
        
        // Recharger la page après téléchargement
        location.reload();
        
        $('#downloadFormatModal').modal('hide'); // Fermer le modal après téléchargement
    })
    .catch(function(error) {
        console.error('Erreur lors de la capture du badge:', error);
    });
  });
</script>


<script>
    document.getElementById('formRegister').addEventListener('submit', function(event) {
        event.preventDefault(); // Empêche l'envoi du formulaire

        var form = this;
        var formData = new FormData(form);

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fermer la modal du formulaire immédiatement
                $('#modalCta').modal('hide'); // Ferme la modal du formulaire

                // Sauvegarder les données du badge dans le DOM pour la prévisualisation
                document.getElementById('badgeFirstName').textContent = data.firstname;
                document.getElementById('badgeLastName').textContent = data.lastname;
                document.getElementById('badgePhone').textContent = data.phone;
                document.getElementById('badgeRole').textContent = data.role;
                
                // Mettre à jour l'image du QR Code dans la modal
                document.getElementById('qrCodeImage').src = data.qrCodePath;

                // Afficher la modal de prévisualisation du QR Code après un délai de 1 seconde
                setTimeout(() => {
                    $('#modalQrCodePreview').modal('show');
                }, 1000); // Affichage immédiat après fermeture du formulaire
            } else {
                alert('Erreur lors de la génération du badge');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur s\'est produite.');
        });
    });

    // Gérer le téléchargement de la carte complète du badge   
    document.getElementById("downloadLink").addEventListener("click", function(event) {
        event.preventDefault(); // Empêche l'action par défaut du lien

        // Cibler l'élément de la modal que vous souhaitez capturer
        var modalContent = document.querySelector("#modalQrCodePreview .modal-content"); // Cibler l'intégralité de la modal
        
        // Utiliser html2canvas pour capturer l'élément
        html2canvas(modalContent).then(function(canvas) {
            // Créer un lien de téléchargement à partir de l'image capturée
            var link = document.createElement('a');
            link.href = canvas.toDataURL("image/png"); // Convertir l'image capturée en format PNG
            link.download = "badge_complet.png"; // Nom du fichier à télécharger
            link.click(); // Déclencher le téléchargement
        });
    });

    // Fermer la modal et recharger la page après un léger délai
    $('#modalQrCodePreview').on('hidden.bs.modal', function () {
        // Laisser le temps à la modal de se fermer avant de recharger la page
        setTimeout(function() {
            location.reload(); // Recharger la page après un court délai
        }, 300); // Délai de 300ms pour s'assurer que la modal est complètement fermée
    });
</script>

    <script>
      // Fonction pour initialiser le compte à rebours
function countdown() {
  const targetDate = new Date("2024-12-23T00:00:00").getTime();
  const countdownElement = document.getElementById("countdown");

  setInterval(() => {
    const now = new Date().getTime();
    const distance = targetDate - now;

    // Calcul des jours, heures, minutes et secondes
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Affichage du compte à rebours
    countdownElement.innerHTML = `${days}j ${hours}h ${minutes}m ${seconds}s`;

    // Si la date cible est atteinte
    if (distance < 0) {
      countdownElement.innerHTML = "Cérémonie en cours";
      clearInterval();
    }
  }, 1000);
}

    window.onload = countdown;

    </script>
    <!-- Global Mailform Output-->
    <div class="snackbars" id="form-output-global"></div>
    <!-- Javascript-->
  <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script src="js/core.min.js"></script>
    <script src="js/script.js"></script>
    <!-- coded by Himic-->
  </body>
</html>



 <!-- coded by Himic
ac6b0a32a15d86d1c3b6e8db0157ac8f-43269c9d-bdce-470c-ba7b-5d11ba275a37
-->