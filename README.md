# Lipid-Pathway

This folder contains the scripts of the network generation and the set of php pages used that carry the analysis in the web interface. It also has a cytoscape file with the information of the biogrid mentioned in supplementary information.

The three php documents are the source code for the website, to run them one would need to setup a MySQL server and have a platform able to run php code. Part 1 displays the UI of the page and allows to input the data in to the text boxes. Part 2 checks for the metabolites that have been introduced, it checks whether they are on the network or not, to do this task it will connect to the MYSQL database and check certain nodes. Part 3 does the creation of the smaller network, the implementation of additional reactions and the scoring of the network.


The Python script contains the code to generate the human lipid network from the basic sif structure file.
