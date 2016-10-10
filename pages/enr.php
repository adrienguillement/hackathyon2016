<?php

include("../include/header.php");
include '../class/kernel/Connection.php';
use Kernel\Connection;

$connect=new Connection();



if(!isset($_POST['building']) && !isset($_POST['surfacePP'])){
    $categorie = $connect->request('SELECT * from correspondance_PDL');
    echo '<form action="./enr.php" method="POST">
    <label>Rechercher un bâtiment </label><input type="text" id="realtxt" onkeyup="javascript:searchSel();"/>
    <select id="realitems" name="building">
    <option value="test">- - -</option>';
    for($i=0;$i<sizeof($categorie);$i++)
    {
        if($categorie[$i][2]=='null')
        {
            echo '<option name="'.$categorie[$i][0].'" value="'.$categorie[$i][0].'">'.$categorie[$i][0].'</option>';
        }
        else
        {
            echo '<option name="'.$categorie[$i][0].'" value="'.$categorie[$i][0].'">'.$categorie[$i][2].'</option>';
        }
    }
    echo "</select>
    <button type=\"submit\">GO!</button>
    </form>";
}
else
{
    //Calcul des couts

    $html='<form action="./enr.php" method="POST">
            <label>Sélectionner la surface de panneaux photovoltaïques :</label>
            <input type="text" name="surfacePP" /></br>
            <label>Sélectionner le nombres d\'éoliennes :</label>
            <input type="text" name="nbEolienne" /></br>
            <input type="hidden" name="building" value="'.$_POST['building'].'"/></br>
            <button type="submit">Calculer</button>
           </form>';
    echo $html;
    if(isset($_POST["surfacePP"])) {
        $metre = $_POST["surfacePP"];
        $eolienne = $_POST["nbEolienne"];
        $coutPanneau = 800 * $metre;
        $coutEolienne = 10000 * $eolienne;
        $totalCout = $coutEolienne + $coutPanneau;
        $html = '<br><table class="table table-striped table-hover ">';
        $html .= '<thead>';
        $html .= '<th>Coût installation panneaux photovoltaïque</th><th>Coût installation éoliennes</th><th>Investissement total</th><th>Coût kWh</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td>' . $coutPanneau . ' €</td>';
        $html .= '<td>' . $coutEolienne . ' €</td>';
        $html .= '<td>' . $totalCout . ' €</td>';
        $html .= '<td>0,15 €</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        echo $html;

        $consoKWH = $connect->request('SELECT kwh from conso where numBat = "' . $_POST['building'] . '"');


        $prix = intval($consoKWH[0][0]) * 0.15;
        $html = '<table class="table table-striped table-hover ">';
        $html .= '<thead>';
        $html .= '<th>Consommation du bâtiment du 1/12/2015 au 30/09/2016</th><th>Coût avant installation d\'ENR</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td>' . $consoKWH[0][0] . ' kWh</td><td>' . $prix . ' €</td>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        echo $html;

        echo 'Production d\'une éolienne : 150kWh par mois' . '<br>' . ' Panneau : 10 kWh';
        $consoFinale = $consoKWH[0][0] - ($eolienne * 1500 + $metre * 100);
        $prix = intval($consoKWH[0][0]) * 0.15;
        $consoFinalePrix = $consoFinale * 0.15;
        $html = '<table class="table table-striped table-hover ">';
        $html .= '<thead>';
        $html .= '<th>Consommation theorique avec cette installation</th>';
        if ($consoFinale > 0) {
            $html .= '<th>Coût théorique après installation</th>';
        } else {
            $html .= '<th>Gain théorique après installation</th>';
        }

        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<td>' . $consoFinale . ' kWh</td>';
        if ($consoFinale > 0) {
            $html .= '<td>' . $consoFinalePrix . ' €</td>';
        } else {
            $html .= '<td>' . -$consoFinalePrix . ' €</td>';
        }

        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        echo $html;

        $compt = 0;
        $economie = $prix - $consoFinalePrix;
        $somme = 0;

        while ($somme < $totalCout) {
            $somme += $economie;
            $compt++;
        }
        echo round($compt) . ' années de 10 mois <br>';
        $moisTotal = ($compt * 10) / 12;
        echo round($moisTotal) . ' années';
    }


}
//calcul de consommation pour chaque batiment en kW
$connect = new Connection();
include("../include/footer.php");