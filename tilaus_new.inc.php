

<link rel="stylesheet" type="text/css" href="css/maksu.css">



<div class="stepwizard">
    <div class="stepwizard-row">
        <div class="stepwizard-step">
            <a href="index.php?sivu=nayta_ostoskori_new" class="col1"><button type="button" class="btn btn-primary btn-circle">1</button></a>
            <p>OSTOSKORI</p>
        </div>
        <div class="stepwizard-step">
            <a href="index.php?sivu=tilaus_new" class="col2"><button type="button" class="btn btn-primary btn-circle active">2</button></a>
            <p>OSOITE</p>
        </div>
        <div class="stepwizard-step">
            <a href="index.php?sivu=yhteenveto_new" class="col3"><button type="button" class="btn btn-primary btn-circle">3</button></a>
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


		print '<input type=text name=hakusana size=20 id="hakusanaOsiteKirjasta" placeholder="'.$translate[Vastaanottaja].'">';
		print ' &nbsp;&nbsp;&nbsp;<a href="index.php?sivu=tilaus_new&syotto=true">'.$translate['syota_osoite_itse'].'</a>';
		print "<div id='findedOsiteKirjasta'></div>";

if ($_POST[save]==1)	{
$sqlTemp = mysql_query("SELECT * FROM tilaustmp WHERE tunnus='$tunnus'")  or die (mysql_error());
$rowTemp = mysql_fetch_array($sqlTemp);

if(empty($rowTemp[nro])){
	mysql_query("INSERT INTO tilaustmp SET
	saajaid = '1',
	nimi='$_POST[nimi] $_POST[etunimi] $_POST[sukunimi]',
	osoite1 ='$_POST[osoite]',
	osoite2 ='$_POST[osoite2]',
	postinumero ='$_POST[pno]',
	postitoimipaikka ='$_POST[ptp]',
	puehlinnumero ='$_POST[puhelin]',
	sahkoposti ='$_POST[sahkoposti]',
	sessid='$sessid',
	tunnus='$tunnus'
	");
}}
		
$sqlTemp = mysql_query("SELECT * FROM tilaustmp WHERE tunnus='$tunnus'")  or die (mysql_error());
$rowTemp = mysql_fetch_array($sqlTemp);

if(!empty($rowTemp[saajaid])){
if(!empty($_GET[ajaxID])){
	mysql_query("UPDATE tilaustmp set saajaid='$_GET[ajaxID]' WHERE tunnus='$tunnus'") or die (mysql_error());
}} else	{
	mysql_query("INSERT tilaustmp set saajaid='$_GET[ajaxID]', tunnus='$tunnus', sessid='$sessid'") or die (mysql_error());
}

	$sqlA = mysql_query("SELECT 
	osoitekirja.nimi, 
	osoitekirja.etunimi, 
	osoitekirja.sukunimi,
	osoitekirja.yritys,
	osoitekirja.osoite1,
	osoitekirja.osoite2,
	osoitekirja.postinumero,
	osoitekirja.postitoimipaikka,
	osoitekirja.osavaltio,
	osoitekirja.maa,
	osoitekirja.sahkoposti,
	osoitekirja.puhelinnumero,
	osoitekirja.toimitustapa,
	tilaustmp.saajaid
	FROM osoitekirja,tilaustmp WHERE tilaustmp.saajaid=osoitekirja.id and tilaustmp.tunnus='$tunnus' and sessid='$sessid'") or die (mysql_error());
	$rowA = mysql_fetch_array($sqlA);

if(!empty($rowA[saajaid])){
	print "<br><br>Toimitusosoite<br><br>";
print "$rowA[nimi] $rowA[etunimi] $rowA[sukunimi]<br>";
print "$rowA[yritys]<br>";
print "$rowA[osoite1]<br>";
if (!empty ($rowA[osoite2]))	{print "$rowA[osoite2]<br>";}
print "$rowA[postinumero] $rowA[postitoimipaikka]<br>";
print "$rowA[sahkoposti]<br>";
print "$rowA[puhelinnumero]<br>";
print "";

$sqlTtapa = mysql_query("SELECT * FROM toimitustavat WHERE yritysid='$yritysid' and id='$rowA[toimitustapa]'");
$rowTtapa = mysql_fetch_array($sqlTtapa);
if(!empty($rowA[saajaid])){
print "Toimitustapa<br>";
print "$rowTtapa[toimitustapa]";
print "<a href=index.php?sivu=tilaus_new&ttapa=1> Muuta toimitustapa</a><br>";
}
}	



if ($_GET[syotto]==true)	{
mysql_query("UPDATE tilaustmp set saajaid='' WHERE tunnus='$tunnus' and sessid='$sessid'");

	print "<table>";
	print "<tr></tr>";
	print "<form method=POST>";
	print "<input type=hidden name=save value=1>";
	print "<tr><td><input type=text name=nimi value=$translate[Nimi]></td></tr>";
	print "<tr><td><input type=text name=yritys value=$translate[Yritys]></td></tr>";
	print "<tr><td><input type=text name=osoite1 value=$translate[Osoite]></td></tr>";
	print "<tr><td><input type=text name=osoite2 value=$translate[Osoite]></td></tr>";
	print "<tr><td><input type=text name=pno value=$translate[Postinumero]></td></tr>";
	print "<tr><td><input type=text name=ptp value=$translate[Postitoimipaikka]></td></tr>";	
	print "<tr><td><input type=text name=puhelin value=$translate[Puhelin]></td></tr>";
	print "<tr><td><input type=text name=sahkoposti value=$translate[Sahkoposti]></td></tr>";
	print "<tr><td><input type=submit value=ok></td></tr>";
	print "</form>";
	print "</table>";
	}



// Roman
echo '
<input type="hidden" id="yritysid" value="'.$yritysid.'">

<div class="row">
	<p><button class="button button-warning" id="nouto">'.$translate['nouto_varastossa'].'</button></p>
	<div class="maksu_result osasto_1" id="osasto_1_result"></div>
	<p><button class="button button-warning" id="kuljetus_postiin">'.$translate['kuljetus_postiin'].'</button></p>
	<div class="maksu_result osasto_3" id="osasto_3_result"></div>
	<p><button class="button button-warning" id="kuljetus_perille">'.$translate['kuljetus_perille'].'</button></p>
	<div class="maksu_result osasto_4" id="osasto_4_result"></div>
	<p><button class="button button-warning" id="kuljetus_pisteeseen">'.$translate['kuljetus_pisteeseen'].'</button></p>
</div>

<br>
<p>
<div style="display: inline">
	<div class="inline_block">
	  <form action="index.php?sivu=nayta_ostoskori_new&ajaxID='.$_GET[ajaxID].'" method=POST>
	    <input type=submit class="button button-warning" value="<< Edellinen">&nbsp;
	  </form>
	</div>
	<div class="inline_block">
	  <form action="index.php?sivu=yhteenveto_new" id="seuraavaForm" method=POST>
	    <input align=right class="button button-warning seuraava" type=submit value="Seuraava >>">
	  </form>
	</div>
</div>
</p>
';

?>


		
		
<style>
#findedOsiteKirjasta { display: none; position: absolute; margin-top : -10px; }
</style>

<script type="text/javascript">
$(document).ready(function(){

	var valittu = localStorage.getItem('valittu');
	if(valittu !== null)
	{
		var val = valittu.split("_");
		if(val[2])
		{

        $.ajax({
           url: 'toimitustapa_ajax.php',
           type: "POST",
           data: { "yritysid" : $("#yritysid").val(), "osasto" : val[2] },
           success: function(data){
		var d = JSON.parse(data);
		console.log(d);
		if(d !== 'empty')
		{
		$("#osasto_" + val[2] + "_result").html(d).toggle('slow');
		$('#'+valittu).addClass('button button-success');
		}
           }
        });

		}

	} else {
		$('.col3').addClass('disabled').removeAttr("href");
	}




$("#nouto").click(function(){

	$("#osasto_3_result").hide('slow');
	$("#osasto_4_result").hide('slow');

        $.ajax({
           url: 'toimitustapa_ajax.php',
           type: "POST",
           data: { "yritysid" : $("#yritysid").val(), "osasto" : '1' },
           success: function(data){
		var d = JSON.parse(data);
		console.log(d);
		if(d !== 'empty')
		$("#osasto_1_result").html(d).toggle('slow');
           }
        });

});

$("#kuljetus_postiin").click(function(){

	$("#osasto_1_result").hide('slow');
	$("#osasto_4_result").hide('slow');

        $.ajax({
           url: 'toimitustapa_ajax.php',
           type: "POST",
           data: { "yritysid" : $("#yritysid").val(), "osasto" : '3' },
           success: function(data){
		var d = JSON.parse(data);
		console.log(d);
		if(d !== 'empty')
		$("#osasto_3_result").html(d).toggle('slow');
           }
        });

});

$("#kuljetus_perille").click(function(){

	$("#osasto_1_result").hide('slow');
	$("#osasto_3_result").hide('slow');

        $.ajax({
           url: 'toimitustapa_ajax.php',
           type: "POST",
           data: { "yritysid" : $("#yritysid").val(), "osasto" : '4' },
           success: function(data){
		var d = JSON.parse(data);
		console.log(d);
		if(d !== 'empty')
		$("#osasto_4_result").html(d).toggle('slow');
           }
        });

});


$(document).delegate(".valiko","click",function(){

	var osoite = $('#hakusanaOsiteKirjasta').val();
	if(osoite === '')
	{
		alert('Valitse blaa ja bla bla');
		return false;
	}

	var thisID = $(this).attr('id');
   	localStorage.setItem('valittu', thisID);
	$('.valiko').removeClass('button button-success');
	$(this).addClass('button button-success');

	$('.col3').removeClass('disabled').attr("href" , "index.php?sivu=yhteenveto_new");

});





$(".seuraava").click(function(e){
	e.preventDefault();
	var valittu = localStorage.getItem('valittu');
	var osoite = $('#hakusanaOsiteKirjasta').val();
	if(valittu === null)
	{
		alert('Valitse blaa ja bla bla');
		return false;

	} else if(osoite === '')
	{
		alert('Osoite blaa ja bla bla');
		return false;

	} else {
		$("#seuraavaForm").submit();
	}
});





$("#hakusanaOsiteKirjasta").keyup(function(){
   $("#findedOsiteKirjasta_ajax.php").show();
	var val = $(this).val()

	if(val.length > 2)
	{
        $.ajax({
           url: 'findedOsiteKirjasta_ajax.php',
           type: "POST",
           data: {"value" : val},
           success: function(html){
		$('#findedOsiteKirjasta').html(html).show();
           }
        });
	}

$("html, body").click(function() {
   $("#findedOsiteKirjasta").hide();
});
});

});
</script>

