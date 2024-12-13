
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
<?php 

$codes_telephoniques = array(
  "Etats Unis d'Amérique" => 1,
  "Canada" => 1,
  "Fédération russe" => 7,
  "Kazakhstan" => 7,
  "Ouzbekistan" => 7,
  "Egypte" => 20,
  "Afrique du Sud" => 27,
  "Grèce" => 30,
  "Pays-Bas" => 31,
  "Belgique" => 32,
  "France" => 33,
  "Espagne" => 34,
  "Hongrie" => 36,
  "Italie" => 39,
  "Vatican" => 39,
  "Roumanie" => 40,
  "Liechtenstein" => 41,
  "Suisse" => 41,
  "Autriche" => 43,
  "Royaume-Uni" => 44,
  "Danemark" => 45,
  "Suède" => 46,
  "Norvège" => 47,
  "Pologne" => 48,
  "Allemagne" => 49,
  "Pérou" => 51,
  "Mexique Centre" => 52,
  "Cuba" => 53,
  "Argentine" => 54,
  "Brésil" => 55,
  "Chili" => 56,
  "Colombie" => 57,
  "Vénézuela" => 58,
  "Malaisie" => 60,
  "Australie" => 61,
  "Ile Christmas" => 61,
  "Indonésie" => 62,
  "Philippines" => 63,
  "Nouvelle-Zélande" => 64,
  "Singapour" => 65,
  "Thaà¯lande" => 66,
  "Japon" => 81,
  "Corée du Sud" => 82,
  "Viêt-Nam" => 84,
  "Chine" => 86,
  "Turquie" => 90,
  "Inde" => 91,
  "Pakistan" => 92,
  "Afghanistan" => 93,
  "Sri Lanka" => 94,
  "Union Birmane" => 95,
  "Iran" => 98,
  "Maroc" => 212,
  "Algérie" => 213,
  "Tunisie" => 216,
  "Libye" => 218,
  "Gambie" => 220,
  "Sénégal" => 221,
  "Mauritanie" => 222,
  "Mali" => 223,
  "Guinée" => 224,
  "Cà´te d'Ivoire" => 225,
  "Burkina Faso" => 226,
  "Niger" => 227,
  "Togo" => 228,
  "Bénin" => 229,
  "Maurice" => 230,
  "Libéria" => 231,
  "Sierra Leone" => 232,
  "Ghana" => 233,
  "Nigeria" => 234,
  "République du Tchad" => 235,
  "République Centrafricaine" => 236,
  "Cameroun" => 237,
  "Cap-Vert" => 238,
  "Sao Tomé-et-Principe" => 239,
  "Guinée équatoriale" => 240,
  "Gabon" => 241,
  "Bahamas" => 242,
  "Congo" => 242,
  "Congo Zaà¯re (Rep. Dem.)" => 243,
  "Angola" => 244,
  "Guinée-Bissao" => 245,
  "Barbade" => 246,
  "Ascension" => 247,
  "Seychelles" => 248,
  "Soudan" => 249,
  "Rwanda" => 250,
  "Ethiopie" => 251,
  "Somalie" => 252,
  "Djibouti" => 253,
  "Kenya" => 254,
  "Tanzanie" => 255,
  "Ouganda" => 256,
  "Burundi" => 257,
  "Mozambique" => 258,
  "Zambie" => 260,
  "Madagascar" => 261,
  "Réunion" => 262,
  "Zimbabwe" => 263,
  "Namibie" => 264,
  "Malawi" => 265,
  "Lesotho" => 266,
  "Botswana" => 267,
  "Antigua-et-Barbuda" => 268,
  "Swaziland" => 268,
  "Mayotte" => 269,
  "République comorienne" => 269,
  "Saint Hélène" => 290,
  "Erythrée" => 291,
  "Aruba" => 297,
  "Ile Feroe" => 298,
  "Groà Â«nland" => 299,
  "Iles vierges américaines" => 340,
  "Iles Caà¯mans" => 345,
  "Espagne" => 349,
  "Gibraltar" => 350,
  "Portugal" => 351,
  "Luxembourg" => 352,
  "Irlande" => 353,
  "Islande" => 354,
  "Albanie" => 355,
  "Malte" => 356,
  "Chypre" => 357,
  "Finlande" => 358,
  "Bulgarie" => 359,
  "Lituanie" => 370,
  "Lettonie" => 371,
  "Estonie" => 372,
  "Moldavie" => 373,
  "Arménie" => 374,
  "Biélorussie" => 375,
  "Andorre" => 376,
  "Monaco" => 377,
  "Saint-Marin" => 378,
  "Ukraine" => 380,
  "Yougoslavie" => 381,
  "Croatie" => 385,
  "Slovénie" => 386,
  "Bosnie-Herzégovine" => 387,
  "Macédoine" => 389,
  "Italie" => 390,
  "République Tchèque" => 420,
  "Slovaquie" => 421,
  "Liechtenstein" => 423,
  "Bermudes" => 441,
  "Grenade" => 473,
  "Iles Falklands" => 500,
  "Belize" => 501,
  "Guatemala" => 502,
  "Salvador" => 503,
  "Honduras" => 504,
  "Nicaragua" => 505,
  "Costa Rica" => 506,
  "Panama" => 507,
  "Haà¯ti" => 509,
  "Guadeloupe" => 590,
  "Bolivie" => 591,
  "Guyane" => 592,
  "Equateur" => 593,
  "Guinée Franà§aise" => 594,
  "Paraguay" => 595,
  "Antilles Franà§aises" => 596,
  "Suriname" => 597,
  "Uruguay" => 598,
  "Antilles hollandaise" => 599,
  "Saint Eustache" => 599,
  "Saint Martin" => 599,
  "Turks et caicos" => 649,
  "Monteserrat" => 664,
  "Saipan" => 670,
  "Guam" => 671,
  "Antarctique-Casey" => 672,
  "Antarctique-Scott" => 672,
  "Ile de Norfolk" => 672,
  "Brunei Darussalam" => 673,
  "Nauru" => 674,
  "Papouasie - Nouvelle Guinée" => 675,
  "Tonga" => 676,
  "Iles Salomon" => 677,
  "Vanuatu" => 678,
  "Fidji" => 679,
  "Palau" => 680,
  "Wallis et Futuna" => 681,
  "Iles Cook" => 682,
  "Niue" => 683,
  "Samoa Américaines" => 684,
  "Samoa occidentales" => 685,
  "Kiribati" => 686,
  "Nouvelle-Calédonie" => 687,
  "Tuvalu" => 688,
  "Polynésie Franà§aise" => 689,
  "Tokelau" => 690,
  "Micronésie" => 691,
  "Marshall" => 692,
  "Sainte-Lucie" => 758,
  "Dominique" => 767,
  "Porto Rico" => 787,
  "République Dominicaine" => 809,
  "Saint-Vincent-et-les Grenadines" => 809,
  "Corée du Nord" => 850,
  "Hong Kong" => 852,
  "Macao" => 853,
  "Cambodge" => 855,
  "Laos" => 856,
  "Trinité-et-Tobago" => 868,
  "Saint-Christophe-et-Niévès" => 869,
  "Atlantique Est" => 871,
  "Marisat (Atlantique Est)" => 872,
  "Marisat (Atlantique Ouest)" => 873,
  "Atlantique Ouest" => 874,
  "Jamaà¯que" => 876,
  "Bangladesh" => 880,
  "Taiwan" => 886,
  "Maldives" => 960,
  "Liban" => 961,
  "Jordanie" => 962,
  "Syrie" => 963,
  "Iraq" => 964,
  "Koweà¯t" => 965,
  "Arabie saoudite" => 966,
  "Yémen" => 967,
  "Oman" => 968,
  "Palestine" => 970,
  "Emirats arabes unis" => 971,
  "Israà Â«l" => 972,
  "Bahreà¯n" => 973,
  "Qatar" => 974,
  "Bhoutan" => 975,
  "Mongolie" => 976,
  "Népal" => 977,
  "Tadjikistan (Rep. du)" => 992,
  "Turkménistan" => 993,
  "Azerbaà¯djan" => 994,
  "Géorgie" => 995,
  "Kirghizistan" => 996,
  "Bahamas" => 1242,
  "Barbade" => 1246,
  "Anguilla" => 1264,
  "Antigua et Barbuda " => 1268,
  "Vierges Britanniques (Iles)" => 1284,
  "Vierges Américaines (Iles)" => 1340,
  "Cayman (Iles)" => 1345,
  "Bermudes" => 1441,
  "Grenade" => 1473,
  "Turks et Caà¯cos (Iles)" => 1649,
  "Montserrat" => 1664,
  "Sainte-Lucie" => 1758,
  "Dominique" => 1767,
  "Saint-Vincent-et-Grenadines" => 1784,
  "Porto Rico" => 1787,
  "Hawaà¯" => 1808,
  "Dominicaine (Rep.)" => 1809,
  "Saint-Vincent-et-Grenadines" => 1809,
  "Trinité-et-Tobago" => 1868,
  "Saint-Kitts-et-Nevis" => 1869,
  "Jamaà¯que" => 1876,
  "Norfolk (Ile)" => 6723
  );
?>


<!DOCTYPE html>
<html class="wide wow-animation" lang="en">
  <head>
    <title>Home</title>
    <meta name="format-detection" content="telephone=no">
    <!-- Include SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- Include SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <link rel="icon" href="images/logo.jpeg" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dom-to-image-more@2.9.0/dist/dom-to-image-more.min.js"></script>
       
    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    




  
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


.titre {
    color: #FFF ;
    text-align: center;
    font-size: 50px;
    font-weight: bold;

   }

/* Réduire la taille sur les écrans plus petits */
@media (max-width: 768px) {
    .titre {
        font-size: 18px; /* Taille adaptée pour les tablettes */
    }
}

@media (max-width: 480px) {
    .titre {
        font-size: 18px; /* Taille adaptée pour les smartphones */
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

.formindicatif{
  border : none;
  background-color : #efefef;

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
  /* Classe personnalisée pour la navbar */
.custom-navbar {
    background-color: #000000 !important; /* Noir */
}

/* S'assurer que la couleur de fond reste noire sur tous les écrans */
@media (max-width: 767px) {
    .custom-navbar {
        background-color: #000000 !important; /* Noir */
    }
}

@media (min-width: 768px) and (max-width: 991px) {
    .custom-navbar {
        background-color: #000000 !important; /* Noir */
    }
}

@media (min-width: 992px) and (max-width: 1199px) {
    .custom-navbar {
        background-color: #000000 !important; /* Noir */
    }
}

@media (min-width: 1200px) {
    .custom-navbar {
        background-color: #000000 !important; /* Noir */
    }
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
          <div class="rd-navbar-wrap" style="background-color:black">
    <nav class="rd-navbar rd-navbar-classic custom-navbar" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed" data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static" data-lg-device-layout="rd-navbar-fixed" data-xl-layout="rd-navbar-static" data-xl-device-layout="rd-navbar-static" data-xxl-layout="rd-navbar-static" data-xxl-device-layout="rd-navbar-static" data-lg-stick-up-offset="46px" data-xl-stick-up-offset="46px" data-xxl-stick-up-offset="46px" data-lg-stick-up="true" data-xl-stick-up="true" data-xxl-stick-up="true">
        <div class="rd-navbar-main-outer">
            <div class="rd-navbar-main">
                <!-- RD Navbar Panel-->
                <div class="rd-navbar-panel">
                    <!-- RD Navbar Toggle-->
                  
                    <!-- RD Navbar Brand-->
                    <div class="rd-navbar-brand">
                        <a class="brand" href="index.html">
                            <img src="images/10.png" alt="" width="223" height="50"/>
                        </a>
                    </div>
                </div>
                <div class="rd-navbar-main-element">
                    <div class="rd-navbar-nav-wrap">
                        <!-- RD Navbar Share (Commenté) -->
                        <!--<div class="rd-navbar-share fl-bigmug-line-share27" data-rd-navbar-toggle=".rd-navbar-share-list">
                            <ul class="list-inline rd-navbar-share-list">
                            </ul>
                        </div>-->
                        <ul class="rd-navbar-nav">
                            <!-- Stylesheets (Commenté)
                            <li class="rd-nav-item active"><a class="rd-nav-link" href="#home">Home</a></li>
                            -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>


        </header>
        </br>
        <h3 class="titre" style="" > Finale et cérémonie de remise de lauréats</h3>
    
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
        function startCountdown(targetDate) {
            const daysElement = document.getElementById('days');
            const hoursElement = document.getElementById('hours');
            const minutesElement = document.getElementById('minutes');
            const secondsElement = document.getElementById('seconds');

            function updateTimer() {
                const now = new Date();
                const timeDifference = targetDate - now;

                if (timeDifference <= 0) {
                    clearInterval(timerInterval);
                    daysElement.textContent = '00';
                    hoursElement.textContent = '00';
                    minutesElement.textContent = '00';
                    secondsElement.textContent = '00';
                    return;
                }

                const days = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeDifference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

                daysElement.textContent = String(days).padStart(2, '0');
                hoursElement.textContent = String(hours).padStart(2, '0');
                minutesElement.textContent = String(minutes).padStart(2, '0');
                secondsElement.textContent = String(seconds).padStart(2, '0');
            }

            const timerInterval = setInterval(updateTimer, 1000);
            updateTimer(); // Appel initial pour éviter d'attendre 1 seconde
        }

        // Date cible : 23 décembre 2024 à minuit
        const targetDate = new Date('2024-12-23T00:00:00');
        startCountdown(targetDate);
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
    .form-control{
      backgound-color : red; !important
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
                
                  </p>
                    <!-- Swiper
                  <a class="button button-primary " href="#modalCta" data-toggle="modal" data-caption-animate="fadeInUp" data-caption-delay="200">Réserver</a>-->
                  <br>
                  <h5 style="color:#fff;margin-top :120px;font-size:25px">Places restantes:&nbsp;<?php echo  $placesRestantes ?> </h5>
                  <br>
                  <button type="button" style="background-color:#f1da18"  href="#modalCta" data-toggle="modal" data-caption-animate="fadeInUp" data-caption-delay="200" class="btn btn-success">Je participe à l'événement</button>
                  <h1>&nbsp;</h1>
                  <h5 style="color:yellow" style="margin-top:100px"> <i class="fa-solid fa-location-dot" style="color:green; font-size:30px"></i> &nbsp;CICAD, route de DIamniadio, Dakar,Sénégal</h5>  
                </div>
              </div>
            </div>
            
          </div>
         
         
          <div class="swiper-pagination__module">
         
          </div>
          
        </section>
        
      </div>
     

      <footer class="section section-fluid footer-minimal context-dark">
    
</footer>
<?php 
// Trier le tableau des codes téléphoniques par ordre alphabétique des noms de pays
ksort($codes_telephoniques, SORT_STRING | SORT_FLAG_CASE);
?>
<!-- Modal pour réserver la place -->

<div class="modal fade" id="modalCta" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Invitation Gov'athon 2024</h4>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegister" method="POST" action="process_registration.php">
                        <div class="row row-14 gutters-14">
                            <!-- Prénom -->
                            <div class="col-12 ">
                                <div class="form-wrap">
                                    <input class="form-input" id="contact-modal-firstname" type="text" name="firstname" required placeholder=" ">
                                    <label class="form-label" for="contact-modal-firstname">Prénom</label>
                                </div>
                            </div>
                            <!-- Nom -->
                            <div class="col-12 ">
                                <div class="form-wrap">
                                    <input class="form-input" id="contact-modal-lastname" type="text" name="lastname" required placeholder=" ">
                                    <label class="form-label" for="contact-modal-lastname">Nom</label>
                                </div>
                            </div>

                            <!-- Téléphone -->
                            <div class="col-12">
                                <div class="form-wrap">
                                    <label class="form-label" for="contact-modal-phone">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Téléphone</label>
                                    <div class="input-group">
                                                    <select class="formindicatif" id="country-code" name="country_code" required>
                                    <option value="" disabled>Indicatif</option>
                                    <?php
                                    foreach ($codes_telephoniques as $country => $code) {
                                        $country_escaped = htmlspecialchars($country, ENT_QUOTES, 'UTF-8');
                                        $code_escaped = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
                                        
                                        $selected = ($country === "Sénégal") ? ' selected' : '';
                                        
                                        echo "<option value=\"+$code_escaped\"$selected>+$code_escaped &mdash; </option>\n";
                                    }
                                    ?>
                                
                               </select>

                                        <!-- Champ de Numéro de Téléphone -->
                                        <input class="form-input" id="contact-modal-phone" type="tel" name="phone" placeholder="" required>
                                    </div>
                                </div>
                            </div>










                            <!-- Email -->
                            <div class="col-12">
                                <div class="form-wrap">
                                    <input class="form-input" id="contact-modal-email" type="email" name="email" required placeholder=" ">
                                    <label class="form-label" for="contact-modal-email">Email</label>
                                </div>
                            </div>   

                            <!-- Fonction   etudiant particulier finaliste-->
                            <div class="col-12">
                                <div class="form-wrap">
                                  
                                  <select class="form-input" id="contact-modal-fonction" type="fonction" name="role" required placeholder=" ">
                                  <option value="Null">-- Merci de choisir votre fonction --</option>
                                    <option value="Etudiant" name="Etudiant">Etudiant</option>
                                    <option value="Particulier" name="Particulier">Particulier</option>
                                    <option value="Finalistes" name="Finalistes" >Finalistes</option>
                                    <option value="Autres" name="Autres">Autres</option>
                                  </select>
                                  </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-primary w-100" type="submit" id="generateBadgeButton">Valider</button>
                    </form>
                    <div id="successMessage" class="mt-3 text-center" style="display:none;">
                        <p class="text-success">Vos données ont été soumises avec succès!</p>
                        <button id="viewBadgeButton" class="btn btn-success">Voir votre badge</button>
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
            <div class="modal-body modal-content-body" style="position: relative;">
                <!-- Ajout de l'image en fond -->
                <img src="images/Govathon2024.png" alt="Background"
                     style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:contain; z-index:-1;">

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
                    
                </div>

                <!-- Bouton de téléchargement -->
                <button id="downloadBadgeButton" class="btn-download">Télécharger</button>
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
    padding: 33%;/* problématique  */
    text-align: center;
    color: #2c3e50;
    height:885px;
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
    margin-top:230px;
    font-weight:bold;
    margin-left:-160px;
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
    margin-top:15px;
}
/* Styles pour le modal OTP */
#modalOtp .modal-content {
    border-radius: 30px;
    padding: 20px;
}

#modalOtp .form-group {
    margin-bottom: 20px;
}

#modalOtp .btn-primary {
    width: 100%;
}

</style>

<style>
    /* Media Queries pour écrans moyens */
    @media (max-width: 768px) {
        .modal-content {
            border-radius: 20px;
            margin: 10px;
        }

        .modal-content-body {
            height: auto; /* Permet à la hauteur de s'adapter */
            padding: 10px; /* Réduit le padding pour éviter les débordements */
            background-size: contain; /* Maintient l'image bien proportionnée */
            background-position: top center; /* Positionne l'image correctement */
        }

        .badge-container {
            justify-content: center; /* Aligne le contenu */
            align-items: center;
            width: 150%;
            height: 600px;
        }

        .badge-info {
            text-align: center; /* Centre le texte */
            margin: 10px 0;
            margin-top:40%;
           text-align:left 3px;
        }

        .badge-details {
            font-size: 14px; /* Taille adaptée */
            margin: 5px 0; /* Ajuste les espacements */
            line-height: 1.4; /* Améliore la lisibilité */
        }

        .qr-code-section img {
            width: 130px;
            height: 130px; /* Ajuste la taille du QR Code */
            margin: 15px 0; /* Ajoute un espacement adéquat */
        }

        .btn-download {
            font-size: 13px;
            padding: 10px 20px; /* Taille adaptée du bouton */
            margin-top: 500px;
            
        }
    }

    /* Media Queries pour écrans très petits */
    @media (max-width: 480px) {
        .modal-content-body {
            padding: 10px;
            background-size: cover;
        }

        .badge-details {
            font-size: 12px; /* Texte plus petit pour petits écrans */
            margin: 5px 0;
            line-height: 1.3; /* Ajustement du line-height */
        }

        .qr-code-section img {
            width: 110px;
            height: 110px; /* Réduit encore la taille du QR Code */
        }

        .btn-download {
            font-size: 12px;
            padding: 8px 15px; /* Taille réduite du bouton */
         
            margin-top: 10px;
        }
    }
</style>




<!-- Modal de sélection du format -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<!-- Modal pour choisir le format de téléchargement -->
<!-- Bouton pour déclencher le téléchargement -->


<!-- Modal pour choisir le format de téléchargement -->
<div class="modal fade" id="downloadFormatModal" tabindex="-1" role="dialog" aria-labelledby="downloadFormatModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="downloadFormatModalLabel">Choisir le format de téléchargement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button id="downloadAsPng" class="btn btn-primary btn-block">Télécharger en Image (PNG)</button>
                <button id="downloadAsPdf" class="btn btn-secondary btn-block">Télécharger en PDF</button>
            </div>
        </div>
    </div>
</div>
<!-- dom-to-image -->

<script src="https://cdn.jsdelivr.net/npm/dom-to-image-more@2.9.4/dist/dom-to-image-more.min.js"></script>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<!-- html2canvas -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>


<!-- Modal de saisie de l'OTP avec affichage de l'email -->
<div class="modal fade" id="modalOtp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Vérification OTP</h4>
                <button class="close" type="button" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Un code de vérification a été envoyé à <strong id="userEmailDisplay"></strong>.</p>
                <form id="formOtp">
                    <div class="form-group">
                        <label for="otp">Entrez le code OTP :</label>
                        <input type="text" class="form-control" id="otp" name="otp" required pattern="\d{6}" maxlength="6" placeholder="123456">
                    </div>
                    <button type="submit" class="btn btn-primary">Vérifier</button>
                </form>
                <div id="otpError" class="text-danger mt-2" style="display:none;">
                    Code OTP incorrect ou expiré. Veuillez réessayer.
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Fonction pour afficher les modals (utilise jQuery et Bootstrap)
function showModal(modalId) {
    $('#' + modalId).modal('show');
}

function hideModal(modalId) {
    $('#' + modalId).modal('hide');
}

// Fonction pour déclencher le téléchargement
function downloadFile(type) {
    // Construire l'URL avec le paramètre 'type'
    const url = 'download_badge.php?type=' + encodeURIComponent(type);
    window.location.href = url;
}

// Gestion du formulaire de réservation
document.getElementById('formRegister').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche l'envoi traditionnel du formulaire

    var form = this;
    var formData = new FormData(form);

    // Extraire l'email pour l'afficher dans le modal OTP
    var userEmail = formData.get('email');

    // Envoyer les données au serveur pour générer et envoyer l'OTP
    fetch('send_otp.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Réponse du serveur (send_otp.php):', data);
        if (data.success) {
            hideModal('modalCta'); // Fermer le modal de réservation
            // Afficher l'email dans le modal OTP
            document.getElementById('userEmailDisplay').textContent = userEmail;
            showModal('modalOtp'); // Afficher le modal OTP
        } else {
            alert('Erreur lors de l\'envoi de l\'OTP : ' + (data.error || 'Inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite.');
    });
});

// Gestion de la vérification de l'OTP
document.getElementById('formOtp').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche l'envoi traditionnel du formulaire

    var form = this;
    var formData = new FormData(form);

    // Envoyer l'OTP au serveur pour vérification
    fetch('verify_otp.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Réponse du serveur (verify_otp.php):', data);
        if (data.success) {
            hideModal('modalOtp'); // Fermer le modal OTP

            // Afficher une notification avec l'option de téléchargement via SweetAlert2
            Swal.fire({
                title: 'Télécharger votre badge',
                text: 'Choisissez le format de téléchargement',
                icon: 'success',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'PNG',
                denyButtonText: 'PDF',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Télécharger le badge PNG via download_badge.php
                    downloadFile('png');
                } else if (result.isDenied) {
                    // Télécharger le badge PDF via download_badge.php
                    downloadFile('pdf');
                }
                // Si annulé, ne rien faire
            });

            disableVoting(); // Fonction à implémenter si nécessaire
        } else {
            // Afficher un message d'erreur dans le modal OTP
            var otpError = document.getElementById('otpError');
            otpError.style.display = 'block';
            otpError.textContent = data.error || 'Erreur lors de la vérification de l\'OTP.';
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur s\'est produite.');
    });
});
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













              


              