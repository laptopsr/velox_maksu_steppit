

<link rel="stylesheet" type="text/css" href="css/maksu.css">



<div class="stepwizard">
    <div class="stepwizard-row">
        <div class="stepwizard-step">
            <a href="index.php?sivu=nayta_ostoskori_new"><button type="button" class="btn btn-primary btn-circle active">1</button></a>
            <p>OSTOSKORI</p>
        </div>
        <div class="stepwizard-step">
            <a href="index.php?sivu=tilaus_new"><button type="button" class="btn btn-primary btn-circle">2</button></a>
            <p>OSOITE</p>
        </div>
        <div class="stepwizard-step">
            <a href="#" class="disabled"><button type="button" class="btn btn-primary btn-circle">3</button></a>
            <p>YHTEENVETO</p>
        </div> 
              <div class="stepwizard-step">
            <a href="#" class="disabled"><button type="button" class="btn btn-primary btn-circle">4</button></a>
            <p>MAKSU</p>
        </div>

    </div>
</div>

<br>


<?php
$sessid = "$_COOKIE[PHPSESSID]";
print "<a href=index.php?>" . $translate["Takaisin"] . "</a>";
//  AND `sessid` = '$sessid'

// J‰rjestyksen m‰‰ritys. M‰‰ritell‰‰n ensiksi oletushaku 
$kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus' AND `maara` > '0' AND `sessid` = '$sessid' ORDER BY id ASC";

// Jos if lause t‰sm‰‰, niin haetaan jollakin alemmista hakulausekkeista. T‰ll‰ k‰yt‰nnˆss‰
// tehd‰‰n myˆhemmin suoritettavaan tuotteiden listaukseen niiden j‰rjestys jonkin kent‰n
// mukaan. T‰ll‰ hetkell‰ erilaisia sorttausperusteita on kolme: koodi, nimike, tai nimike2
if ($_GET[jarjesta] == "nimike")
	{
	$kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus'  AND `maara` > '0' AND `sessid` = '$sessid' ORDER BY tuote DESC";
	}
if ($_GET[jarjesta] == "nimike2")
	{
	$kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus'  AND `maara` > '0' AND `sessid` = '$sessid' ORDER BY tuote2 DESC";
	}
if ($_GET[jarjesta] == "koodi")
	{
	$kysely = "SELECT * FROM ostoskori WHERE tunnus='$tunnus'  AND `maara` > '0' AND `sessid` = '$sessid' ORDER BY tuotenumero ASC";
	}

	$haku7 = mysql_query($kysely, $mysqlyhteys) or die("Virhe kyselyss‰7");

// Haetaan ostoskorissa olevien tuotteiden kokonaism‰‰r‰
$result5 = mysql_query("SELECT SUM(maara) FROM ostoskori WHERE `tunnus` LIKE '$tunnus' AND `sessid` = '$sessid'" , $mysqlyhteys);
$rivi5   = mysql_fetch_row($result5);
$maarasumma = $rivi5["0"];

$result6 = mysql_query("SELECT loppuasiakas FROM tunnus WHERE `tunnus` LIKE '$tunnus'" , $mysqlyhteys);
$rivi6   = mysql_fetch_row($result6);

echo "<html><body>"; 

if ($maarasumma < "1")
	{
	print "$translate[Ostoskorissa_ei_tuotteita]";
	print "</body></html>";
	}
	else
	{

	?>
	<form action="paivita.php" name="theForm" method=POST>
	<?
	
//	print "	<br><br>	<b>$translate[OSTOSKORI] (1/4)</b><br><br>";

	if ($_GET[virhe] == "100")
                {
		print "<b>$translate[Lihavoiduilla_tuotteilla_on_tilausrajoituksia]. <br><br></b>";
		}
	print "<table border=1>";
        print "<tr><td><a href=index.php?sivu=nayta_ostoskori&jarjesta=koodi>$translate[Koodi]</td>";
	print "<td><a href=index.php?sivu=nayta_ostoskori&jarjesta=nimike>$translate[Nimike]</a></td>";
	print "<td><a href=index.php?sivu=nayta_ostoskori&jarjesta=nimike2>$translate[Nimike2]</a></td>";
        print "<td>$translate[Maara]</td><td>$translate[Yksikko]</td>";
// Roman
if(!is_mobiili())
	print "<td>$translate[Varastosaldo]</td>";

	if ($hinta_nakyvissa == "1")
		{
		print "<td>" . $translate["Hinta"] . "</td><td>" . $translate["Hinta_yhteensa"] . "</td>";
		}

	print "<td>&nbsp;</td><td>&nbsp;</td></tr>";
	$vuosi = date("Y");
	$seur_vuosi = $vuosi + 1;
	print "<INPUT TYPE=\"hidden\" NAME=\"maarapaivitys\" SIZE=\"3\" value=1>";
	//k‰yd‰‰n tavarat l‰pi
	for ($i = 0; $i < mysql_num_rows($haku7); 
	   $i++) {
   		//haetaan nimi, hinta
		$tuote = mysql_result($haku7, $i, "tuote");
		$tuote2 = mysql_result($haku7, $i, "tuote2");
		$tuotenumero = mysql_result($haku7, $i, "tuotenumero");
		$maara = mysql_result($haku7, $i, "maara");	
		$lihavointi = mysql_result($haku7, $i, "lihavointi");	

		$saldokysely = "SELECT V_WEBSTOCK.outgoing,V_WEBSTOCK.instock,";
		$saldokysely .= "VARASTO.myksikko,VARASTO.muotti,VARASTO.ovh, VARASTO.valuutta ";
		$saldokysely .= "FROM V_WEBSTOCK,VARASTO where V_WEBSTOCK.code";
		$saldokysely .= "=VARASTO.koodi and V_WEBSTOCK.code = ";
		$saldokysely .= "'$tuotenumero' and V_WEBSTOCK.yritysid='$yritysid' and VARASTO.yritysid='$yritysid' and V_WEBSTOCK.stocknumber = '1'";
		//echo $saldokysely;
		$saldohaku = mysql_query($saldokysely, $mysqlyhteys) or die("Virhe kyselyss‰");

        $sumkysely = "SELECT SUM(instock) as summa, SUM(outgoing) as outg, SUM(incoming) as inco FROM V_WEBSTOCK where code='".$tuotenumero . "' and yritysid=$yritysid LIMIT 1";
		$sum_temp_result = mysql_query($sumkysely, $mysqlyhteys);
		$sum_kentat   = mysql_fetch_array($sum_temp_result);
		$menossa      = $sum_kentat[outg];
		$saldo        = $sum_kentat[summa];

	        //@$menossa = mysql_result($saldohaku, 0, "outgoing"); 
	        //@$saldo = mysql_result($saldohaku, 0, "instock");

	        @$yksikko = mysql_result($saldohaku, 0, "myksikko");
	        @$muotti = mysql_result($saldohaku, 0, "muotti");
	        @$hinta = mysql_result($saldohaku, 0, "ovh");
	        @$valuutta = mysql_result($saldohaku, 0, "valuutta");

		$b = "";
		$b2 = "";
 
		// tulostetaan taulukon rivi
		if ($maara > "0")
			{
			$varattu = $menossa + $tilattu;
			$vapaa_saldo = $saldo - $varattu;
			if ($lihavointi == "1")
				{
				$b =  "<b>";
				$b2 = "</b>";
				}

			print "<tr><td>$b $tuotenumero $b2</td>";

			if ($loppuasiakas =="1" || $loppuasiakas =="2" || $abcryhmaLOP=="1")	{
			print "<td>$b $tuote $b2</td>";
			} else {
			print "<td>$b <a href=index.php?sivu=nayta&koodi=$tuotenumero&listaa=tuotetiedot>$tuote</a> $b2</td>";
			}
			print "<td>$b$tuote2 $b2&nbsp;</td>";
                        print "<td><INPUT TYPE=\"text\" NAME=\"maara_$tuotenumero\"";
                        print " SIZE=\"3\" value=$maara></td>";

			// Valmistaudutaan myˆs tuotteeseen, jolle ei ole m‰‰ritelty yksikkˆ‰. Eli kentt‰ on silloin ''
                        // ..ja se tulostetaan k‰ytt‰en HTML-entiteetti‰ non-breaking space eli nbsp. 
                        if (strlen($yksikko)>0) { print "<td>$yksikko</td>"; } else { print "<td>&nbsp;</td>"; }

// Roman
if(!is_mobiili()){
                        print "<td>";
			if ($muotti != "1")
				{
					if ($loppuasiakas !== "1" and $loppuasiakas !=="2" && $abcryhmaLOP=="0") { printf("%4.0lf",$vapaa_saldo); } else 
						{
							if ($vapaa_saldo <= 0) { print "Tulossa"; } else { print "Tilattavissa"; }
						}
				}
			print "&nbsp;</td>";
}
			if ($hinta_nakyvissa == "1")
				{
				$rivihinta = $hinta * $maara;
				print "<td align=right>" . str_replace("." , "," , sprintf("%01.2f",$hinta)) . " $valuutta</td><td align=right>" . str_replace("." , "," , sprintf("%01.2f", $rivihinta)) . " $valuutta</td>";
				$yhteensa = $yhteensa + $rivihinta;
				}

			print "$nayta";
			echo "<td><a href=\"poista_ostoskorista.php?koodi=". ($tuotenumero)."\">$translate[Poista]</a></td></tr>";

			}


		}
  	if ($hinta_nakyvissa == "1")
		{
		print "<tr><td colspan=7 align=right>" . $translate["Yhteensa"] . "</td><td align=right>" . str_replace("." , "," , sprintf("%01.2f", $yhteensa)) . " $valuutta</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
		}

	echo "</table>";

	if($yllapitaja=="1")	{
	$kysely = mysql_query("SELECT * FROM ostoskori WHERE tunnus='$tunnus' AND `maara` > '0' AND `sessid` = '$sessid' ORDER BY id ASC");	
	$jr=mysql_fetch_array($kysely);
	print "<input type=checkbox name=copy value='1' ".($jr[ostoskoripohja]=='1'?"checked":"").">$translate[Jata_tuotteet_ostoskoriin_pohjaksi_seuraavalle_tilaukselle]<br><br>";
	}
	
	?>


	<input type="submit" name="submit" value="<? print "$translate_Tallenna_muutokset";?>" onClick="document.theForm.action='paivita.php'">
	<?
	print " 
	<br>
	$translate_ohje1

	<br><br>

	";
	//Please enter the ordered amount for each item.
	if ($paivitetty == "1")
	        {
		print "Tiedot p‰ivitetty";
		}

	
	$result5 = mysql_query("SELECT SUM(maara) FROM ostoskori WHERE `tunnus` LIKE '$tunnus' AND `sessid` = '$sessid'" , 
	$mysqlyhteys);
	$rivi5 = mysql_fetch_row($result5);
	$maarasumma = $rivi5["0"];


	 if ($maarasumma > "0")
		{
		?>
		<input type="submit" name="submit" value="Seuraava >>" onClick="document.theForm.action='paivita2.php'">
		<?
		print "
		</form>";
		}
		else {
		print "Ostoskorissa ei tuotteita<br><br>";
		}


		echo "</body></html>"; 
	}
	
?>




<script type="text/javascript">
$(document).ready(function(){

	localStorage.removeItem('valittu');


});
</script>

