<?php
include('../db.php'); // Ensure this path is correct
$db->set_charset("utf8mb4");
$message = "";

// Handle INSERT request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'insert') {
    try {
        // Code that may throw an exception
        $occasion_id = $_POST['occasion'];
        $shakha_id = $_POST['shakha_id'];
       
        $query = "SELECT occasion, batch, occasion_key FROM occasions WHERE id = '$occasion_id'";
        $result = mysqli_query($db, $query);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $result_row = mysqli_fetch_assoc($result);
                $occasion = $result_row['occasion'];
                $batch = $result_row['batch'];
                $occasion_key = $result_row['occasion_key'];
                $occasion_quota_key = $occasion_key.$shakha_id;

                $query_check = "SELECT * FROM occasion_quota WHERE occasion_quota_key = '$occasion_quota_key'";
                $result_check = mysqli_query($db, $query_check);
                if (mysqli_num_rows($result_check) > 0) {
                    throw new Exception("Error: Duplicate entry for occasion_quota_key.");
                }

                $query = "INSERT INTO occasion_quota (occasion, occasion_id, occasion_quota_key, batch, shakha_id, bandhu_count, bhagini_count, total) VALUES ('$occasion', '$occasion_id', '$occasion_quota_key', '$batch', '$shakha_id', '$bandhu_count', '$bhagini_count', '$total_count')";

                if (mysqli_query($db, $query)) {
                    $message = "Record inserted successfully.";
                } else {
                    throw new Exception("Error: " . mysqli_error($db));
                }

            } else {
                // Ocassion doesn't exist
                throw new Exception("Ocassion doesn't exist.");
            }
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
    
}

// Handle update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    try {
        $id = $_POST['quota_id'];
        $bandhu_count = $_POST['bandhu_count'];
        $bhagini_count = $_POST['bhagini_count'];
        $total_count = $bandhu_count + $bhagini_count;

        if ($total_count <= 0) {
            echo '<script>alert("Total quota should be greater than zero");</script>';
            echo '<script>window.location.href="quota.php";</script>';
            exit;
        }
        
        $query = "UPDATE occasion_quota SET bandhu_count='$bandhu_count', bhagini_count='$bhagini_count', total='$total_count' WHERE id='$id'";
        if (mysqli_query($db, $query)) {
            $message = "Record updated successfully.";
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
        
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
}

// Handle DELETE request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    try {
        $id = $_POST['id'];
        $query = "DELETE FROM occasion_quota WHERE id='$id'";

        if (mysqli_query($db, $query)) {
            $message = "Record deleted successfully.";
        } else {
            throw new Exception("Error: " . mysqli_error($db));
        }
    } catch (Exception $e) {
        // Code that runs if an exception is caught
        echo $e->getMessage(), "\n";
    }
    
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shakha Management</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link rel="icon" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/apple-touch-icon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="dropdownassets/select2.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">

    <!-- jQuery (load without defer to ensure availability for plugins) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Marathi Input Scripts -->
    <script src="https://cdn.rawgit.com/wikimedia/jquery.ime/master/dist/jquery.ime.js" defer></script>
    <script src="https://cdn.rawgit.com/wikimedia/jquery.ime/master/dist/jquery.ime.inputmethods.js" defer></script>

    <!-- Select2 JS -->
    <script src="dropdownassets/select2.min.js" defer></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js" defer></script>

    <style>
        /* Consolidated styling for the body and layout */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        header {
            background: #ff9900;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        form, table, .btn, .modal {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }
        /* Additional consolidated styles */
        .center {
            text-align: center;
            color: #800000;
            font-size: 24px;
            font-weight: bold;
        }
        .content, .message, .signature {
            max-width: 800px;
            margin: auto;
            background-color: #fff8e1;
            border: 2px solid #b8860b;
            padding: 20px;
            border-radius: 10px;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#marathiInput').ime();
            $('#marathiInput').ime('select', 'mr-transliteration');
        });
    </script>
</head>



<body>

  <!-- ======================== Header ======================== -->
  <?php include('header.php'); ?>
  <!------====================== End Header ==============------->

  <!--------------------- Sidebar --------------------------------->
  <!--------------------- End Sidebar ----------------------------->


    <div class="container">
        <?php if ($message): ?>
            <div class="message <?= strpos($message, 'Error') !== false ? 'error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <br>
        <h2>Occasion Quota Report</h2>
        <br>
        <!-- <form class="row g-3" action="" method="POST" accept-charset="UTF-8"> -->
            <!-- <div class="col-md-4"> 

            </div> -->

            <!-- <input type="hidden" name="action" value="insert"> -->

            <div class="col-md-4"> 
                <label for="occasion">Occasion name:</label>
                <select name="occasion" class="form-control" onchange="filterTable()" required>
                    <option value="">Please Select</option>
                    <?php 
                        $query_utsav = "SELECT MIN(id) as id, occasion, batch
                            FROM occasion_quota
                            GROUP BY occasion, batch;
                        ";
                        $result_utsav = mysqli_query($db, $query_utsav);
                        while ($row_utsav = mysqli_fetch_assoc($result_utsav)) {
                            // $displayValue = htmlspecialchars($row_utsav['occasion']);
                            // if (!empty($row_utsav['batch'])) {
                            //     $displayValue .= ' B - ' . htmlspecialchars($row_utsav['batch']);
                            // }
                            echo '<option value="' . htmlspecialchars($row_utsav['id']) . '">' . $row_utsav['occasion'] . '</option>';
                        }
                    ?>
                </select>
            </div>

            <div class="col-md-4"> 
                <label for="shakha">Shakha</label>
                <input type="text" name="shakha" class="form-control" list="shakha" id="branchInput" oninput="filterTable()" required />
                <datalist id="shakha">
                    <?php 
                        $query_shakha = "SELECT * FROM mandir_branch";
                        $result_shakha = mysqli_query($db, $query_shakha);
                        while ($row_shakha = mysqli_fetch_assoc($result_shakha)) {
                            $unique_code = htmlspecialchars($row_shakha['unique_code']);
                            echo '<option value="' . htmlspecialchars($row_shakha['shakha']) . '" data-unique="' . $unique_code . '">' . $unique_code . '</option>';
                        }
                    ?>
                </datalist>
            </div>

            <!-- Include Select2 JS -->
            <script src="dropdownassets/select2.min.js"></script>

            
                <script>
                    $(document).ready(function() {
                        $('#shakhaSelect').select2({
                            placeholder: "Select an option",
                            allowClear: true
                        });
                    });
            </script>

            <!-- <script>
                function filterTable() {
                    const occasionFilter = document.querySelector("select[name='occasion']").value; // Get the selected occasion ID
                    const shakhaFilter = document.getElementById("branchInput").value.toLowerCase(); // Get the shakha input
                    const table = document.getElementById("recordsTable");
                    const rows = table.getElementsByTagName("tr");

                    for (let i = 1; i < rows.length; i++) { // Skip the header row
                        const cells = rows[i].getElementsByTagName("td");
                        const occasionMatch = (occasionFilter === "" || cells[1].textContent.includes(occasionFilter)); // Check if the occasion matches
                        const shakhaMatch = shakhaFilter === "" || cells[3].textContent.toLowerCase().includes(shakhaFilter); // Check if the shakha matches
                        rows[i].style.display = occasionMatch && shakhaMatch ? "" : "none"; // Show or hide the row
                    }
                }
            </script> -->

            <!-- <input type="submit" value="Insert"> -->
        <!-- </form> -->

        <h2>Occasion records</h2>
        <table id="recordsTable" class="table datatable">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Occasion</th>
                    <th scope="col">Batch</th>
                    <th scope="col">Shakha</th>
                    <th scope="col">Bandhu Count</th>
                    <th scope="col">Bhagini Count</th>
                    <th scope="col">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    // Fetch existing records
                    $query = "SELECT occasion_quota.*, mandir_branch.shakha
                                FROM occasion_quota
                                JOIN mandir_branch ON occasion_quota.shakha_id = mandir_branch.unique_code;";
                    $result = mysqli_query($db, $query);
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $i = $i + 1;
                ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= htmlspecialchars($row['occasion']) ?></td>
                        <td><?= $row['batch'] ?  htmlspecialchars($row['batch']) : "" ?></td>
                        <td><?= htmlspecialchars($row['shakha']) ?></td>
                        <td><?= htmlspecialchars($row['bandhu_count']) ?></td>
                        <td><?= htmlspecialchars($row['bhagini_count']) ?></td>
                        <td><?= htmlspecialchars($row['total']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>

        // Define the filterTable function
        function filterTable() {
            const occasionFilter = document.querySelector("select[name='occasion']").value; // Get the selected occasion ID
            const shakhaFilter = document.getElementById("branchInput").value.toLowerCase(); // Get the shakha input
            const table = document.getElementById("recordsTable");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) { // Skip the header row
                const cells = rows[i].getElementsByTagName("td");
                const occasionMatch = (occasionFilter === "" || cells[1].textContent === occasionMap[occasionFilter]); // Check if the occasion matches
                const shakhaMatch = shakhaFilter === "" || cells[3].textContent.toLowerCase().includes(shakhaFilter); // Check if the shakha matches
                rows[i].style.display = occasionMatch && shakhaMatch ? "" : "none"; // Show or hide the row
            }
        }

        // $(document).ready(function () {
        //     // Initialize DataTable
        //     $('#recordsTable').DataTable();

        //     // Attach event listeners
        //     $('#occasionSelect').on('change', filterTable);
        //     $('#branchInput').on('input', filterTable);
        // });

        // Store occasion data for easy lookup
        const occasionMap = {};

        // function filterTable() {
        //     const occasionFilter = document.querySelector("select[name='occasion']").value; // Get the selected occasion ID
        //     const shakhaFilter = document.getElementById("branchInput").value.toLowerCase(); // Get the shakha input
        //     const table = document.getElementById("recordsTable");
        //     const rows = table.getElementsByTagName("tr");

        //     for (let i = 1; i < rows.length; i++) { // Skip the header row
        //         const cells = rows[i].getElementsByTagName("td");
        //         const occasionMatch = (occasionFilter === "" || cells[1].textContent === occasionMap[occasionFilter]); // Check if the occasion matches
        //         const shakhaMatch = shakhaFilter === "" || cells[3].textContent.toLowerCase().includes(shakhaFilter); // Check if the shakha matches
        //         rows[i].style.display = occasionMatch && shakhaMatch ? "" : "none"; // Show or hide the row
        //     }
        // }

        // Populate the occasion map on page load
        document.addEventListener('DOMContentLoaded', () => {
            const occasionSelect = document.querySelector("select[name='occasion']");
            occasionSelect.querySelectorAll('option').forEach(option => {
                const occasionName = option.text.split(' B - ')[0];
                occasionMap[option.value] = occasionName; // Store occasion ID as key and occasion name as value
            });
        });

        // document.addEventListener('DOMContentLoaded', () => {
        //     // Initialize DataTables
        //     const table = $('.datatable').DataTable();

        //     const occasionSelect = document.querySelector("select[name='occasion']");
        //     const branchInput = document.getElementById("branchInput");

        //     // Initialize occasionMap for easy lookup
        //     const occasionMap = {};

        //     // Populate the occasion map on page load
        //     occasionSelect.querySelectorAll('option').forEach(option => {
        //         const occasionName = option.text.split(' B - ')[0];
        //         occasionMap[option.value] = occasionName; // Store occasion ID as key and occasion name as value
        //     });

        //     // Populate the occasion map on page load
        //     occasionSelect.querySelectorAll('option').forEach(option => {
        //         const occasionName = option.text.split(' B - ')[0]; // Get only the part before " B - "
        //         occasionMap[option.value] = occasionName; // Store occasion ID as key and occasion name as value
        //     });

        //     // Filter the table using DataTables' API
        //     function filterTable() {
        //         const occasionFilter = occasionSelect.value; // Get the selected occasion ID
        //         const occasionName = occasionFilter ? occasionMap[occasionFilter] : '';
        //         const shakhaFilter = branchInput.value.toLowerCase(); // Get the shakha input

        //         // Custom filtering using DataTables' search functionality
        //         table.columns(1).search(occasionName).draw(); // Filter the Occasion column
        //         table.columns(3).search(shakhaFilter, true, false).draw(); // Filter the Shakha column
        //     }

        //     // Attach event listeners to trigger the filtering function
        //     occasionSelect.addEventListener('change', filterTable);
        //     branchInput.addEventListener('input', filterTable);
        // });

        // $(document).ready(function () {
        //     if ($.fn.DataTable.isDataTable('#recordsTable')) {
        //         $('#recordsTable').DataTable().clear().destroy();
        //     }

        //     const table = $('#recordsTable').DataTable();

        //     $('#occasionSelect, #branchInput').on('change input', function () {
        //         table.columns(1).search($('#occasionSelect option:selected').text()).draw();
        //         table.columns(3).search($('#branchInput').val()).draw();
        //     });
        // });

        // $(document).ready(function () {
        //     // Initialize DataTable
        //     const table = $('#recordsTable').DataTable();

        //     // Define filterTable function
        //     window.filterTable = function () {
        //         const occasionFilter = $("select[name='occasion']").val(); // Get the selected occasion ID
        //         const shakhaFilter = $("#branchInput").val().toLowerCase(); // Get the shakha input

        //         table.columns(1).search(occasionFilter ? occasionFilter : '', true, false).draw(); // Filter the Occasion column
        //         table.columns(3).search(shakhaFilter, true, false).draw(); // Filter the Shakha column
        //     };

        //     // Attach event listeners
        //     $('#occasionSelect, #branchInput').on('change input', filterTable);
        // });

    </script>

      <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.min.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

</body>
</html>
