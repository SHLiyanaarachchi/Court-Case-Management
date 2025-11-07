<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check if case_no is provided
if (!isset($_GET['case_no']) || empty($_GET['case_no'])) {
    header('Location: dashboard.php');
    exit();
}

$case_no = $_GET['case_no'];
$success = '';
$error = '';

// Fetch case details
$stmt = $conn->prepare("SELECT * FROM cases WHERE case_no = ?");
$stmt->bind_param("s", $case_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: dashboard.php');
    exit();
}

$case = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Types = trim($_POST['Types'] ?? '');
    $Case_No = trim($_POST['Case_No'] ?? '');
    $Province = trim($_POST['Province'] ?? '');
    $District = trim($_POST['District'] ?? '');
    $Filed_Date = trim($_POST['Filed_Date'] ?? '');
    $Court = trim($_POST['Court'] ?? '');
    $Category_Cause_of_Action = trim($_POST['Category_Cause_of_Action'] ?? '');
    $Name_Address = trim($_POST['Name_Address'] ?? '');
    $Terminated = trim($_POST['Terminated'] ?? '');
    $Next_Date = trim($_POST['Next_Date'] ?? '');
    $Remarks = trim($_POST['Remarks'] ?? '');
    $Last_Date = trim($_POST['Last_Date'] ?? '');

    // Validation
    if (
        empty($Types) || empty($Case_No) || empty($Province) ||
        empty($District) || empty($Filed_Date) || empty($Court) ||
        empty($Category_Cause_of_Action) || empty($Name_Address) ||
        empty($Terminated) || empty($Next_Date) || empty($Remarks) || empty($Last_Date)
    ) {
        $error = 'Please fill in all required fields';
    } else {
        // Update case
        $update_stmt = $conn->prepare("
            UPDATE cases SET 
                Types = ?, 
                Case_No = ?, 
                Province = ?, 
                District = ?, 
                Filed_Date = ?, 
                Court = ?, 
                Category_Cause_of_Action = ?, 
                Name_Address = ?, 
                Terminated = ?, 
                Next_Date = ?, 
                Remarks = ?, 
                Last_Date = ? 
            WHERE case_no = ?
        ");

        $update_stmt->bind_param(
            "sssssssssssss",
            $Types, $Case_No, $Province, $District, $Filed_Date,
            $Court, $Category_Cause_of_Action, $Name_Address,
            $Terminated, $Next_Date, $Remarks, $Last_Date, $case_no
        );

        if ($update_stmt->execute()) {
            $success = 'Case updated successfully!';
            // Refresh data in form
            $case = [
                'Types' => $Types,
                'Case_No' => $Case_No,
                'Province' => $Province,
                'District' => $District,
                'Filed_Date' => $Filed_Date,
                'Court' => $Court,
                'Category_Cause_of_Action' => $Category_Cause_of_Action,
                'Name_Address' => $Name_Address,
                'Terminated' => $Terminated,
                'Next_Date' => $Next_Date,
                'Remarks' => $Remarks,
                'Last_Date' => $Last_Date
            ];
        } else {
            $error = 'Error updating case: ' . $conn->error;
        }
        $update_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Case - Court Case Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef2f3;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background: #203a43;
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-back {
            background: #667eea;
            padding: 10px 20px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
        }
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #1e3c72;
            margin-bottom: 10px;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-left: 5px solid #28a745;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-left: 5px solid #dc3545;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        input, select, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-actions {
            margin-top: 25px;
            display: flex;
            gap: 15px;
        }
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit {
            background: #f093fb;
            color: white;
        }
        .btn-cancel {
            background: #aaa;
            color: white;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div>⚖️ Court Case Management</div>
        <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
    </nav>

    <div class="container">
        <h2>✏️ Edit Case</h2>
        <p>Case ID: <strong><?php echo htmlspecialchars($case['case_no']); ?></strong></p>

        <?php if ($success): ?>
            <div class="success-message">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message">✗ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="Types">Types *</label>
                    <select id="Types" name="Types" required>
                        <option value="">Select Type</option>
                        <option value="Filed by the CEA" <?php echo ($case['Types'] === 'Filed by the CEA') ? 'selected' : ''; ?>>Filed by the CEA</option>
                        <option value="Filed against the CEA - Pending" <?php echo ($case['Types'] === 'Filed against the CEA - Pending') ? 'selected' : ''; ?>>Filed against the CEA - Pending</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Case_No">Case No *</label>
                    <input type="text" id="Case_No" name="Case_No" value="<?php echo htmlspecialchars($case['Case_No']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Name_Address">Name Address *</label>
                    <input type="text" id="Name_Address" name="Name_Address" value="<?php echo htmlspecialchars($case['Name_Address']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Province">Province *</label>
                    <input type="text" id="Province" name="Province" value="<?php echo htmlspecialchars($case['Province']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="District">District *</label>
                    <input type="text" id="District" name="District" value="<?php echo htmlspecialchars($case['District']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Filed_Date">Filed Date *</label>
                    <input type="date" id="Filed_Date" name="Filed_Date" value="<?php echo htmlspecialchars($case['Filed_Date']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Court">Court *</label>
                    <input type="text" id="Court" name="Court" value="<?php echo htmlspecialchars($case['Court']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Category_Cause_of_Action">Category/Cause of Action *</label>
                    <input type="text" id="Category_Cause_of_Action" name="Category_Cause_of_Action" value="<?php echo htmlspecialchars($case['Category_Cause_of_Action']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="Next_Date">Next Date *</label>
                    <input type="date" id="Next_Date" name="Next_Date" value="<?php echo htmlspecialchars($case['Next_Date']); ?>" required>
                </div>

                <div class="form-group full-width">
                    <label for="Terminated">Terminated *</label>
                    <textarea id="Terminated" name="Terminated" required><?php echo htmlspecialchars($case['Terminated']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="Remarks">Remarks *</label>
                    <textarea id="Remarks" name="Remarks" required><?php echo htmlspecialchars($case['Remarks']); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label for="Last_Date">Last Date *</label>
                    <input type="date" id="Last_Date" name="Last_Date" value="<?php echo htmlspecialchars($case['Last_Date']); ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-submit">💾 Update Case</button>
                <a href="dashboard.php" class="btn btn-cancel">✕ Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
