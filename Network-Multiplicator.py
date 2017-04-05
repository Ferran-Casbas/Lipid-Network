#The program generates the network with the majority of pathways. The Base start point is the General.sif
#From the program have already the information of how the nodes should be conected
#Author: Ferran Casbas
#Created: 14/10/14
#Last Revision:24/11/15
import re

filename = '../Data/Network/Inproces/General.sif'   #This file contains the main pathways in a low cuality format 

f =open(filename, 'r')
out=open('Multiplied.sif','w')
listofchains=[]

AcidL = open('./listcheck/Acids-list.txt').readlines()
D= {}
for i in AcidL:
    key = i.split('\t')[0]
    value= i.split('\t')[1]
    D[key]=value
### THis section we identify all the FA chains that are going to be used to make the different combinations of PC, PE ...
for line in f:
    out.write(line)
    #matchObj = re.search( r'\([0-9]:|\([0-9][0-9]', line, re.M|re.I)
    matchObj = re.search( r'CoA \(', line, re.M|re.I)
    if matchObj:
        #We have to format that list
        linelist=line.split("\t")
        linelist[-1]=linelist[-1][0:-1]
        newline='.'.join(linelist).split('.')
        for i in linelist:
            try:
                int(i)
                newline.remove(i)
            except ValueError:
                pass
            if i == 'pp':
                newline.remove(i)
            if i == 'interaction':
                newline.remove(i)
            if i == 'Any length CoA':
                newline.remove(i)
        listofchains.append(newline[0])

listofchains=list(set(listofchains))
listofchains=sorted(listofchains)
#This sections adds the reactions of the carnitines
nodeID = 700   #contains about 63 nodes in here
for i in listofchains:
    out.write(i+'\tpp\t'+str(nodeID)+'\n')
    out.write('CPT1\tpp\t'+str(nodeID)+'\n')
    out.write('L-carnitine'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'L-'+str(i.split()[0])+'carnitine'+'\n')
    nodeID+=1
    out.write('L-'+str(i.split()[0])+'carnitine'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+i+'\n')
    out.write('CPT2\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'L-carnitine'+'\n')
    nodeID+=1

#This section adds the reaction of the Cholesterol esters
for i in listofchains:
    out.write('Cholesterol'+'\tpp\t'+str(nodeID)+'\n')
    out.write('SOAT1/SOAT2'+'\tpp\t'+str(nodeID)+'\n')
    out.write(i+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'CE'+i.split()[-1]+'\n')
    nodeID+=1
    
# Here we reduce the number of chains in the combinatoru since some of the combinations are not possible and they would be dummy nodes
smalllistofchains = []
#print(len(listofchains))

for i in listofchains:
    a = i
    i = i.split()[-1]
    if len(i.split(':')[0]) != 2:
        if  i.split(':')[0] != '(10':
            if i.split(':')[0] != '(24' or i.split(':')[1][0] == '0':
                if i != '(18:2[6,9])' and  i != '(20:3[5,8,11])' and  i != '(18:3[6,9,12])' and  i != '(20:4[8,11,14,17])': 
                    #print(i)
                    smalllistofchains.append(a)
listofchains = smalllistofchains

#This section adds the reactions of the spingolipids pathway
nodeID=1000  #abut 415 reactions will be generated in here
for i in listofchains:
    out.write(i+'\tpp\t'+str(nodeID)+'\n')
    #Condition for different lass
    if i.split()[-1][1:5] == '16:0':
        out.write('LASS5/LASS6\tpp\t'+str(nodeID)+'\n')
    else:
        if i.split()[-1][1:5] == '18:0':
            out.write('LASS1/LASS4\tpp\t'+str(nodeID)+'\n')
        else:
            if i.split()[-1][1:5] == '20:0' or i.split()[-1][1:5] == '22:0':
                out.write('LASS4\tpp\t'+str(nodeID)+'\n')
            else:
                if i.split()[-1][1:5] == '24:0':
                    out.write('LASS2\tpp\t'+str(nodeID)+'\n')
                else:
                    out.write('LASS1/2/3/4/5/6\tpp\t'+str(nodeID)+'\n')
    out.write('Sphinganine'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DH Cer'+i.split()[-1]+'\n')
    nodeID+=1
    out.write('Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UGT8'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Gal Cer'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('DH Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('CERK'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DH CerP'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UGCG'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Glc Cer'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('DH Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('DEGS1'+'\tpp\t'+str(nodeID)+'\n')
    out.write('DEGS2'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Cer'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('SM'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('SMPD1'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Cer'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('CERK'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'CerP'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('DH Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UGCG'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DH Glc Cer'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('DH Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UGT8'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DH Gal Cer'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('DH SM'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UGT8'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DH Cer'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('SGMS1'+'\tpp\t'+str(nodeID)+'\n')
    out.write('SGMS2'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'SM'+i.split()[-1]+'\n')    
    nodeID+=1
    out.write('DH Cer'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('SGMS1'+'\tpp\t'+str(nodeID)+'\n')
    out.write('SGMS2'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DH SM'+i.split()[-1]+'\n')    
    nodeID+=1

    ##Phyto spyngolipids
    out.write('Phyto-ceramide'+i.split()[-1]+'\tpp\t'+str(nodeID)+'\n')
    out.write('ASAH1/ASAH2'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Phyto-sphingosine'+'\n')
    out.write(str(nodeID)+'\tpp\t'+D[i.split()[-1].replace("(","").replace(")","")][:-1]+'\n')
    nodeID+=1


## Glycero phopholipids
nodeID = 2000
out.write('Choline-P'+'\tpp\t'+str(nodeID+1)+'\n')
out.write('Pcyt1a\tpp\t'+str(nodeID+1)+'\n')
out.write(str(nodeID)+'\tpp\t'+'CDP-Choline\n')
nodeID+=1
out.write('Choline\tpp\t'+str(nodeID)+'\n')
out.write('Chkb\tpp\t'+str(nodeID)+'\n')
out.write(str(nodeID)+'\tpp\t'+'Choline-P\n')
nodeID+=1
out.write('Glycerol-3-P\tpp\t'+str(nodeID)+'\n')
out.write('GPD1L\tpp\t'+str(nodeID)+'\n')
out.write('GPD2\tpp\t'+str(nodeID)+'\n')
out.write(str(nodeID)+'\tpp\t'+'Glycerone-P\n')
nodeID+=1
out.write('Glycerone-P\tpp\t'+str(nodeID)+'\n')
out.write('GPD1L\tpp\t'+str(nodeID)+'\n')
out.write('GPD2\tpp\t'+str(nodeID)+'\n')
out.write(str(nodeID)+'\tpp\t'+'Glycerol-3-P\n')
nodeID+=1
DGList=[]
Lcombos=[]
LcombosZERO=[]
c=0

#Glycero Phospholipids
for a in range(len(listofchains)):
    b = 'Auxiliar CoA (0)'  #This help us generate all the Lyso PC, Lyso PE...
    comboZERO=listofchains[a].split()[-1][0:-1]+'/'+b.split()[-1][1:]
    for b in range(len(listofchains)):
        if a>=b:
            combo=listofchains[a].split()[-1][0:-1]+'/'+listofchains[b].split()[-1][1:]
            #out.write('55\tpp\t'+'PA'+combo+'\n')
            out.write('PE'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Ptdss2\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PS'+combo+'\n')
            nodeID+=1
            DGList.append('DG'+combo)
            out.write('DG'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Dgkz\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA'+combo+'\n')
            nodeID+=1
            out.write('PS'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Pisd\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE'+combo+'\n')
            nodeID+=1
            out.write('PC'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Ptdss1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PS'+combo+'\n')
            nodeID+=1
            out.write('PA'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Cds1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'CDP-DG'+combo+'\n')
            nodeID+=1
            out.write('PGP'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PG'+combo+'\n')
            nodeID+=1
            out.write('PE-Me'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Pemt\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE-(Me)2'+combo+'\n')
            nodeID+=1
            out.write('PE'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Pemt\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE-Me'+combo+'\n')
            nodeID+=1
            out.write('CDP-DG'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PGP'+combo+'\n')
            nodeID+=1
            out.write('PC'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('PLD1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA'+combo+'\n')
            out.write(str(nodeID)+'\tpp\t'+'Choline\n')
            nodeID+=1
            out.write('PE'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('PLD1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA'+combo+'\n')
            nodeID+=1
            out.write('PE-(Me)2'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Pemt\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PC'+combo+'\n')
            nodeID+=1
            out.write('PA'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('PPAP2A\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'DG'+combo+'\n')
            nodeID+=1
            out.write('CDP-PE'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE'+combo+'\n')
            nodeID+=1
            out.write('PI'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Cdipt\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'CDP-DG'+combo+'\n')
            nodeID+=1
            out.write('DG'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
            out.write('CDP-Etn'+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE'+combo+'\n')
            nodeID+=1
            out.write('DG'+combo+'\tpp\t'+str(nodeID)+'\n')
            out.write('Chpt1\tpp\t'+str(nodeID)+'\n')
            out.write('CDP-Choline'+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PC'+combo+'\n')
            nodeID+=1
            out.write('PA(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write('PPAP2A\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'DG(O-'+combo[1:]+'\n')
            nodeID+=1
            out.write('DG(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE(O-'+combo[1:]+'\n')
            nodeID+=1
            out.write('DG(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PC(P-'+combo[1:]+'\n')
            nodeID+=1
            out.write('PE(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write('UG1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE(P-'+combo[1:]+'\n')
            nodeID+=1
            out.write('PE(P-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write('PLD4\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA(P-'+combo[1:]+'\n')
            nodeID+=1
            out.write('PE(P-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'DG(P-'+combo[1:]+'\n')
            nodeID+=1
            out.write('PE(P-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write('UG2\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PC(P-'+combo[1:]+'\n')
            nodeID+=1
            Lcombos.append(combo)
    b = 'Auxiliar CoA (0)'        
    combo=listofchains[a].split()[-1][0:-1]+'/'+b.split()[-1][1:]
    #print(listofchains[a],combo)
    out.write(listofchains[a]+'\tpp\t'+str(nodeID)+'\n')
    out.write('Gpam\tpp\t'+str(nodeID)+'\n')
    out.write('Glycerol-3-P'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PA'+combo+'\n')
    nodeID+=1
    out.write('83\tpp\t'+'PE'+combo+'\n')
    #out.write('55\tpp\t'+'1-acyl-PA'+listofchains[a].split()[-1]+'\n')
    out.write('PE'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Ptdss2\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PS'+combo+'\n')
    nodeID+=1
    out.write('MG'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Dgkz\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PA'+combo+'\n')
    nodeID+=1
    out.write('PS'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Pisd\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PE'+combo+'\n')
    nodeID+=1
    out.write('PC'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Ptdss1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PS'+combo+'\n')
    nodeID+=1
    out.write('PA'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Cds1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'CDP-MG'+combo+'\n')
    nodeID+=1
    out.write('PGP'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PG'+combo+'\n')
    nodeID+=1
    out.write('PE-Me'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PE-(Me)2'+combo+'\n')
    nodeID+=1
    out.write('PE'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Pemt\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PE-Me'+combo+'\n')
    nodeID+=1
    out.write('CDP-MG'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PGP'+combo+'\n')
    nodeID+=1
    out.write('PC'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('PLD1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PA'+combo+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Choline\n')
    nodeID+=1
    out.write('PE'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('PLD1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PA'+combo+'\n')
    nodeID+=1
    out.write('PE-(Me)2'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PC'+combo+'\n')
    nodeID+=1
    out.write('PA'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Ppap2a\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'MG'+combo+'\n')
    nodeID+=1
    out.write('PI'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Cdipt\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'CDP-MG'+combo+'\n')
    nodeID+=1
    out.write('MG'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('Chpt1\tpp\t'+str(nodeID)+'\n')
    out.write('CDP-Choline'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PC'+combo+'\n')
    nodeID+=1
    ##Ethers
    out.write(listofchains[a]+'\tpp\t'+str(nodeID)+'\n')
    out.write('GNPAT\tpp\t'+str(nodeID)+'\n')
    out.write('Glycerone-P'+'\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Glycerone-Phosphate'+combo+'\n')
    nodeID+=1
    out.write('Glycerone-Phosphate'+combo+'\tpp\t'+str(nodeID)+'\n')
    out.write('AGPS\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'Glycerone-Phosphate(O-'+combo[1:]+'\n')
    nodeID+=1
    out.write('Glycerone-Phosphate(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UG6\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PA(O-'+combo[1:]+'\n')
    nodeID+=1
    out.write('PA(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('PPAP2A\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DG(O-'+combo[1:]+'\n')
    nodeID+=1
    out.write('DG(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PE(O-'+combo[1:]+'\n')
    nodeID+=1
    out.write('DG(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PC(P-'+combo[1:]+'\n')
    nodeID+=1
    out.write('PE(O-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UG1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PE(P-'+combo[1:]+'\n')
    nodeID+=1
    out.write('PE(P-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('PLD4\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PA(P-'+combo[1:]+'\n')
    nodeID+=1
    out.write('PE(P-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('CEPT1\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'DG(P-'+combo[1:]+'\n')
    nodeID+=1
    out.write('PE(P-'+combo[1:]+'\tpp\t'+str(nodeID)+'\n')
    out.write('UG2\tpp\t'+str(nodeID)+'\n')
    out.write(str(nodeID)+'\tpp\t'+'PC(P-'+combo[1:]+'\n')
    nodeID+=1
    LcombosZERO.append(combo)

    
#This sections connects some of the pathways between them
for a in LcombosZERO:
    #print(a.split()[-1][1:-1])
    for combo in Lcombos:
        combo = combo[1:-1]
        comboZERO=a
        #print(combo.split('/')[0],combo.split('/')[1],a)
        if combo.split('/')[0]==a[1:-4] or combo.split('/')[1]==a[1:-4]:
            out.write('PE('+combo+')\tpp\t'+str(nodeID)+'\n')
            out.write('Pla2g1b\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PE'+comboZERO+'\n')
            nodeID+=1
            out.write('PC('+combo+')\tpp\t'+str(nodeID)+'\n')
            out.write('Pla2g1b\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PC'+comboZERO+'\n')
            nodeID+=1
for a in Lcombos:
    for b in listofchains:
        #print(b.split()[-1][1:-1],a.split('/')[0][1:])
        if b.split()[-1][1:-1]==a.split('/')[0][1:]:
            comboZERO='('+a.split('/')[1][:-1]+'/0)'
            out.write(b+'\tpp\t'+str(nodeID)+'\n')
            out.write('Agpat4/Agpat6\tpp\t'+str(nodeID)+'\n')
            out.write('PA'+comboZERO+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA'+a+'\n')
            nodeID+=1
            out.write(b+'\tpp\t'+str(nodeID)+'\n')
            out.write('UG7\tpp\t'+str(nodeID)+'\n')
            out.write('PA(O-'+comboZERO[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA(O-'+a[1:]+'\n')
            nodeID+=1
        if b.split()[-1][1:-1]==a.split('/')[1][:-1]:
            comboZERO='('+a.split('/')[0][1:]+'/0)'
            out.write(b+'\tpp\t'+str(nodeID)+'\n')
            out.write('Agpat4/Agpat6\tpp\t'+str(nodeID)+'\n')
            out.write('PA'+comboZERO+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA'+a+'\n')
            nodeID+=1
            out.write(b+'\tpp\t'+str(nodeID)+'\n')
            out.write('UG7\tpp\t'+str(nodeID)+'\n')
            out.write('PA(O-'+comboZERO[1:]+'\tpp\t'+str(nodeID)+'\n')
            out.write(str(nodeID)+'\tpp\t'+'PA(O-'+a[1:]+'\n')
            nodeID+=1
pair = open('listcheck/pair-raw.txt','w')
for i in DGList:
    pair.write(i.split('G')[1])
    pair.write('\n')
pair.close()

##Triacylglycerides generation here:
TGList=[]

for a in DGList:
    for b in listofchains:
        out.write(a+'\tpp\t'+str(nodeID)+'\n')
        out.write('MOGAT3'+'\tpp\t'+str(nodeID)+'\n')
        out.write(b+'\tpp\t'+str(nodeID)+'\n')
        combo=a.split('G')[1][1:-1]+'/'+b.split()[-1][1:-1]
        combo=sorted(combo.split('/'))
        combo='('+'/'.join(combo)+')'
        out.write(str(nodeID)+'\tpp\t'+'TG'+combo+'\n')
        nodeID+=1
        TGList.append(combo)
out.close()
print(len(DGList),len(listofchains),len(set(TGList)))
trio = open('listcheck/trio-raw.txt','w')
for i in TGList:
    trio.write(i)
    trio.write('\n')
trio.close()
######
print('Network Multiplicated')
print('results in Multiplied.sif')


