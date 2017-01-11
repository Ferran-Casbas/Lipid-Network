<html>
<link type = 'text/css' rel= 'stylesheet' href='stylesheet1.css'/>
<head>
	<title>Lipid-Pathway</title>
</head>
<body style=" padding: 0; margin: 0;">
<div class='divhead'>
	<table class='imgtab'>
	<td>
		<h1>Lipid Network Creator</h1> 
	</td>
	<td >
		<img src='http://www.d-board.eu/dboard/images-multimedia/logos/d-board.png' />
	</td>
	<td >
		<img src='https://www2.warwick.ac.uk/fac/sci/wmg/research/business_transformation/ssg/collaboration/notts.gif' />
	</td>
	</table>
</div>
<?php
$host="mysql01.nottingham.ac.uk";
$user="stxfc1_admin";
$password="NBG0XfCmq4Zt";
$_POST['database'] = 'stxfc1_lip_path_gen';
/* Section that executes query and displays the results */  
if(!empty($_POST['form']))
{
	echo "<h3>Analizing</h3><hr>";
	//Empty tables
    $cxn = mysqli_connect($host,$user,$password,$_POST['database']);
    $sql="TRUNCATE Table ImportantNodes";
	$result = mysqli_query($cxn,$sql);
    $cxn = mysqli_connect($host,$user,$password,$_POST['database']);
    $sql="TRUNCATE Table ScoreNodes;";
	$result = mysqli_query($cxn,$sql);

	//Now we have to add all values for each in the table
	$listMeta = explode("\n",$_POST['query']);
	if (($key = array_search('', $listMeta)) !== false) {		##### Remove empty entries
    unset($listMeta[$key]);
	}
	$listMetaG = explode("\n",$_POST['query2']);
	if (($key = array_search('', $listMetaG)) !== false) {		##### Remove empty entries
    unset($listMetaG[$key]);
	}
	$boolspace1 = strlen($listMeta[0])!=strlen(str_replace(" ","",$listMeta[0]));
	$boolspace2 = strlen($listMetaG[0])!=strlen(str_replace(" ","",$listMetaG[0]));
	//Check if is scored and if it has tabs
	if ($_POST['Scored']=="Yes" and !($boolspace1 or $boolspace2)){
		//Err
		echo "<p class=warning>Warning! You have selected scored nodes but you have not provide any score.</p>";
		foreach ($listMeta as $Meta) {
			$Meta = str_replace("\r", "", $Meta);
			$sql='INSERT INTO ImportantNodes (Node) VALUES ("' . $Meta . '")';
			$result = mysqli_query($cxn,$sql);
			}
	} 
	elseif ($_POST['Scored']!="Yes" and ($boolspace1 or $boolspace2)){
		//Err
		echo "<p class=warning>Error! If you want a scored network remember to check the tick box</p>";
	}
 	elseif ($_POST['Scored']=="Yes" and ($boolspace1 or $boolspace2)){
			//Requires scoring to be done
			$Scorebool = "Yes";
			$sumscore = 0;
			$count = 0;
			if ($listMeta[0] != ""){
				foreach ($listMeta as $Meta) {
					$sec = explode(" ",$Meta);
					$Meta = str_replace("\r", "", $sec[0]);
					$sql='INSERT INTO ImportantNodes (Node) VALUES ("' . $Meta . '")';
					$result = mysqli_query($cxn,$sql);
					$sql='SELECT Nodename FROM NodeInfo where LipidMapID = "' . $Meta . '"';
					$result = mysqli_query($cxn,$sql);
					$MetaName= mysqli_fetch_row($result);
					if ($MetaName == ""){
						$sql='SELECT Nodename FROM NodeInfo where NodeName = "' . $Meta . '"';
						$result = mysqli_query($cxn,$sql);
						$MetaName= mysqli_fetch_row($result);
						}
					$Score = str_replace("\r", "", $sec[1]);
					$sql='INSERT INTO ScoreNodes (Node,Score) VALUES ("' . $MetaName[0] . '",' . $Score . ')';
					$result = mysqli_query($cxn,$sql);
					$count += 1;
					$sumscore += abs($Score);
					
					//We can have piece 1 here
					//SELECT NodeName,Score FROM NodeInfo cross join ScoreNodes where LipidMapID = Node;
				}
			}

			$sql="TRUNCATE Table PreImportant";
			$result = mysqli_query($cxn,$sql);
			$listGeneral = explode("\n",$_POST['query2']);
			if ($listGeneral[0] != ""){
				#echo "<BR>..</BR>";
				foreach ($listGeneral as $General) {
					$sql="TRUNCATE Table PreImportante";
					$result = mysqli_query($cxn,$sql);
					$sec = explode(" ",$General);
					$General = str_replace("\r", "", $sec[0]);
					$sql='INSERT INTO PreImportant (Node) VALUES ("' . $General . '")';
					$result = mysqli_query($cxn,$sql);
					$sql='INSERT INTO PreImportante (Node) VALUES ("' . $General . '")';
					$result = mysqli_query($cxn,$sql);
					$Score = str_replace("\r", "", $sec[1]);
					$sql="DROP VIEW IF EXISTS temp";
					$result = mysqli_query($cxn,$sql);
					$sql="CREATE VIEW temp AS (Select Name from Auxiliar where COMBO  in (SELECT * from PreImportante))";
					$result = mysqli_query($cxn,$sql);
					$sql="SELECT * FROM temp";
					$result = mysqli_query($cxn,$sql);
					$count+=1;
					$sumscore+=abs($Score);
					for ($i=0;$i < mysqli_num_rows($result);$i++){
						$row = mysqli_fetch_row($result);
						$ScoreD = (float)$Score/mysqli_num_rows($result);
						//echo $row[0]." ".$ScoreD."<p>";
						$sql='INSERT INTO ScoreNodes (Node,Score) VALUES ("' . $row[0] . '",' . $ScoreD . ')';
						$resultata = mysqli_query($cxn,$sql);
						}
					}
				}
			#detete empty colums
			$sql='DELETE FROM ScoreNodes WHERE Node ="" ';
			$result = mysqli_query($cxn,$sql);
			##
			$Average=$sumscore/$count;
			#echo $Average;
			#echo "<BR></BR>";
			$sql='UPDATE ScoreNodes SET ScoreU = Score /' . $Average . '';
			$result = mysqli_query($cxn,$sql);
			
	}
	elseif ($_POST['Scored']!="Yes" and !($boolspace1 or $boolspace2)){
			$Scorebool = "No";
			//Only Net Req
			foreach ($listMeta as $Meta) {
				$Meta = str_replace("\r", "", $Meta);
				$sql='INSERT INTO ImportantNodes (Node) VALUES ("' . $Meta . '")';
				$result = mysqli_query($cxn,$sql);
				}
			//Now we have to check the general querry to see if we have to add even more
			$sql="TRUNCATE Table PreImportant";
			$result = mysqli_query($cxn,$sql);
			$listGeneral = explode("\n",$_POST['query2']);
			foreach ($listGeneral as $General) {
				$General = str_replace("\r", "", $General);
				$sql='INSERT INTO PreImportant (Node) VALUES ("' . $General . '")';
				$result = mysqli_query($cxn,$sql);
				}
	}


	$sql="DROP VIEW IF EXISTS tab0";
	$result = mysqli_query($cxn,$sql);

	//Translate LipidID to NodeName save it to table 0
	$sql="CREATE VIEW tab0 AS (SELECT Nodename FROM NodeInfo cross join ImportantNodes where LipidMapID = Node)";
	$result = mysqli_query($cxn,$sql);
	
	// ADD general top tab 0 only when the general is given too
	$sql="DROP VIEW IF EXISTS tab01";
	$result = mysqli_query($cxn,$sql);
	$sql="DROP VIEW IF EXISTS tab02";
	$result = mysqli_query($cxn,$sql);
	if($_POST['query2'] <> ""){
		//General info is also given
		$sql="CREATE VIEW tab01 AS (Select Name from Auxiliar where COMBO  in (SELECT * from PreImportant))";
		$result = mysqli_query($cxn,$sql);
		$sql="CREATE VIEW tab02 AS SELECT * from tab0 UNION SELECT * from tab01";
		$result = mysqli_query($cxn,$sql);
		}
	else{
		//No general input is guiven
		$sql="CREATE VIEW tab02 AS SELECT * from tab0";
		$result = mysqli_query($cxn,$sql);		
		}
	$sql="DROP VIEW IF EXISTS Rejected";
	$sql="DROP VIEW IF EXISTS ALLIdentities";
	$result = mysqli_query($cxn,$sql);
	//Report back not used Lipid ID
	$sql="create view Rejected as SELECT Node FROM ImportantNodes where ( Node not in (SELECT distinct LipidMapID from NodeInfo))";
	$result = mysqli_query($cxn,$sql);
	$sql="Create view ALLIdentities as SELECT Node as Nodename FROM rejected where ( Node in (SELECT distinct NodeName from NodeInfo)) union select * from tab02";
	$result = mysqli_query($cxn,$sql);
	$sql="SELECT Node FROM rejected where ( Node not in (SELECT distinct NodeName from NodeInfo))";
	$result = mysqli_query($cxn,$sql);
	$bool = false;
	$FailID = "";
	while($row = mysqli_fetch_row($result))
		{
		$FailID = $FailID . $row[0]. ' ';
		$bool = true;
		}
	if ($bool and $_POST['query'] <> "") {
		echo "<p class=warning>The following lipidsmaps ID were not found in the network and therefore were not added to the network:       ". $FailID ."<br>
			  If you think that the pathway for this metabolite shall be known report this lipid ID back to the following email stxfc1@nottingham.ac.uk</p>";
	}

	//Report back not found generic names
	$sql="SELECT * from PreImportant where ( Node not in (Select distinct COMBO from Auxiliar));";
	$result = mysqli_query($cxn,$sql);
	$bool = false;
	$FailID = "";
	while($row = mysqli_fetch_row($result))
		{
		$FailID = $FailID . $row[0]. ' ';
		$bool = true;
		}
	if ($bool and $_POST['query2'] <> "") {
		echo "<p class=warning>The following generic names were not found in the network and therefore were not added to the network:       ". $FailID ."<br>
		You can check the general Lipid nomenclatur of lipid maps in <a href = 'http://www.lipidmaps.org/data/classification/lipid_cns.html#N'>here.</a><br>
		Also make sure that the lipids from general naming are not a Definitive identity, if they are introduce in the other box with their lipid maps id</p>";
	}	

	//Procesing to create the network from a table 0 tab02
	$sql="DROP VIEW IF EXISTS tab1";
	$result = mysqli_query($cxn,$sql);
	$sql="DROP VIEW IF EXISTS tab2";
	$result = mysqli_query($cxn,$sql);
	$sql="DROP VIEW IF EXISTS tab3";
	$result = mysqli_query($cxn,$sql);
	$sql="CREATE VIEW tab1 AS (SELECT Product FROM Network cross join ALLIdentities where (Network.Substrate = Nodename) or (Network.Product= Nodename))";
	$result = mysqli_query($cxn,$sql);
	$sql="CREATE VIEW tab2 AS (SELECT Substrate FROM Network cross join ALLIdentities where (Network.Substrate = Nodename)or(Network.Product= Nodename))";
	$result = mysqli_query($cxn,$sql);
	$sql="CREATE VIEW tab3 AS SELECT * from tab1 UNION select * from tab2";
	$result = mysqli_query($cxn,$sql);
	$sql="SELECT Network.Substrate,InteractionType,Product FROM Network where (Network.Substrate = ANY(SELECT * from tab3) or Network.Product = ANY(SELECT * from tab3))";
	$result = mysqli_query($cxn,$sql);



	//write in the webpage
	// view of the tentative identities
	$sql="CREATE VIEW block1 as Select COMBO as 'Original', Name from Auxiliar where COMBO  in (SELECT * from PreImportant)";
	$result = mysqli_query($cxn,$sql);
	$sql="select * from block1";
	$result = mysqli_query($cxn,$sql);
	$NUMofMETtentative = @mysqli_num_rows($result);
	//view of the perfect identifications
	$sql="CREATE VIEW block2 as SELECT LipidMapID, NodeName FROM NodeInfo cross join ImportantNodes where LipidMapID = Node or nodeName = Node";
	$result = mysqli_query($cxn,$sql);
	$sql="select * from block2";
	$result = mysqli_query($cxn,$sql);
	$NUMofMETperfect = @mysqli_num_rows($result);
	//assembly of the views
	$sql="create view blockTOT as SELECT * from block1 union select * from block2";
	$result = mysqli_query($cxn,$sql);
	
	
	$sql="select * from blockTOT";
	$result = mysqli_query($cxn,$sql);
	$NUMofMET = @mysqli_num_rows($result);
	### Make the score relative
	$sql='UPDATE ScoreNodes SET ScoreR = ScoreU /' . $count . '';
	$result = mysqli_query($cxn,$sql);
	###
	$sql="select * from blockTOT";
	$result = mysqli_query($cxn,$sql);	
	//$sql="Select COMBO as 'Original', Name as 'Lipids of interest' from Auxiliar where COMBO  in (SELECT * from PreImportant) union SELECT LipidMapID, NodeName FROM NodeInfo cross join ImportantNodes where LipidMapID = Node or nodeName = Node;";
	//$result = mysqli_query($cxn,$sql) or die ("Couldn't execute querry");

	//Error reports
	if($result == false)
	{
		echo "<h4>Error: ".mysqli_error($cxn)."</h4>";
	}
	elseif(@mysqli_num_rows($result) == 0)
	{
		echo "<h4>At least one lipid is necesary to build the network</h4>";
	}
	else
	{
	/* Display results */
	echo "A total of <b>" . $NUMofMET ."</b> metabolites have been identified and are prepare to be used as a selection for the creation of the network image.
	<br>From those <b>" . $NUMofMETperfect . "</b> are provided as perfectly identified lipids and <b>" . $NUMofMETtentative . "</b> have been generated from the tentative identification list.
	<br> If these are correct, click Submit below. Otherwise, click Back.<br><br>";
    echo "<table id='t01'><thead><tr>";

    $finfo = mysqli_fetch_fields($result);
    foreach($finfo as $field)
    {
       echo "<th>".$field->name."</th>";
    }
    echo "</tr></thead>
          <tbody>";
    for ($i=0;$i < mysqli_num_rows($result);$i++)
    {
       echo "<tr>";
       $row = mysqli_fetch_row($result);
       foreach($row as $value)
       {
          echo "<td>".$value."</td>";
       }
       echo "</tr>";
    }
    echo "</tbody></table>";
  }  
}
unlink('/home/stxfc1/public_html/Results.zip');
?>
<form action="Part3-COPY.php" method="POST">
	<p colspan = "3" style= 'font-weight: bold' valign="top"></p>
	<p style='text-align: left'> <input type="submit" value="Submit"  id='submit'>
	<input type="hidden" name="form" value="yes">
	<input type="hidden" name="Scored" value="<?php echo $Scorebool ?>">
</form>
<button onclick="history.go(-1);">Back </button>
</body>
</html>
