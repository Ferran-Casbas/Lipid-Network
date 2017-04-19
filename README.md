# Lipid-Pathway

This folder contains the scripts of the network generation and the set of php pages used that carry the analysis in the web interface.

Summary:

LipEnz-BIOGRID.cys	                Cytoescape file containing Biogrid information for the enzymes of the network
Network-Multiplicator.py	          Python script, Generates a more complete lipid pathway
Part1.php		                        Website part 1 contains UI 
Part2.php	                          Website part 2 connects with the MySQL server and check the input lipids, handles errors
Part3.php	                          Website part 3 does final operations and generates the output files
stylesheet1.css                     Style file, formats the php pages


How to:

The Python script contains the code to generate the human lipid network from the basic sif structure file.

This network nformation has to be introduce in the MYSQL server, all instructions of how to make the tables can be found on the MYSQL-SCRIPTS document.

The three php documents are the source code for the website, to run them one would need to setup a MySQL server and have a platform able to run php code. Part 1 displays the UI of the page and allows to input the data in to the text boxes. Part 2 checks for the metabolites that have been introduced, it checks whether they are on the network or not, to do this task it will connect to the MYSQL database and check certain nodes. Part 3 does the creation of the smaller network, the implementation of additional reactions and the scoring of the network.
