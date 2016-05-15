

<link rel="stylesheet" type="text/css" href="css/maksu.css">



<div class="stepwizard">
    <div class="stepwizard-row">
        <div class="stepwizard-step">
            <a href="index.php?sivu=nayta_ostoskori_new"><button type="button" class="btn btn-primary btn-circle">1</button></a>
            <p>OSTOSKORI</p>
        </div>
        <div class="stepwizard-step">
            <a href="index.php?sivu=tilaus_new"><button type="button" class="btn btn-primary btn-circle">2</button></a>
            <p>OSOITE</p>
        </div>
        <div class="stepwizard-step">
            <a href="index.php?sivu=yhteenveto_new"><button type="button" class="btn btn-primary btn-circle active">3</button></a>
            <p>YHTEENVETO</p>
        </div> 
              <div class="stepwizard-step">
            <a href="#"><button type="button" class="btn btn-primary btn-circle">4</button></a>
            <p>MAKSU</p>
        </div>

    </div>
</div>

<br>

<?php

$tunnuskysely = "SELECT * FROM tunnus where `tunnus` = '$tunnus' limit 1";
$tunnushaku = mysql_query($tunnuskysely, $mysqlyhteys) or die("Virhe kyselyssa");

        //haetaan nimi, hinta
        $sahkoposti = mysql_result($tunnushaku, 0, "sahkoposti");
        $puhelinnumero = mysql_result($tunnushaku, 0, "puhelinnumero");


// J‰rjestyksen m‰‰ritys. M‰‰ritell‰‰n ensiksi oletushaku
        $kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus' AND `sessid` = '$sessid' ORDER BY id ASC";

// Jos if lause t‰sm‰‰, niin haetaan jollakin alemmista hakulausekkeista
if ($_GET[jarjesta] == "nimike")
        {
        $kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus' AND `sessid` = '$sessid' ORDER BY tuote ASC";
        }
if ($_GET[jarjesta] == "nimike2")
        {
        $kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus' AND `sessid` = '$sessid'  ORDER BY tuote2 ASC";
        }
if ($_GET[jarjesta] == "versio")
        {
        $kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus' AND `sessid` = '$sessid'  ORDER BY tuote2 ASC";
        }
if ($_GET[jarjesta] == "koodi")
        {
        $kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus' AND `sessid` = '$sessid'  ORDER BY tuotenumero ASC";
        }

        $haku7 = mysql_query($kysely, $mysqlyhteys) or die("Virhe kyselyss‰7");



$summakysely = "SELECT SUM(maara) FROM ostoskori WHERE `tunnus` LIKE '$tunnus' AND `sessid` = '$sessid'";
//suoritetaan kysely

// Haetaan tiedot tilaustmp -v‰liaikaiskannasta

$result5 = mysql_query("SELECT SUM(maara) FROM ostoskori WHERE `tunnus` LIKE '$tunnus' AND `sessid` = '$sessid'" , $mysqlyhteys);
$rivi5 = mysql_fetch_row($result5);
$maarasumma = $rivi5["0"];

if ($maarasumma < "1")
	{
	header( "Location: index.php" );
	}
echo "<html><body>"; 

$tilaustmp = $SQL->_fetch($SQL->_query("SELECT t.toimituspvm, t.aika1, t.aika2, t.haluttu_toimitusvuosi,t.haluttu_toimituskuukausi,t.haluttu_toimituspaiva,t.etunimi,t.sukunimi,t.yritys,o.yritys as oyritys,t.osoite1,o.osoite1 as 
oosoite1,t.postinumero,o.postinumero as 
opostinumero,t.postitoimipaikka,o.postitoimipaikka as opostitoimipaikka,t.viitteenne,t.viesti,t.lisatietoja,t.laheteviesti,t.pakettikorttiviesti,t.haluttu,t.tullausarvo,t.puhelinnumero,o.puhelinnumero as opuhelinnumero,
t.toimitustapa,o.toimitustapa as otoimitustapa,t.saajaid,o.maa as omaa,t.maa,o.osavaltio as oosavaltio,t.osavaltio,o.tilausvahvistus_sp,o.toimitusvahvistus_sp,o.tilausvahvistus_sms,o.toimitusvahvistus_sms,t.sahkoposti

FROM tilaustmp t left join osoitekirja o on  t.saajaid = o.id 
where t.tunnus ='$tunnus' AND 
`sessid` = '$sessid' 
ORDER BY nro DESC"));

$kpha = "select V_KPAIKKA.kpaikka from V_KPAIKKA, tilaustmp where kpnro=tilaustmp.kpaikka and tilaustmp.tunnus='$tunnus' and sessid='$sessid'";
//print "$kpha";
$kph  = mysql_query($kpha, $mysqlyhteys);
$kp = mysql_fetch_array($kph);

if ($tilaustmp["tullausarvo"] != "0" && $tilaustmp[tullausarvo] != "")
		{
	$tullausarvo = "$translate[Tullausarvo]: $tilaustmp[tullausarvo]";
	}


if ($tilaustmp["saajaid"] > 1)
	{
	print '<a href=?sivu=tilaus>' . $translate["Takaisin"] . '</a><br><br>';
	}
	else
	{
	print '<a href=?sivu=tilaus&valinta=syotto>' . $translate["Takaisin"] . '</a><br><br>';
	}

print "<b>$translate_TILAUKSEN_YHTEENVETO (3/4)</b><br><br>
$translate[Onko_tilaus_haluttu]<br><br>";

	if ($tilaustmp["toimitustapa"] != "0")
		{
		$ttid = $tilaustmp["toimitustapa"];
		}
		else
		{
		$ttid = $tilaustmp["otoimitustapa"];
		}

        $toimitustapahaku = $SQL->_fetch($SQL->_query("SELECT * FROM toimitustavat WHERE yritysid=$yritysid and id=$ttid"));

	//print_r($toimitustapahaku);
        $toimitustapatxt = $toimitustapahaku["toimitustapa"];
        $kentat = $toimitustapahaku["kentat"];
        $pakolliset_kentat = $toimitustapahaku["pakolliset_kentat"];


print "<table border=1>
<tr><td><a href=yhteenveto.php?jarjesta=koodi>$translate[Koodi]</a></td><td><a href=yhteenveto.php?jarjesta=nimike>$translate[Nimike]</td><td><a href=yhteenveto.php?jarjesta=nimike2>$translate[Nimike2]</a></td><td>$translate[Maara]</td><td>$translate[Yksikko]</td> ";

if ($hinta_nakyvissa == "1") 
	{
	print "<td>$translate[Hinta]</td>";
	print "<td>$translate[Hinta_yhteensa]</tr>";
	}
$vuosi = date("Y");
$seur_vuosi = $vuosi + 1;
//k‰yd‰‰n tavarat l‰pi
for ($i = 0; $i < mysql_num_rows($haku7); 
   
   $i++) {

 	  //haetaan nimi, hinta
	$tuote = mysql_result($haku7, $i, "tuote");
	$tuote2 = mysql_result($haku7, $i, "tuote2");
	$tuotenumero = mysql_result($haku7, $i, "tuotenumero");
	$tilausnumero = mysql_result($haku7, $i, "tilausnumero");
	$maara = mysql_result($haku7, $i, "maara");
	$RAK = mysql_result($haku7, $i, "rakennekoodi");
	$hinta = mysql_result($haku7, $i, "hinta");
	$hinta_yhteensa = $hinta * $maara;
	if ($hinta == "0")
		{
		$hinta = "";
		}

        if ($yritysid != 22) { 
          $saldokysely = "SELECT OUTGOING,INSTOCK FROM V_WEBSTOCK where code='".$tuotenumero . "' and stocknumber = 1 and yritysid='$yritysid'";
          $saldohaku = mysql_query($saldokysely, $mysqlyhteys) or die("Virhe kyselyss‰");
          @$menossa = mysql_result($saldohaku, 0, "OUTGOING");
          @$saldo = mysql_result($saldohaku, 0, "INSTOCK");

          $varastokysely = "SELECT * FROM VARASTO where KOODI='".$tuotenumero . "' and yritysid='$yritysid'";
          $VARASTOhaku = mysql_query($varastokysely, $mysqlyhteys) or die("Virhe kyselyss‰");
		$ha20 = mysql_fetch_array($VARASTOhaku);
          @$yksikko = mysql_result($VARASTOhaku, 0, "MYKSIKKO");
          @$muotti = mysql_result($VARASTOhaku, 0, "MUOTTI");
        } else {
          $saldokysely = "SELECT tuote_menossa,tuote_varastsaldo,tuote_yksikko,tuote_painokeraily FROM tuote WHERE id=$tuotenumero;";
          $saldohaku   = mysql_query($saldokysely,$mysqlyhteys) or die("Virhe SQL-kyselyss‰");
          $kaikki = mysql_fetch_array($saldohaku);
          @$menossa = $kaikki['tuote_menossa'];
          @$saldo   = $kaikki['tuote_varastsaldo'];
          @$yksikko  = $kaikki['tuote_yksikko'];
          @$muotti   = $kaikki['tuote_painokeraily'];
        }

        $tilat1 = "select sum(instock) as instock, sum(outgoing) as outgoing from V_WEBSTOCK where code='$tuotenumero' and yritysid='$yritysid'";
        $tila1  = mysql_query($tilat1, $mysqlyhteys);
        $til1   = mysql_fetch_array($tila1);

	$tilattavissa = $til1[instock]-$til1[outgoing];


		$result4 = mysql_query("SELECT SUM(maara) FROM tilaukset WHERE `yritysid` LIKE '$yritysid' and `tuotenumero` LIKE
	'$tuotenumero' and siirretty_novaan = '0'" , $mysqlyhteys);
	$rivi4 = mysql_fetch_row($result4);
	$tilattu = $rivi4["0"];

 		$ovh=$ha20[ovh];
 		$hinta_yhteensa=$ha20[ovh]*$maara;
	  //tulostetaan taulukon rivi
		if ($maara > "0")
		{
		$varattu = $tilattu + $menossa;
		$vapaa_saldo = $saldo - $varattu;
		print "<tr><td>$tuotenumero</td><td>$tuote</td><td>$tuote2&nbsp;</td><td>$maara</td><td>$yksikko &nbsp;</td>";
		print "</td>";
		if ($hinta_nakyvissa == "1") 
			{
			print "<td>";
			$hinta = round($ovh, 2);
			printf("%6.2lf",$ovh);
			$hinta_yhteensa = round($hinta_yhteensa, 2);
			print " $ha20[valuutta]</td>";
			print "<td>";
			printf("%6.2lf",$hinta_yhteensa);
			print "$ha20[valuutta]</td>";
			}
		$kokonaishinta = round($kokonaishinta, 2);
		$kokonaishinta = $hinta_yhteensa + $kokonaishinta;

		if ($tilattavissa<"0" || $RAK=="1")	{
			print "<td>$translate[jalkitoimitus]</td>";
			mysql_query("UPDATE tilaustmp SET jalkitoimitus='1' WHERE sessid='$sessid'");
			}
		print "</tr>";
		}
	}
	if ($hinta_nakyvissa == "1")
		{
		print "<tr><td colspan=6 align=right>$translate[Yhteensa]</td><td>";
		printf("%6.2lf",$kokonaishinta);
		print " $ha20[valuutta]</td></tr>";	
		}

	print "</TABLE><br>";


	print "<table><tr>
	<td>$translate[Vastaanottaja]</td></tr>";
	print "	<tr>";
	print "<td valign=top>$tmpyritys<br>";

print "$tilaustmp[etunimi] $tilaustmp[sukunimi]<br>";
print "$tilaustmp[yritys]<br>";
//print "$tilaustmp[oyritys]<br>";
print "$tilaustmp[osoite1]<br>";
//print "$tilaustmp[oosoite1]<br>";
print "$tilaustmp[postinumero] $tilaustmp[postitoimipaikka]<br>";

if ($tilaustmp[oosavaltio] != "") 
	print "	$tilaustmp[oosavaltio]<br>";
	else
	print "	$tilaustmp[osavaltio]<br>";

if ($tilaustmp["maa"] != "") {
	print '	' . $tilaustmp["maa"] . '<br>';}
if ($tilaustmp[puhelinnumero] != "") {
	print "<br>$tilaustmp[puhelinnumero]<br>";
	}else {
	print "<br>$tilaustmp[opuhelinnumero]<br>";
	}
	print "</td>";


	print "<tr><td>$tilaustmp[sahkoposti]<br>";
	print "</td>";

	if ($tilaustmp[tullausarvo] != "0") {
	print "<td valign=top>$tilaustmp[tullausarvo]<br>";}

print "</td></tr>
</table><br>";


if ($maarasumma > "0")
	{
	$veloxexpress="0";
	print "$translate[Toimitustapa]: ";

	if ($yritysid !=="41")	{
	include ("toimitustapa.php");

	}

	if ($loppuasiakas !=="1" && $loppuasiakas !=="2" && $abcryhmaLOP=="0")	{
	print "<br><a href=index.php?sivu=toimitustapamuutos&sessid='$sessid'&veloxexpress=0>$translate[Muuta_toimitustapaa_tai_aikaa]</a><br>";
	}

	if ($tilaustmp['haluttu_toimitusvuosi'] > "0")
	        {

		}	
		else 
		{

		}

//	if ($tilaustmp["lisatietoja"] != "") {
		print "<br>$translate[Viesti_varastolle]:&nbsp". $tilaustmp["lisatietoja"] . "<br>";

//	if ($tilaustmp["viitteenne"] != "") {
		print "$translate[Viesti_laskulle]:&nbsp" . $tilaustmp["viitteenne"] . "<br>";
		print "$translate[Viesti_laskulle]:&nbsp" . $kp["kpaikka"] . "<br>";

//	}
//        if ($tilaustmp["viesti"] != ""	) 
                print "$translate[Viesti_lahetteelle]:&nbsp" . $tilaustmp["laheteviesti"] . "<br>";
                print "$translate[Viesti_pakettikortille]:&nbsp" . $tilaustmp["pakettikorttiviesti"] . "<br>";

	}
if ($tietoja_puuttuu > 0 && is_numeric($tietoja_puuttuu)) 
	{
	print "<font color=#ff0000>$translate[Tietoja_puuttuu_palaa_edelliselle_sivulle]</font><br>";
	}

if ($maarasumma > "0")
        {
	print "<table bgcolor=#ffffcc>";


$kppak = mysql_query("select kpaikkapakote from yritystiedot where yritysid='$yritysid'",$mysqlyhteys);
$kppa = mysql_fetch_array($kppak);

if ($yllapitaja == "1" || $vastuuhenkilo == "1"){

print "<br>$translate[lisaa_saate](PDF):";
print "<form method=post enctype=multipart/form-data>";
print "<table width=350 border=0 cellpadding=1 cellspacing=1 class=box>";
print "<tr><tr></tr><td width=246>";
print "<input type=hidden name=MAX_FILE_SIZE value=5000000>";
print "<input name=tilausvahvistus type=file id=tilausvahvistus>";
print "</td>";
print "<input type=hidden name=test value=1>";
print "<td width=80><input name=upload type=submit class=box id=upload value=Upload></td>";
print "</tr></table></form>";


if(($_POST[test]) =="1")
{
$fileName = $_FILES['tilausvahvistus']['name'];
$tmpName  = $_FILES['tilausvahvistus']['tmp_name'];
$fileSize = $_FILES['tilausvahvistus']['size'];
$fileType = $_FILES['tilausvahvistus']['type'];

$fp      = fopen($tmpName, 'r');
$content = fread($fp, filesize($tmpName));
$content = addslashes($content);
fclose($fp);

if(!get_magic_quotes_gpc())
{
    $fileName = addslashes($fileName);
}

$query = "INSERT INTO upload (yritysid, name, size, type, content, sessid ) ".
"VALUES ('$yritysid','$fileName', '$fileSize', '$fileType', '$content', '$sessid')";
//print "$query<br>";
mysql_query($query) or die('Error, query failed'); 
echo "<br>File $fileName uploaded<br>";
} 

print "<html><head>";
print "<title>Download File From MySQL</title>";
print "<meta http-equiv=Content-Type content=text/html; charset=iso-8859-1>";
print "</head>";
print "<body>";

$query = "SELECT id, name FROM upload where sessid='$sessid'";
$result = mysql_query($query) or die('Error, query failed');
while(list($id, $name) = mysql_fetch_array($result))
{
print "<a href=download.php?id=$id>$name</a> <br>";
}
}

print "</body></html>";
print "</blockquote>";
}


$jalkitoi = mysql_query("SELECT * FROM tilaustmp WHERE sessid='$sessid' and tunnus='$tunnus'");
$jalkit = mysql_fetch_array($jalkitoi);

if ($tilaustmp[toimitustapa]=="16" and $tilaustmp[puhelinnumero]=="") {
print "<b>$translate[Puhelinnumero_on_pakollinen_tieto_valitulle_toimitustavalle]</b>";
} else {
if ($kp[kpaikka] =="" and $kppa[kpaikkapakote] =="1")	{
	print "<a href=index.php?sivu=tilaus&osid=$tilaustmp[saajaid]>Lis&auml&auml kustannuspaikka</a>";
	}	else	{

	if ($yritysid !="41" and $jalkit[jalkitoimitus]=="1")	{
	print "<form action=\"hyvaksy_tilaus3.php\" method=\"POST\">";
	print "<input type=hidden name=jalkit value=1>";
	print "<input type=submit value='$translate_Tilaus_toimitetaan_kun_kaikki_saapuneet'>";
	print "</form>";
	print "<br>";
	print "<form action=\"hyvaksy_tilaus3.php\" method=\"POST\">";
	print "<input type=submit value='$translate_Tilaa_heti_osatoimitus'>";
	print "</form>";
	} else{
	print "<form action=\"hyvaksy_tilaus3.php\" method=\"POST\">";
	print "<input type=submit value=$translate_Tilaa>";
	print "</form>";
	}
	}
}
print "<br><br>";

	 if ($yllapitaja=="1"){
        print "<form action=\"hyvaksy_tilaus3palautus.php\" method=\"POST\">";
        print "<input type=hidden name=tunnus value='$tunnus'>";
        print "<input type=hidden name=sessid value='$sessid'>";
        print "<input type=\"submit\" value='$translate[Palautustilaus]'>";
        print "</form>";
}

	
	print "</table>";

?>
