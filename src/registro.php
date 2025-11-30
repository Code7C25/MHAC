<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - MHAC</title>
    <link rel="stylesheet" href="css/registro.css">

    <style>
      .hidden { display: none; }
    </style>
</head>
<body>

<div class="registro-container">
  <div class="registro-header">
    <div class="registro-logo">🐾</div>
    <h1 class="registro-title">Regístrate</h1>
    <p class="registro-subtitle">¡Únete a MHAC y ayuda a más peluditos!</p>
  </div>

  <?php if (isset($_SESSION['registro_error'])): ?>
    <div class="error-message">
      <?= htmlspecialchars($_SESSION['registro_error']) ?>
    </div>
    <?php unset($_SESSION['registro_error']); ?>
  <?php endif; ?>

  <form action="procesar_registro.php" method="POST" class="registro-form">

    <div class="form-row">
      <div class="form-group nombre">
        <input type="text" name="nombre" class="form-input" placeholder="Nombre" required>
        <label class="form-label">Nombre</label>
      </div>
      <div class="form-group apellido" id="apellido-group">
        <input type="text" name="apellido" class="form-input" placeholder="Apellido">
        <label class="form-label">Apellido</label>
      </div>
    </div>

    <div class="form-group email full-width">
      <input 
      type="email" 
      name="email" 
      class="form-input" 
      placeholder="Correo electrónico" 
      required 
      pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
      title="Ingresa una dirección de correo válida (ej: ejemplo@dominio.com)">
      <label class="form-label">Email</label>
    </div>

<div class="form-group telefono-completo full-width">
    
    <select name="pais_codigo" id="pais-codigo" class="form-select-telefono" required>
        <option value="" disabled selected>País</option>
        
        <option value="+54" data-pais="AR">🇦🇷 Argentina (+54)</option>
        <option value="+591" data-pais="BO">🇧🇴 Bolivia (+591)</option>
        <option value="+56" data-pais="CL">🇨🇱 Chile (+56)</option>
        <option value="+57" data-pais="CO">🇨🇴 Colombia (+57)</option>
        <option value="+506" data-pais="CR">🇨🇷 Costa Rica (+506)</option>
        <option value="+53" data-pais="CU">🇨🇺 Cuba (+53)</option>
        <option value="+593" data-pais="EC">🇪🇨 Ecuador (+593)</option>
        <option value="+503" data-pais="SV">🇸🇻 El Salvador (+503)</option>
        <option value="+34" data-pais="ES">🇪🇸 España (+34)</option>
        <option value="+502" data-pais="GT">🇬🇹 Guatemala (+502)</option>
        <option value="+504" data-pais="HN">🇭🇳 Honduras (+504)</option>
        <option value="+52" data-pais="MX">🇲🇽 México (+52)</option>
        <option value="+505" data-pais="NI">🇳🇮 Nicaragua (+505)</option>
        <option value="+507" data-pais="PA">🇵🇦 Panamá (+507)</option>
        <option value="+595" data-pais="PY">🇵🇾 Paraguay (+595)</option>
        <option value="+51" data-pais="PE">🇵🇪 Perú (+51)</option>
        <option value="+1787" data-pais="PR">🇵🇷 Puerto Rico (+1)</option>
        <option value="+1809" data-pais="DO">🇩🇴 Rep. Dominicana (+1)</option>
        <option value="+598" data-pais="UY">🇺🇾 Uruguay (+598)</option>
        <option value="+58" data-pais="VE">🇻🇪 Venezuela (+58)</option>

        <option disabled>–––––––––– Otros Países ––––––––––</option>

        <option value="+93" data-pais="AF">🇦🇫 Afganistán (+93)</option>
        <option value="+355" data-pais="AL">🇦🇱 Albania (+355)</option>
        <option value="+49" data-pais="DE">🇩🇪 Alemania (+49)</option>
        <option value="+376" data-pais="AD">🇦🇩 Andorra (+376)</option>
        <option value="+244" data-pais="AO">🇦🇴 Angola (+244)</option>
        <option value="+1264" data-pais="AI">🇦🇮 Anguila (+1)</option>
        <option value="+1268" data-pais="AG">🇦🇬 Antigua y Barbuda (+1)</option>
        <option value="+966" data-pais="SA">🇸🇦 Arabia Saudita (+966)</option>
        <option value="+213" data-pais="DZ">🇩🇿 Argelia (+213)</option>
        <option value="+374" data-pais="AM">🇦🇲 Armenia (+374)</option>
        <option value="+297" data-pais="AW">🇦🇼 Aruba (+297)</option>
        <option value="+61" data-pais="AU">🇦🇺 Australia (+61)</option>
        <option value="+43" data-pais="AT">🇦🇹 Austria (+43)</option>
        <option value="+994" data-pais="AZ">🇦🇿 Azerbaiyán (+994)</option>
        <option value="+1242" data-pais="BS">🇧🇸 Bahamas (+1)</option>
        <option value="+880" data-pais="BD">🇧🇩 Bangladés (+880)</option>
        <option value="+1246" data-pais="BB">🇧🇧 Barbados (+1)</option>
        <option value="+973" data-pais="BH">🇧🇭 Baréin (+973)</option>
        <option value="+32" data-pais="BE">🇧🇪 Bélgica (+32)</option>
        <option value="+501" data-pais="BZ">🇧🇿 Belice (+501)</option>
        <option value="+229" data-pais="BJ">🇧🇯 Benín (+229)</option>
        <option value="+1441" data-pais="BM">🇧🇲 Bermudas (+1)</option>
        <option value="+375" data-pais="BY">🇧🇾 Bielorrusia (+375)</option>
        <option value="+387" data-pais="BA">🇧🇦 Bosnia y Herzegovina (+387)</option>
        <option value="+267" data-pais="BW">🇧🇼 Botsuana (+267)</option>
        <option value="+55" data-pais="BR">🇧🇷 Brasil (+55)</option>
        <option value="+673" data-pais="BN">🇧🇳 Brunéi (+673)</option>
        <option value="+359" data-pais="BG">🇧🇬 Bulgaria (+359)</option>
        <option value="+226" data-pais="BF">🇧🇫 Burkina Faso (+226)</option>
        <option value="+257" data-pais="BI">🇧🇮 Burundi (+257)</option>
        <option value="+975" data-pais="BT">🇧🇹 Bután (+975)</option>
        <option value="+238" data-pais="CV">🇨🇻 Cabo Verde (+238)</option>
        <option value="+855" data-pais="KH">🇰🇭 Camboya (+855)</option>
        <option value="+237" data-pais="CM">🇨🇲 Camerún (+241)</option>
        <option value="+1" data-pais="CA">🇨🇦 Canadá (+1)</option>
        <option value="+599" data-pais="BQ">🇧🇶 Caribe Neerlandés (+599)</option>
        <option value="+974" data-pais="QA">🇶🇦 Catar (+974)</option>
        <option value="+235" data-pais="TD">🇹🇩 Chad (+235)</option>
        <option value="+420" data-pais="CZ">🇨🇿 Chequia (+420)</option>
        <option value="+86" data-pais="CN">🇨🇳 China (+86)</option>
        <option value="+357" data-pais="CY">🇨🇾 Chipre (+357)</option>
        <option value="+39" data-pais="VA">🇻🇦 Ciudad del Vaticano (+39)</option>
        <option value="+269" data-pais="KM">🇰🇲 Comoras (+269)</option>
        <option value="+850" data-pais="KP">🇰🇵 Corea del Norte (+850)</option>
        <option value="+82" data-pais="KR">🇰🇷 Corea del Sur (+82)</option>
        <option value="+225" data-pais="CI">🇨🇮 Costa de Marfil (+225)</option>
        <option value="+385" data-pais="HR">🇭🇷 Croacia (+385)</option>
        <option value="+599" data-pais="CW">🇨🇼 Curazao (+599)</option>
        <option value="+45" data-pais="DK">🇩🇰 Dinamarca (+45)</option>
        <option value="+1767" data-pais="DM">🇩🇲 Dominica (+1)</option>
        <option value="+20" data-pais="EG">🇪🇬 Egipto (+20)</option>
        <option value="+971" data-pais="AE">🇦🇪 Emiratos Árabes Unidos (+971)</option>
        <option value="+291" data-pais="ER">🇪🇷 Eritrea (+291)</option>
        <option value="+421" data-pais="SK">🇸🇰 Eslovaquia (+421)</option>
        <option value="+386" data-pais="SI">🇸🇮 Eslovenia (+386)</option>
        <option value="+1" data-pais="US">🇺🇸 Estados Unidos (+1)</option>
        <option value="+372" data-pais="EE">🇪🇪 Estonia (+372)</option>
        <option value="+268" data-pais="SZ">🇸🇿 Esuatini (+268)</option>
        <option value="+251" data-pais="ET">🇪🇹 Etiopía (+251)</option>
        <option value="+63" data-pais="PH">🇵🇭 Filipinas (+63)</option>
        <option value="+358" data-pais="FI">🇫🇮 Finlandia (+358)</option>
        <option value="+679" data-pais="FJ">🇫🇯 Fiyi (+679)</option>
        <option value="+33" data-pais="FR">🇫🇷 Francia (+33)</option>
        <option value="+241" data-pais="GA">🇬🇦 Gabón (+241)</option>
        <option value="+220" data-pais="GM">🇬🇲 Gambia (+220)</option>
        <option value="+995" data-pais="GE">🇬🇪 Georgia (+995)</option>
        <option value="+233" data-pais="GH">🇬🇭 Ghana (+233)</option>
        <option value="+350" data-pais="GI">🇬🇮 Gibraltar (+350)</option>
        <option value="+1473" data-pais="GD">🇬🇩 Granada (+1)</option>
        <option value="+30" data-pais="GR">🇬🇷 Grecia (+30)</option>
        <option value="+299" data-pais="GL">🇬🇱 Groenlandia (+299)</option>
        <option value="+590" data-pais="GP">🇬🇵 Guadalupe (+590)</option>
        <option value="+1671" data-pais="GU">🇬🇺 Guam (+1)</option>
        <option value="+594" data-pais="GF">🇬🇫 Guayana Francesa (+594)</option>
        <option value="+44" data-pais="GG">🇬🇬 Guernsey (+44)</option>
        <option value="+224" data-pais="GN">🇬🇳 Guinea (+224)</option>
        <option value="+240" data-pais="GQ">🇬🇶 Guinea Ecuatorial (+240)</option>
        <option value="+245" data-pais="GW">🇬🇼 Guinea Bisáu (+245)</option>
        <option value="+592" data-pais="GY">🇬🇾 Guyana (+592)</option>
        <option value="+509" data-pais="HT">🇭🇹 Haití (+509)</option>
        <option value="+852" data-pais="HK">🇭🇰 Hong Kong (+852)</option>
        <option value="+36" data-pais="HU">🇭🇺 Hungría (+36)</option>
        <option value="+91" data-pais="IN">🇮🇳 India (+91)</option>
        <option value="+62" data-pais="ID">🇮🇩 Indonesia (+62)</option>
        <option value="+964" data-pais="IQ">🇮🇶 Irak (+964)</option>
        <option value="+98" data-pais="IR">🇮🇷 Irán (+98)</option>
        <option value="+353" data-pais="IE">🇮🇪 Irlanda (+353)</option>
        <option value="+247" data-pais="AC">🇦🇨 Isla Ascensión (+247)</option>
        <option value="+44" data-pais="IM">🇮🇲 Isla de Man (+44)</option>
        <option value="+61" data-pais="CX">🇨🇽 Isla Navidad (+61)</option>
        <option value="+672" data-pais="NF">🇳🇫 Isla Norfolk (+672)</option>
        <option value="+354" data-pais="IS">🇮🇸 Islandia (+354)</option>
        <option value="+358" data-pais="AX">🇦🇽 Islas Aland (+358)</option>
        <option value="+1345" data-pais="KY">🇰🇾 Islas Caimán (+1)</option>
        <option value="+61" data-pais="CC">🇨🇨 Islas Cocos (+61)</option>
        <option value="+682" data-pais="CK">🇨🇰 Islas Cook (+682)</option>
        <option value="+298" data-pais="FO">🇫🇴 Islas Feroe (+298)</option>
        <option value="+500" data-pais="FK">🇫🇰 Islas Malvinas (Falkland) (+500)</option>
        <option value="+1670" data-pais="MP">🇲🇵 Islas Marianas del Norte (+1)</option>
        <option value="+692" data-pais="MH">🇲🇭 Islas Marshall (+692)</option>
        <option value="+677" data-pais="SB">🇸🇧 Islas Salomón (+677)</option>
        <option value="+1649" data-pais="TC">🇹🇨 Islas Turcas y Caicos (+1)</option>
        <option value="+1284" data-pais="VG">🇻🇬 Islas Vírgenes Británicas (+1)</option>
        <option value="+1340" data-pais="VI">🇻🇮 Islas Vírgenes de EE. UU. (+1)</option>
        <option value="+972" data-pais="IL">🇮🇱 Israel (+972)</option>
        <option value="+39" data-pais="IT">🇮🇹 Italia (+39)</option>
        <option value="+1876" data-pais="JM">🇯🇲 Jamaica (+1)</option>
        <option value="+81" data-pais="JP">🇯🇵 Japón (+81)</option>
        <option value="+44" data-pais="JE">🇯🇪 Jersey (+44)</option>
        <option value="+962" data-pais="JO">🇯🇴 Jordania (+962)</option>
        <option value="+7" data-pais="KZ">🇰🇿 Kazajistán (+7)</option>
        <option value="+254" data-pais="KE">🇰🇪 Kenia (+254)</option>
        <option value="+996" data-pais="KG">🇰🇬 Kirguistán (+996)</option>
        <option value="+686" data-pais="KI">🇰🇮 Kiribati (+686)</option>
        <option value="+383" data-pais="XK">🇽🇰 Kosovo (+383)</option>
        <option value="+965" data-pais="KW">🇰🇼 Kuwait (+965)</option>
        <option value="+856" data-pais="LA">🇱🇦 Laos (+856)</option>
        <option value="+266" data-pais="LS">🇱🇸 Lesoto (+266)</option>
        <option value="+371" data-pais="LV">🇱🇻 Letonia (+371)</option>
        <option value="+961" data-pais="LB">🇱🇧 Líbano (+961)</option>
        <option value="+231" data-pais="LR">🇱🇷 Liberia (+231)</option>
        <option value="+218" data-pais="LY">🇱🇾 Libia (+218)</option>
        <option value="+423" data-pais="LI">🇱🇮 Liechtenstein (+423)</option>
        <option value="+370" data-pais="LT">🇱🇹 Lituania (+370)</option>
        <option value="+352" data-pais="LU">🇱🇺 Luxemburgo (+352)</option>
        <option value="+853" data-pais="MO">🇲🇴 Macao (+853)</option>
        <option value="+389" data-pais="MK">🇲🇰 Macedonia del Norte (+389)</option>
        <option value="+261" data-pais="MG">🇲🇬 Madagascar (+261)</option>
        <option value="+60" data-pais="MY">🇲🇾 Malasia (+60)</option>
        <option value="+265" data-pais="MW">🇲🇼 Malaui (+265)</option>
        <option value="+960" data-pais="MV">🇲🇻 Maldivas (+960)</option>
        <option value="+223" data-pais="ML">🇲🇱 Malí (+223)</option>
        <option value="+356" data-pais="MT">🇲🇹 Malta (+356)</option>
        <option value="+212" data-pais="MA">🇲🇦 Marruecos (+212)</option>
        <option value="+596" data-pais="MQ">🇲🇶 Martinica (+596)</option>
        <option value="+230" data-pais="MU">🇲🇺 Mauricio (+230)</option>
        <option value="+222" data-pais="MR">🇲🇷 Mauritania (+222)</option>
        <option value="+262" data-pais="YT">🇾🇹 Mayotte (+262)</option>
        <option value="+691" data-pais="FM">🇫🇲 Micronesia (+691)</option>
        <option value="+373" data-pais="MD">🇲🇩 Moldavia (+373)</option>
        <option value="+377" data-pais="MC">🇲🇨 Mónaco (+377)</option>
        <option value="+976" data-pais="MN">🇲🇳 Mongolia (+976)</option>
        <option value="+382" data-pais="ME">🇲🇪 Montenegro (+382)</option>
        <option value="+1664" data-pais="MS">🇲🇸 Montserrat (+1)</option>
        <option value="+258" data-pais="MZ">🇲🇿 Mozambique (+258)</option>
        <option value="+95" data-pais="MM">🇲🇲 Myanmar (Birmania) (+95)</option>
        <option value="+264" data-pais="NA">🇳🇦 Namibia (+264)</option>
        <option value="+674" data-pais="NR">🇳🇷 Nauru (+674)</option>
        <option value="+977" data-pais="NP">🇳🇵 Nepal (+977)</option>
        <option value="+227" data-pais="NE">🇳🇪 Níger (+227)</option>
        <option value="+234" data-pais="NG">🇳🇬 Nigeria (+234)</option>
        <option value="+683" data-pais="NU">🇳🇺 Niue (+683)</option>
        <option value="+47" data-pais="NO">🇳🇴 Noruega (+47)</option>
        <option value="+687" data-pais="NC">🇳🇨 Nueva Caledonia (+687)</option>
        <option value="+64" data-pais="NZ">🇳🇿 Nueva Zelanda (+64)</option>
        <option value="+968" data-pais="OM">🇴🇲 Omán (+968)</option>
        <option value="+31" data-pais="NL">🇳🇱 Países Bajos (+31)</option>
        <option value="+92" data-pais="PK">🇵🇰 Pakistán (+92)</option>
        <option value="+680" data-pais="PW">🇵🇼 Palaos (+680)</option>
        <option value="+970" data-pais="PS">🇵🇸 Palestina (+970)</option>
        <option value="+675" data-pais="PG">🇵🇬 Papúa Nueva Guinea (+675)</option>
        <option value="+689" data-pais="PF">🇵🇫 Polinesia Francesa (+689)</option>
        <option value="+48" data-pais="PL">🇵🇱 Polonia (+48)</option>
        <option value="+351" data-pais="PT">🇵🇹 Portugal (+351)</option>
        <option value="+44" data-pais="GB">🇬🇧 Reino Unido (+44)</option>
        <option value="+236" data-pais="CF">🇨🇫 República Centroafricana (+236)</option>
        <option value="+242" data-pais="CG">🇨🇬 República del Congo (+242)</option>
        <option value="+243" data-pais="CD">🇨🇩 República Democrática del Congo (+243)</option>
        <option value="+262" data-pais="RE">🇷🇪 Reunión (+262)</option>
        <option value="+250" data-pais="RW">🇷🇼 Ruanda (+250)</option>
        <option value="+40" data-pais="RO">🇷🇴 Rumania (+40)</option>
        <option value="+7" data-pais="RU">🇷🇺 Rusia (+7)</option>
        <option value="+212" data-pais="EH">🇪🇭 Sáhara Occidental (+212)</option>
        <option value="+685" data-pais="WS">🇼🇸 Samoa (+685)</option>
        <option value="+1684" data-pais="AS">🇦🇸 Samoa Americana (+1)</option>
        <option value="+590" data-pais="BL">🇧🇱 San Bartolomé (+590)</option>
        <option value="+1869" data-pais="KN">🇰🇳 San Cristóbal y Nieves (+1)</option>
        <option value="+378" data-pais="SM">🇸🇲 San Marino (+378)</option>
        <option value="+590" data-pais="MF">🇲🇫 San Martín (+590)</option>
        <option value="+508" data-pais="PM">🇵🇲 San Pedro y Miquelón (+508)</option>
        <option value="+1784" data-pais="VC">🇻🇨 San Vicente y las Granadinas (+1)</option>
        <option value="+290" data-pais="SH">🇸🇭 Santa Elena (+290)</option>
        <option value="+1758" data-pais="LC">🇱🇨 Santa Lucía (+1)</option>
        <option value="+239" data-pais="ST">🇸🇹 Santo Tomé y Príncipe (+239)</option>
        <option value="+221" data-pais="SN">🇸🇳 Senegal (+221)</option>
        <option value="+381" data-pais="RS">🇷🇸 Serbia (+381)</option>
        <option value="+248" data-pais="SC">🇸🇨 Seychelles (+248)</option>
        <option value="+232" data-pais="SL">🇸🇱 Sierra Leona (+232)</option>
        <option value="+65" data-pais="SG">🇸🇬 Singapur (+65)</option>
        <option value="+1721" data-pais="SX">🇸🇽 Sint Maarten (+1)</option>
        <option value="+963" data-pais="SY">🇸🇾 Siria (+963)</option>
        <option value="+252" data-pais="SO">🇸🇴 Somalia (+252)</option>
        <option value="+94" data-pais="LK">🇱🇰 Sri Lanka (+94)</option>
        <option value="+27" data-pais="ZA">🇿🇦 Sudáfrica (+27)</option>
        <option value="+249" data-pais="SD">🇸🇩 Sudán (+249)</option>
        <option value="+211" data-pais="SS">🇸🇸 Sudán del Sur (+211)</option>
        <option value="+46" data-pais="SE">🇸🇪 Suecia (+46)</option>
        <option value="+41" data-pais="CH">🇨🇭 Suiza (+41)</option>
        <option value="+597" data-pais="SR">🇸🇷 Surinam (+597)</option>
        <option value="+47" data-pais="SJ">🇸🇯 Svalbard y Jan Mayen (+47)</option>
        <option value="+66" data-pais="TH">🇹🇭 Tailandia (+66)</option>
        <option value="+886" data-pais="TW">🇹🇼 Taiwán (+886)</option>
        <option value="+255" data-pais="TZ">🇹🇿 Tanzania (+255)</option>
        <option value="+992" data-pais="TJ">🇹🇯 Tayikistán (+992)</option>
        <option value="+246" data-pais="IO">🇮🇴 Territorio Británico del Océano Índico (+246)</option>
        <option value="+670" data-pais="TL">🇹🇱 Timor Oriental (+670)</option>
        <option value="+228" data-pais="TG">🇹🇬 Togo (+228)</option>
        <option value="+690" data-pais="TK">🇹🇰 Tokelau (+690)</option>
        <option value="+676" data-pais="TO">🇹🇴 Tonga (+676)</option>
        <option value="+1868" data-pais="TT">🇹🇹 Trinidad y Tobago (+1)</option>
        <option value="+290" data-pais="TA">🇹🇦 Tristán de Acuña (+290)</option>
        <option value="+216" data-pais="TN">🇹🇳 Túnez (+216)</option>
        <option value="+90" data-pais="TR">🇹🇷 Turquía (+90)</option>
        <option value="+993" data-pais="TM">🇹🇲 Turkmenistán (+993)</option>
        <option value="+688" data-pais="TV">🇹🇻 Tuvalu (+688)</option>
        <option value="+380" data-pais="UA">🇺🇦 Ucrania (+380)</option>
        <option value="+256" data-pais="UG">🇺🇬 Uganda (+256)</option>
        <option value="+998" data-pais="UZ">🇺🇿 Uzbekistán (+998)</option>
        <option value="+678" data-pais="VU">🇻🇺 Vanuatu (+678)</option>
        <option value="+84" data-pais="VN">🇻🇳 Vietnam (+84)</option>
        <option value="+681" data-pais="WF">🇼🇫 Wallis y Futuna (+681)</option>
        <option value="+967" data-pais="YE">🇾🇪 Yemen (+967)</option>
        <option value="+253" data-pais="DJ">🇩🇯 Yibuti (+253)</option>
        <option value="+260" data-pais="ZM">🇿🇲 Zambia (+260)</option>
        <option value="+263" data-pais="ZW">🇿🇼 Zimbabue (+263)</option>
    </select>
    
    <div class="input-telefono-compuesto">
        <span id="codigo-display" class="codigo-display">+XX</span>
        
        <input 
            type="tel" 
            name="telefono_numero" 
            id="telefono-numero"
            class="form-input-numero" 
            placeholder="Nro. de ciudad y teléfono" 
            required>
        <input type="hidden" name="telefono_completo" id="telefono-completo">
    </div>

    <label class="form-label">Teléfono</label>
</div>

    <div class="form-row">
      <div class="form-group password">
        <input type="password" name="password" class="form-input" placeholder="Contraseña" required>
        <label class="form-label">Contraseña</label>
      </div>
      <div class="form-group rol">
        <select name="rol" id="rol" class="form-select" required>
          <option value="" disabled selected>Selecciona un rol</option>
          <option value="adoptante">Adoptante</option>
          <option value="refugio">Refugio</option>
          <option value="voluntario">Voluntario</option>
          <option value="hogar_transito">Hogar de tránsito</option>
          <option value="veterinario">Veterinario</option>
          <option value="donante">Donante</option>
           <option value="dador">Dador</option>
        </select>
        <label class="form-label">Rol</label>
      </div>
    </div>

    <button type="submit" class="registro-btn">Registrarse</button>
  </form>

  <div class="registro-footer">
    <p>¿Ya tenés cuenta? <a href="login.php" class="login-link">Iniciá sesión</a></p>
  </div>
</div>

<script>
  document.getElementById("rol").addEventListener("change", function() {
      let apellidoGroup = document.getElementById("apellido-group");
      if (this.value === "refugio") {
          apellidoGroup.classList.add("hidden");
          apellidoGroup.querySelector("input").value = ""; // limpiar
      } else {
          apellidoGroup.classList.remove("hidden");
      }
  });

    // ----------------------------------------------------
    // Lógica para Teléfono y Código de País
    // ----------------------------------------------------
    const selectPais = document.getElementById("pais-codigo");
    const codigoDisplay = document.getElementById("codigo-display");
    const inputNumero = document.getElementById("telefono-numero");
    const inputCompleto = document.getElementById("telefono-completo");

    // Función para actualizar el código visible y concatenar el número final
    function actualizarTelefono() {
        let codigo = selectPais.value || "+XX";
        let numero = inputNumero.value.trim();

        // 1. Actualiza el display
        codigoDisplay.textContent = codigo;
        
        // 2. Concatena el número completo y lo guarda en el campo oculto para PHP
        if (codigo && numero) {
            inputCompleto.value = codigo + numero;
        } else {
            inputCompleto.value = '';
        }
    }

    // Eventos para actualizar
    selectPais.addEventListener("change", actualizarTelefono);
    inputNumero.addEventListener("input", actualizarTelefono);

    // Inicializa el campo al cargar la página (por si se recarga con error)
    actualizarTelefono();

</script>

</body>
</html>
