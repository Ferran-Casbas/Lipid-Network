<?php
if (@$_POST['DownloadButton'] == "Download"){
	$file = '/home/stxfc1/public_html/Results.zip';
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		exit();
	}
}
?>
<!-->The final page contains the downloads links of the results. Also it does the last steps of the calculations wich are adding some extra reactions and scoring the network<!-->

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
		<img src='https://www.nottingham.ac.uk/~stxfc1/D-BOARD2.jpg'/>
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
	//Create Network
	#echo $_POST['Scored'];
	echo "<h3>Results</h3><hr>";
	$cxn = mysqli_connect($host,$user,$password,$_POST['database']);
	$sql="DROP VIEW IF EXISTS Net";
	$result = mysqli_query($cxn,$sql);
	$sql="CREATE VIEW Net AS SELECT Network.Substrate,InteractionType,Product FROM Network where (Network.Substrate = ANY(SELECT * from tab3) or Network.Product = ANY(SELECT * from tab3));";
	$result = mysqli_query($cxn,$sql);	
	//write in a doc
		//The Network
	unlink('/home/stxfc1/public_html/Network.sif');
	$sql="SELECT * FROM Net";
	$result = mysqli_query($cxn,$sql);
	$myfile = fopen("/home/stxfc1/public_html/Network.sif", "w");
	for($i = 0; $i < mysqli_num_rows($result); $i++)
    {
      $first = True;
      $row_array = mysqli_fetch_row($result);
      for($j = 0;$j < mysqli_num_fields($result);$j++) 
      {
		if ($first == False){
			fwrite($myfile,"\t");
			fwrite($myfile,$row_array[$j]);
			$first=False;
		}
		else{
			$first=False;
			fwrite($myfile,$row_array[$j]);
		 }
      }
	  fwrite($myfile,"\n");
    }
	fclose($myfile);
	
		//The info of metabolite
	unlink('/home/stxfc1/public_html/Node-Info.txt');
	$sql="DROP VIEW IF EXISTS Info1";
	$sql="DROP VIEW IF EXISTS Info2";	
	$sql="CREATE VIEW Info1 AS Select Substrate,TypeNode from Net cross join NodeInfo where (Substrate = NodeName)";
	$result = mysqli_query($cxn,$sql);
	$sql="CREATE VIEW Info2 AS Select distinct Product,TypeNode from Net cross join NodeInfo where (Product = NodeName)";
	$result = mysqli_query($cxn,$sql);
	$sql="CREATE VIEW tem AS Select * from Info1 union Select * from Info2";
	$result = mysqli_query($cxn,$sql);
	
	
	$myfile = fopen("/home/stxfc1/public_html/Node-Info.txt", "w");
	
 	if ($_POST['Scored']=="Yes"){
		fwrite($myfile,"Name\tType\tImportance\n");
		#Change here to get score Relative or Univariate! just change the letter of the score from inside the coalesce function R, U
		$sql="select Substrate,TypeNode,COALESCE(ScoreR,0) from tem left join ScoreNodes on Substrate = Node";
		
	}
	elseif ($_POST['Scored']!="Yes"){
		fwrite($myfile,"Name\tType\n");
		$sql="SELECT * from Info1 union select * from Info2 order by TypeNode";	
	}
	$result = mysqli_query($cxn,$sql);	
	for($i = 0; $i < mysqli_num_rows($result); $i++)
    {
      $first = True;
      $row_array = mysqli_fetch_row($result);
      for($j = 0;$j < mysqli_num_fields($result);$j++) 
      {
		if ($first == False){
			fwrite($myfile,"\t");
			fwrite($myfile,$row_array[$j]);
			$first=False;
		}
		else{
			$first=False;
			fwrite($myfile,$row_array[$j]);
		 }
      }
	  fwrite($myfile,"\n");
    }
	fclose($myfile);

	//Run Python!//////////////////////////////////////////////////////////////////////////////////////ADD EXTRA REACTIONS////////////////////////////////////////////////////////////////////////////////
	##Store data in a list
	$handle = @fopen("/home/stxfc1/public_html/Network.sif", "r");
	$doc = array();
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			array_push($doc, $buffer);
		}
		fclose($handle);
	}
	## Find all PC
	$lPC = array();
	foreach ($doc as $line)
		{
		$line= str_replace("\n", "",$line );
		$frags = preg_split("/[\t]/",$line);
		if (substr($frags[0], 0, 2)=='PC'){
			array_push($lPC, $frags[0]);
		}
		if (substr($frags[2], 0, 2)=='PC'){
			array_push($lPC, $frags[2]);
		}	
	}
	#Separate the lyso from no lyso
	$lPCcomb = array();
	$lPClyso = array();
	foreach ($lPC as $PC){
		$a = explode("/",$PC);
		$a = substr($a[1],0,1);
		if ($a =='0'){
			array_push($lPClyso, $PC);
		}else{
			array_push($lPCcomb, $PC);
		}
	}
	$lPCcomb = array_unique($lPCcomb);
	$lPClyso = array_unique($lPClyso);
	#print_r($lPCcomb);
	#print_r($lPClyso);
	#Get a dic of the CoA
	$handle = @fopen("/home/stxfc1/public_html/Coa-list.txt", "r");
	$DCoA = array();
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			$buffer= str_replace("\n", "",$buffer );
			$buffer= str_replace("\r", "",$buffer );
			$frags = preg_split("/[\t]/",$buffer);
			$DCoA[$frags[0]] = $frags[1];
		}
		fclose($handle);
	}
	#print_r($DCoA);
	# $DCoA['10:0']
	$nodeID = 99999;
	$boolpassed = FALSE;
	$OtherMetaUsed = array();	
	$file = @fopen("/home/stxfc1/public_html/Network.sif", "a");
	foreach ($lPCcomb as $i){
		$i = str_replace("\n", "",$i);
		$i = str_replace("\r", "",$i);
		$Qi = str_replace("(", "QQQ",$i);
		$cut = explode("QQQ",$Qi);
		#$cut = $cut[1]
		#$cut = explode("$$",$i)[1];				#Separate in i1 and i2 the two pieces of the PC
		$piece = explode("/",$cut[1]);
		$i1= $piece[0];
		$i2= $piece[1];
		$i2= str_replace(")", "",$i2);	
		#echo $i;	
		foreach ($lPClyso as $a){
			$a = str_replace("\n", "",$a);
			$aQ = str_replace("(", "QQQ",$a);
			$p = explode("QQQ",$aQ);
			$p1 = $p[1];
			$p2= explode("/",$p1);
			$a1=$p2[0];
			#echo "<br>" . $i . " - - " . $i1 . " - - " . $i2 . " - - " . $a . " - - " . $a1; 
			if ($a1 == $i1)	{
				#echo "<br>" . $cut . " - - " . $i . " - - " . $i1 . " - - " . $i2 . " - - " . $a . " - - " . $a1 . " - - " . $DCoA[$i2];
				$boolpassed = TRUE;
				fwrite($file, $i . "\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "Cholesterol\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "LCAT1\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "RN" . $nodeID . "\tinteraction\t" . $a . "\n");
				fwrite($file, "RN" . $nodeID . "\tinteraction\tCE(" . $i2 . ")\n");
				array_push($OtherMetaUsed, "CE(" . $i2 . ")");
				$nodeID+=1;
				fwrite($file, $a . "\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "LPCAT1\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "RN" . $nodeID . "\tinteraction\t" . $i . "\n");
				fwrite($file, $DCoA[$i2] . "\tinteraction\tRN" . $nodeID . "\n");
				array_push($OtherMetaUsed, $DCoA[$i2]);
				$nodeID+=1;
				
			}
			if ($a1 == $i2 and $i1 != $i2)	{
				#echo "<br>" . $cut . " - - " . $i . " - - " . $i1 . " - - " . $i2 . " - - " . $a . " - - " . $a1 . " - - " . $DCoA[$i2];
				$boolpassed = TRUE;
				fwrite($file, $i . "\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "Cholesterol\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "LCAT1\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "RN" . $nodeID . "\tinteraction\t" . $a . "\n");
				fwrite($file, "RN" . $nodeID . "\tinteraction\tCE(" . $i1 . ")\n");
				array_push($OtherMetaUsed, "CE(" . $i1 . ")");
				$nodeID+=1;
				fwrite($file, $a . "\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "LPCAT1\tinteraction\tRN" . $nodeID . "\n");
				fwrite($file, "RN" . $nodeID . "\tinteraction\t" . $i . "\n");
				fwrite($file, $DCoA[$i1] . "\tinteraction\tRN" . $nodeID . "\n");
				array_push($OtherMetaUsed, $DCoA[$i1]);
				$nodeID+=1;			
			}
		}

	}
	#echo "<br>===>" . $var==TRUE ? 'TRUE' : 'FALSE' . "<=====</br>";
	fclose($file);
	#Add also the info of on to the Nod-Info
	#is important to renmove from the OtherMetaUsed all metabolites that are already in the txt to not rewrite
	$handle = @fopen("/home/stxfc1/public_html/Node-Info.txt", "r");
	$NodeInfo = array();
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			array_push($NodeInfo, $buffer);
		}
		fclose($handle);
	}
	$c=0;
	foreach ($OtherMetaUsed as $i){
		foreach( $NodeInfo as $a){
			$metL = preg_split("/[\t]/",$a);
			$met = $metL[0];
			if($met == $i){
				unset($OtherMetaUsed[$c]);
				break;
			}
		}
		$c+=1;
	}
	if ($_POST['Scored']=="Yes"){
		$add = "\t0";
	}else{
		$add = "";
	}
	if ($boolpassed==TRUE){
		#echo " :(";
		$file = @fopen("/home/stxfc1/public_html/Node-Info.txt", "a");
		fwrite($file,"Cholesterol\tMetabolite". $add ."\n");
		fwrite($file,"LCAT1\tGene". $add ."\n");
		fwrite($file,"LPCAT1\tGene". $add ."\n");
		while($nodeID >= 99999) {
			fwrite($file,"RN" . $nodeID . "\tReaction Node". $add ."\n");
			$nodeID--;
		}
		foreach ($OtherMetaUsed as $i){
			fwrite($file, $i. "\tMetabolite". $add ."\n");
		}
	}
	//////////////////////////////////////////////////////////////////////////////////////END////////////////////////////////////////////////////////////////////////////////
	
	
	//Run Python!//////////////////////////////////////////////////////////////////////////////////////Score Network////////////////////////////////////////////////////////////////////////////////
	function IsInTheList($value,$l) {   #THE FUCTION CHECKS IF A VALUE IS IN THE LIST L 
		foreach ($l as $i){
			$i = str_replace("\n", "",$i);
			if($value==$i){
				return True;
				}
			}
		return False;
		
	 }   
	function nicelist($l){				#Transform the list [Type, Score, Importance] in to a string
		$s='';
		if ($l[1] != 0) {
			$s = $l[0] . '	' . $l[1] . '	0.0';
		}
		else {
			#echo $l[0]=='Gene';
			if ($l[0]=='Gene'){
				#echo "Si GENE" . $l[0]  . "<br>" ;
				$s = $l[0] . '	' . $l[1] . '	' . $l[2];
			}else{
				#echo "No GENE" . $l[0]  . "<br>" ;
				$s = $l[0] . '	' . $l[1] . '	' . '0.0';   #for now we will remove it We don't care about the importance of not genes nodes ATM
			}
		}
		return $s;
	}

	#Open and save in a array Node-Info and Network
	$handle = @fopen("/home/stxfc1/public_html/Network.sif", "r");
	$Network = array();
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			array_push($Network, $buffer);
		}
		fclose($handle);
	}
	#Open Node-Info store it as a dic
	$handle = @fopen("/home/stxfc1/public_html/Node-Info.txt", "r");
	$NodeInfo = array();
	$First = True;
	if ($handle) {
		while (($buffer = fgets($handle, 4096)) !== false) {
			if ($First){
				$First = False;
			}else{
				$buffer= str_replace("\n", "",$buffer);
				$l = explode("\t",$buffer);
				$NodeInfo[$l[0]] = array($l[1],$l[2],0);		#[Node]=[Type, Score, Importance]
			}
		}
		fclose($handle);
	}
	#print_r($NodeInfo);
	###First loop find the genes in the network and their adjacent RN, Store it in a dictionary
	$GeneDic = array();
	foreach ($Network as $i) {
		$i = str_replace("\n", "",$i);
		$l = explode("\t",$i);
		if ($NodeInfo[$l[0]][0]== "Gene"){
			if($GeneDic[$l[0]]==""){
				$GeneDic[$l[0]]=array($l[2]);
				}
			else{
				array_push($GeneDic[$l[0]],$l[2]);
				}
			}
	}	
	#This Dictionary has the info of the gene and the RN close to it
	#print_r($GeneDic);
	
	
	###Second loop. This is a triple loop
	$Dup = array();
	foreach($GeneDic as $Gene => $arrayRN) {	#First we loop for each gene
		foreach ($arrayRN as $RN){		#Now we loop on all the RN of a gene
			foreach ($Network as $i) {		#Finally we loop in the Net
				$i = str_replace("\n", "",$i);
				$l = explode("\t",$i);
				if ($l[0] == $RN){							#Check the line contains the RN
					if ($NodeInfo[$l[2]][1]!=0){			#Check that the neithbopur has a score
						#echo "<br> ACTIVE FRONT" . $l[2];
						if (IsInTheList($l[2],$Dup)==False){		#Check if has already excited before
							array_push($Dup,$l[2]);
							$NodeInfo[$Gene][2]+=$NodeInfo[$l[2]][1];
						}
					}
				}
				if ($l[2] == $RN){							#Check the line contains the RN
					if ($NodeInfo[$l[0]][1]!=0){			#Check that the neithbopur has a score
						#echo "<br> ACTIVE BACK";
						
						if (IsInTheList($l[0],$Dup)==False){		#Check if has already excited before
							array_push($Dup,$l[0]);
							$NodeInfo[$Gene][2]-=$NodeInfo[$l[0]][1];
						}
					}
				}
				#echo "<br>". $i ."<br>";
				#print_r($Dup);
			}
		}
		unset($Dup);
		$Dup = array();
	}
	#echo "<br></br><br></br><br></br>";
	#print_r($NodeInfo);
	
	$file = @fopen("/home/stxfc1/public_html/NodeAttributes&Score-Perf.txt", "w");
	fwrite($file,"name	Type	Basic	Importance\n");
	
	foreach ($NodeInfo as $key => $i){
		#echo $key . "===" . $i[0]. $i[1]. $i[2] ;
		#echo  "==========". nicelist($i) ."<br>";
		fwrite($file, $key . "	" . nicelist($i) . "\n");
	   }
	fclose($file);
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	if ($_POST['Scored'] == "Yes"){
		$NodeInfo = "NodeAttributes&Score-Perf.txt";
	}else{
		$NodeInfo = "Node-Info.txt";
	}	

	//Zip the all files to download
	$zip = new ZipArchive();
	$filename = "/home/stxfc1/public_html/Results.zip";
	if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
		exit("cannot open <$filename>\n");
	}
	$zip->addFile($NodeInfo);
	$zip->addFile("Network.sif");
	$zip->addFile("README.docx");
	$zip->addFile("Global-Metabolomics-Layout-2016-09-23.cys");
 

  echo "
      <form action='{$_SERVER['PHP_SELF']}' method='POST'>
		<p>To get the network click on the Download button, in the zip file you can found 3 files, read the README to know how to import your data in to cytoscape.</p>
        <p><input type='submit' name='DownloadButton' value='Download' id='submit'></p>
		<p>Alternatively you can download the 3 different files from the link below.</p>
		<p><a href= http://www.nottingham.ac.uk/~stxfc1/$NodeInfo>Node-Info.txt</a>
		<a href= http://www.nottingham.ac.uk/~stxfc1/Network.sif>Network.sif</a>
		<a href= http://www.nottingham.ac.uk/~stxfc1/README.docx>README.docx</a></p>
      </form>";
?>
</body>
</html>