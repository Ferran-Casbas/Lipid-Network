<html>
<link type = 'text/css' rel= 'stylesheet' href='stylesheet1.css'/>
<head>
	<title>Lipid-Pathway</title>
</head>
<body style=" padding: 0; margin: 0;">
<div class='divhead'>
	<table class='imgtab'>
	<td>
		<h1>Lipid Pathway Network Creator</h1> 
	</td>
	<td >
		<img src='http://www.d-board.eu/dboard/images-multimedia/logos/d-board.png' />
	</td>
	<td >
		<img src='https://www2.warwick.ac.uk/fac/sci/wmg/research/business_transformation/ssg/collaboration/notts.gif' />
	</td>
	</table>
</div>

<form action="Part2.php" method="POST">
	<p colspan = "3" style= 'font-weight: bold' valign="top"></p>
	<p>This software will use a list of lipids to create a network containing the metabolic pathways close to them. The metabolites can be provided with a <a href="http://www.nottingham.ac.uk/~stxfc1/Score-info.html" target="_blank" >score</a> 
	indicating whether they have been found in high concentration or in low concentration.</p>
	<p>In the input boxes below an example data is presented, with two identified lipids 15(S)-HPETE and 5-HPETE that their Lipid ID from lipidMaps we have been obtained. 
	The second box present a metabolite with low quality identification, from the retention time and mass it has been possible to recognize that is a PC with 32 carbons 
	and two double bonds, but since the distribution of the chains remains unknown it will  be placed in the generic box.</p>
	<p>Introduce in the boxes the lipids that have come out from your analysis, rember to separate them between identified and generic. You can introduce your data scored or not.</p>
	<fieldset>
		<legend>Lipidomics Input</legend>
		<input type="radio" name="Scored" value="No" checked>Not scored<br>
		<input type="radio" name="Scored" value="Yes">Scored (separate the score from the naming by a space)<br>
		<table>
			<th>Definitive identity (<a href="http://lipidmaps.org/" target="_blank" >LipidMaps ID</a>)</th>
			<th>  </th>
			<th>Tentative identity</th>
			<th>  </th>
			<tr>
				<td><textarea name="query" cols="40" rows="10">LMFA03060001
LMFA03060002</textarea></td>
				<td>  </td>
				<td><textarea name="query2" cols="40" rows="10">PC(32:2)</textarea></td>
				<td>  </td>
			<tr>
		</table>
	</fieldset>
	<p style='text-align: left'> <input type="submit" value="Create Network"  id='submit'>
	<input type="hidden" name="form" value="yes">
</form>
</body>
</html>
