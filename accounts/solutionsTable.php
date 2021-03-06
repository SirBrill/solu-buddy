<?php
session_start();

function displaySolutionMessage($isError=false){
    if($isError) $msg = "Error getting solutions.";
    else $msg = "No solutions saved."
?>
    <tbody>
        <tr>
            <td colspan="10">
                <?= $msg ?>
            </td>
        </tr>
    </tbody>
<?php
}
$_SESSION['solutions'] = 'true';
require ("soluMySQLConnect.php");
require ("../dynamicHelpers.php");
renderHead( ["title" => "Solutions Page", "navField1" => "Account Settings", "navField2" => "Saved Solutions",
	"navField3" => "Chemistry Terms", "navField4" => "Create Solution(s)"] );

$username = $_SESSION["username"];

if($statement = $dbc->prepare("SELECT ID FROM accounts WHERE Username = ?")){
    $statement->bind_param("s", $username);
    $statement->execute();
    $statement->bind_result($accountId);
    $statement->store_result();
    $numAccounts = $statement->num_rows;
    $statement->fetch();
    if($numAccounts > 1){
        echo "More than one account found for username.";
        $statement->close();
        exit;
    } else if($numAccounts == 0){
        echo "No account found for given username.";
        $statement->close();
        exit;
    }
    $statement->close();
} else {
	echo 'Could not run query: ' . $dbc->error;
	exit;
}
echo "<CENTER><h1>Solutions Made By $username</h1></CENTER>"
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="/static/css/bootstrap.css">
    <link href = "/static/css/table.css" type="text/css" rel = "stylesheet">
    <link href = "/static/css/header-styles.css" type="text/css" rel = "stylesheet">
    <link href = "/static/css/navBar.css" type="text/css" rel = "stylesheet">
    <script src="/static/js/jquery-1.11.3.min.js"></script>
    <script src="/static/js/bootstrap.js"></script>
    <style>
        .page {
            margin: 16px;
        }
        .tab-pane {
            margin-top: 16px;
        }
        .table-responsive {
            overflow-x: initial;
        }
    </style>
	<script>
        $('#savedSolutionsTabs a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
	</script>
</head>
<body id="bodySolutions">
<div class="page">
    <ul class="nav nav-tabs" role="tablist" id="savedSolutionsTabs">
        <li role="presentation" class="active"><a href="#singleSolutions" aria-controls="singleSolutions" role="tab" data-toggle="tab">Single Solutions</a></li>
        <li role="presentation"><a href="#serialSolutions" aria-controls="serialSolutions" role="tab" data-toggle="tab">Serial Dilution Solutions</a></li>
        <li role="presentation"><a href="#calibrations" aria-controls="calibrations" role="tab" data-toggle="tab">Calibration Standards</a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="singleSolutions">
            <div class="panel panel-default">
                <div class="panel-heading">Single Solution From Solid</div>
                <div class="table-responsive">
                    <table id="singleSolidSolutionsTable" align="center" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Solvent Formula</th>
                            <th>Solute Formula</th>
                            <th>Solute Molecular Weight</th>
                            <th>Solution Total Volume</th>
                            <th>Solution Concentration</th>
                            <th>Mass of Solute to Add</th>
                            <th>DELETE</th>
                        </tr>
                        </thead>
                        <?php
                        if($statement = $dbc->prepare("SELECT ID, Solvent_Identity, Solute_Identity, Solute_Weight, Solution_Total_Volume, Solution_Concentration, Mass_Solute_Add FROM single_solution_solid WHERE Account_ID = ?")){
                            $statement->bind_param("i", $accountId);
                            $statement->execute();
                            $statement->bind_result($id, $solventIdentity, $soluteIdentity, $soluteWeight, $solutionTotalVolume, $solutionConcentration, $massToAdd);
                            $statement->store_result();
                            $numRows = $statement->num_rows;
                            //Creates a loop to interate through results
                            while ($statement->fetch()){
                        ?>
                        <tbody>
                        <tr>
                            <td><?= $solventIdentity ?></td>
                            <td><?= $soluteIdentity ?></td>
                            <td><?= $soluteWeight ?> g/mol</td>
                            <td><?= $solutionTotalVolume ?> mL</td>
                            <td><?= $solutionConcentration ?> mol/L</td>
                            <td><?= $massToAdd ?> g</td>
                            <td><a href="deleteSolution.php?ID=<?= $id ?>&t=single_sol_solid">Delete</a></td>
                        </tr>
                        </tbody>
                        <?php
                            }
                            if(!$numRows){
                                displaySolutionMessage();
                            }
                            $statement->close();
                        }
                        else {
                            displaySolutionMessage(true);
                        }
                    ?>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Single Solution From Liquid (Gravimetrically)</div>
                <div class="table-responsive">
                    <table id="singleLiquidGravSolutionsTable" align="center" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Solvent Formula</th>
                            <th>Solute Formula</th>
                            <th>Solute Molecular Weight</th>
                            <th>Solution Total Volume</th>
                            <th>Solution Concentration</th>
                            <th>Mass of Solute to Add</th>
                            <th>DELETE</th>
                        </tr>
                        </thead>
                        <?php
                        if($statement = $dbc->prepare("SELECT ID, Solvent_Identity, Solute_Identity, Solute_Weight, Solution_Total_Volume, Solution_Concentration, Mass_Solute_Add FROM single_solution_liquid_grav WHERE Account_ID = ?")){
                            $statement->bind_param("i", $accountId);
                            $statement->execute();
                            $statement->bind_result($id, $solventIdentity, $soluteIdentity, $soluteWeight, $solutionTotalVolume, $solutionConcentration, $massToAdd);
                            $statement->store_result();
                            $numRows = $statement->num_rows;
                            //Creates a loop to interate through results
                            while ($statement->fetch()){
                        ?>
                        <tbody>
                        <tr>
                            <td><?= $solventIdentity ?></td>
                            <td><?= $soluteIdentity ?></td>
                            <td><?= $soluteWeight ?> g/mol</td>
                            <td><?= $solutionTotalVolume ?> mL</td>
                            <td><?= $solutionConcentration ?> mol/L</td>
                            <td><?= $massToAdd ?> g</td>
                            <td><a href="deleteSolution.php?ID=<?= $id ?>&t=single_sol_liq_grav">Delete</a></td>
                        </tr>
                        </tbody>
                        <?php
                            }
                            if(!$numRows){
                                displaySolutionMessage();
                            }
                            $statement->close();
                        }
                        else{
                            displaySolutionMessage(true);
                        }
                    ?>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Single Solution From Liquid (Volumetrically)</div>
                <div class="table-responsive">
                    <table id="singleLiquidVolSolutionsTable" align="center" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Solvent Formula</th>
                            <th>Solute Formula</th>
                            <th>Solute Molecular Weight</th>
                            <th>Solute Density</th>
                            <th>Solution Total Volume</th>
                            <th>Solution Concentration</th>
                            <th>Mass of Solute to Add</th>
                            <th>DELETE</th>
                        </tr>
                        </thead>
                        <?php
                        if($statement = $dbc->prepare("SELECT ID, Solvent_Identity, Solute_Identity, Solute_Weight, Solute_Density, Solution_Total_Volume, Solution_Concentration, Volume_Solute_Add FROM single_solution_liquid_vol WHERE Account_ID = ?")){
                            $statement->bind_param("i", $accountId);
                            $statement->execute();
                            $statement->bind_result($id, $solventIdentity, $soluteIdentity, $soluteWeight, $soluteDensity, $solutionTotalVolume, $solutionConcentration, $volumeToAdd);
                            $statement->store_result();
                            $numRows = $statement->num_rows;
                            //Creates a loop to interate through results
                            while($statement->fetch()){
                        ?>
                        <tbody>
                        <tr>
                            <td><?= $solventIdentity ?></td>
                            <td><?= $soluteIdentity ?></td>
                            <td><?= $soluteWeight ?> g/mol</td>
                            <td><?= $soluteDensity ?> g/mL</td>
                            <td><?= $solutionTotalVolume ?> mL</td>
                            <td><?= $solutionConcentration ?> mol/L</td>
                            <td><?= $volumeToAdd ?> mL</td>
                            <td><a href="deleteSolution.php?ID=<?= $id ?>&t=single_sol_liq_vol">Delete</a></td>
                        </tr>
                        </tbody>
                        <?php
                            }
                            if(!$numRows){
                                displaySolutionMessage();
                            }
                            $statement->close();
                        }else {
                            displaySolutionMessage(true);
                        }
                    ?>
                    </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="serialSolutions">
            <div class="panel panel-default">
                <div class="panel-heading">Serial Dilutions</div>
                <div class="table-responsive">
                <table id="serialSolutionsTable" align="center" class="table table-hover">
                    <thead>
                    <tr>
                        <th>Solvent Formula</th>
                        <th>Solute Formula</th>
                        <th>Original Solution Molarity</th>
                        <th>Dilution Flask Volume</th>
                        <th>Number of Flasks</th>
                        <th>Volume to Transfer</th>
                        <th>DELETE</th>
                    </tr>
                    </thead>
                    <?php
                    if($statement = $dbc->prepare("SELECT ID, Solvent_Identity, Solute_Identity, Solution_Molarity, Dilution_Flask_Volume, Number_Flasks, Volume_Transfer FROM serial_dilution WHERE Account_ID = ?")) {
                        $statement->bind_param("i", $accountId);
                        $statement->execute();
                        $statement->bind_result($id, $solventIdentity, $soluteIdentity, $solutionMolarity, $dilutionFlaskVolume, $numberFlasks, $volumeTransfer);
                        $statement->store_result();
                        $numRows = $statement->num_rows;
                        //Creates a loop to interate through results
                        while ($statement->fetch()) {
                            ?>
                            <tbody>
                            <tr>
                                <td><?= $solventIdentity ?></td>
                                <td><?= $soluteIdentity ?></td>
                                <td><?= $solutionMolarity ?> g/mol</td>
                                <td><?= $dilutionFlaskVolume ?> mL</td>
                                <td><?= $numberFlasks ?></td>
                                <td><?= $volumeTransfer ?> mL</td>
                                <td><a href="deleteSolution.php?ID=<?= $id ?>&t=serial">Delete</a></td>
                            </tr>
                            </tbody>
                            <?php
                        }
                        if(!$numRows){
                            displaySolutionMessage();
                        }
                        $statement->close();
                    }else{
                        displaySolutionMessage(true);
                    }
                    ?>
                </table>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="calibrations">
            <div class="panel panel-default">
                <div class="panel-heading">External Calibrations</div>
                <div class="table-responsive">
                    <table id="calibrationExternalTable" align="center" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Solvent Formula</th>
                            <th>Analyte Formula</th>
                            <th>Analyte Molecular Weight</th>
                            <th>Analyte Molarity</th>
                            <th>Number of Standards</th>
                            <th>Flask Volumes</th>
                            <th>DELETE</th>
                        </tr>
                        </thead>
                        <?php
                        if($statement = $dbc->prepare("SELECT ID, Solvent_Identity, Analyte_Identity, Analyte_Weight, Number_Standards, Flask_Volumes, Analyte_Molarity FROM calibration_external WHERE Account_ID = ?")){
                            $statement->bind_param("i", $accountId);
                            $statement->execute();
                            $statement->bind_result($id, $solventIdentity, $analyteIdentity, $analyteWeight, $numStandards, $flaskVolumes, $analyteMolarity);
                            $statement->store_result();
                            $numRows = $statement->num_rows;
                            //Creates a loop to interate through results
                            while ($statement->fetch()){
                        ?>
                        <tbody>
                        <tr>
                            <td><?= $solventIdentity ?></td>
                            <td><?= $analyteIdentity ?></td>
                            <td><?= $analyteWeight ?> g/mol</td>
                            <td><?= $analyteMolarity ?> M</td>
                            <td><?= $numStandards ?></td>
                            <td><?= $flaskVolumes ?> mL</td>
                            <td><a href="deleteSolution.php?ID=<?= $id ?>&t=calibration_ext">Delete</a></td>
                        </tr>
                        </tbody>
                        <?php
                            }
                            if(!$numRows){
                                displaySolutionMessage();
                            }
                            $statement->close();
                        }else{
                            displaySolutionMessage(true);
                        }
                    ?>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Standard Addition Calibrations</div>
                <div class="table-responsive">
                    <table id="calibrationAdditionTable" align="center" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Analyte Formula</th>
                            <th>Analyte Molarity</th>
                            <th>Analyte Molecular Weight</th>
                            <th>Unknown Name</th>
                            <th>Unknown Volume</th>
                            <th>Number of Standards</th>
                            <th>Flask Volumes</th>
                            <th>DELETE</th>
                        </tr>
                        </thead>
                        <?php
                        if($statement = $dbc->prepare("SELECT ID, Analyte_Identity, Analyte_Molarity, Unknown_Name, Number_Standards, Flask_Volumes, Unknown_Volume FROM calibration_addition WHERE Account_ID = ?")){
                            $statement->bind_param("i", $accountId);
                            $statement->execute();
                            $statement->bind_result($id, $analyteIdentity, $analyteMolarity, $unknownName, $numStandards, $flaskVolumes, $unknownVolume);
                            $statement->store_result();
                            $numRows = $statement->num_rows;
                            //Creates a loop to interate through results
                            while ($statement->fetch()){
                        ?>
                        <tbody>
                        <tr>
                            <td><?= $analyteIdentity ?></td>
                            <td><?= $analyteMolarity ?> M</td>
                            <td><?= $analyteWeight ?> g/mol</td>
                            <td><?= $unknownName ?></td>
                            <td><?= $unknownVolume ?> mL</td>
                            <td><?= $numStandards ?></td>
                            <td><?= $flaskVolumes ?> mL</td>
                            <td><a href="deleteSolution.php?ID=<?= $id ?>&t=calibration_add">Delete</a></td>
                        </tr>
                        </tbody>
                        <?php
                            }
                            if(!$numRows){
                                displaySolutionMessage();
                            }
                            $statement->close();
                        }else{
                                displaySolutionMessage(true);
                        }
                    ?>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">Internal Calibrations</div>
                <div class="table-responsive">
                    <table id="calibrationInternalTable" align="center" class="table table-hover">
                        <thead>
                        <tr>
                            <th>Analyte Name</th>
                            <th>Analyte Molarity</th>
                            <th>Internal Standard Solution Name</th>
                            <th>Internal Standard Solution Molarity</th>
                            <th>Number of Standards</th>
                            <th>Flask Volumes</th>
                            <th>DELETE</th>
                        </tr>
                        </thead>
                        <?php
                        if($statement = $dbc->prepare("SELECT ID, Analyte_Identity, Analyte_Molarity, Internal_Standard_Solution_Identity, Internal_Molarity, Number_Standards, Flask_Volumes FROM calibration_internal WHERE Account_ID = ?")){
                            $statement->bind_param("i", $accountId);
                            $statement->execute();
                            $statement->bind_result($id, $analyteIdentity, $analyteMolarity, $internalIdentity, $internalMolarity, $numStandards, $flaskVolumes);
                            $statement->store_result();
                            $numRows = $statement->num_rows;
                            //Creates a loop to interate through results
                            while ($statement->fetch()){
                        ?>
                        <tbody>
                        <tr>
                            <td><?= $analyteIdentity ?></td>
                            <td><?= $analyteMolarity ?> M</td>
                            <td><?= $internalIdentity ?></td>
                            <td><?= $internalMolarity ?> M</td>
                            <td><?= $numStandards ?></td>
                            <td><?= $flaskVolumes ?> mL</td>
                            <td><a href="deleteSolution.php?ID=<?= $id ?>&t=calibration_int">Delete</a></td>
                        </tr>
                        </tbody>
                        <?php
                            }
                            if(!$numRows){
                                displaySolutionMessage();
                            }
                            $statement->close();
                        }
                        else{
                            displaySolutionMessage(true);
                        }
                        $dbc->close();
                    ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include('../footer.html');
?>
</body>
</html>

