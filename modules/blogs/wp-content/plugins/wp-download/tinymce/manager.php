<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="fr-FR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>WP-Download &rsaquo; Edition</title>
<link rel="stylesheet" href="style.css" type="text/css" />
<script type="text/javascript">
	function CancelForm() 
	{
		window.close();
		return false;
	};

	function ValideForm() 
	{
		var myURL = document.getElementById("url");
		var myTitle = document.getElementById("title");
		var myConditions = document.getElementsByName("conditions");
		var myTop10 = document.getElementsByName("top10");
		opener.WPDownloadInsertCode(myURL.value, myTitle.value, getRadioValue(myConditions), getRadioValue(myTop10));
		window.close();
		return false;
	};
	
	function getRadioValue(radio) {
		for (var i=0; i<radio.length;i++) {
			if (radio[i].checked) {
				return radio[i].value;
			}
		}
	};
</script>
</head>
<body>

<div id="wphead">
<h1>WP-Download <span>(v1.2)</span></h1>
</div>

<form action="manager.php" id="insertForm" method="post">
<table id="inputTable" border="0">
	<tr>
		<td>Adresse du fichier :</td>
		<td><input type="text" id="url" value="http://" style="width:300px"></td>
	</tr>
	<tr>
		<td>Libell&eacute; &agrave; afficher :</td>
		<td><input type="text" id="title" value="" style="width:300px"></td>
	</tr>
	<tr>
		<td>Conditions d'utilisation :</td>
		<td><input type="radio" name="conditions" value="true" checked > Oui &nbsp; <input type="radio" name="conditions" value="false"> Non</td>
	</tr>
	<tr>
		<td>Afficher dans le Top 10 :</td>
		<td><input type="radio" name="top10" value="true" checked > Oui &nbsp; <input type="radio" name="top10" value="false"> Non</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<button type="button" class="button" onclick="return ValideForm();">Valider</button>
      <button type="button" class="button" onclick="return CancelForm();">Annuler</button>
		</td>
	</tr>
</table>
</form>
	
</body>
</html>