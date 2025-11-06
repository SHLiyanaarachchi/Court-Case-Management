<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check if case_no is provided
if (!isset($_GET['case_id']) || empty($_GET['case_no'])) {
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
    $Case_No= trim($_POST['Case_No'] ?? '');
    $Province = trim($_POST['Province'] ?? '');
    $District = trim($_POST['District'] ?? '');
    $Filed_Date = trim($_POST['Filed_Date'] ?? '');
    $Court = trim($_POST['Court'] ?? '');
    $Category_Cause_of_Action = trim($_POST['Category_Cause_of_Action'] ?? '');
    $Name_Address = trim($_POST['Name_Address'] ?? '');
    $Terminated = trim($_POST['Terminated'] ?? '');
    $Next_Date = trim($_POST['Next_Date'] ?? '');
    $Remarks = $trim($_POST['Remarks'] ?? '');
    $Last_Date = $trim($_post['Last_Date'] ?? '');


    // Validation
    if (empty($Types) || empty($Case_No) || empty($Province) ||
        empty($District) || empty($Filed_Date) || empty($Court) || empty($Category_Cause_of_Action)|| empty($Name_Address)|| empty($Terminated)|| empty($Next_Date)|| empty($Remarks)|| empty($Last_Date)) {
        $error = 'Please fill in all required fields';
    } else {
        // Update case
        $update_stmt = $conn->prepare("UPDATE cases SET Types = ?, Case_No = ?, Province = ?, District = ?, Filed_Date = ?, Court = ?, Category_Cause_of_Action = ?, Name_Address = ?, Terminated= ?, Next_Date = ?,Remarks = ?,Last_Date = ?, WHERE case_no= ?");

        $Next_Date_value = !empty($Next_Date) ? $Next_Date : null;

        $update_stmt->bind_param("sssssssssss", $Types, $Case_No, $Province, $District, $Filed_Date, $Court, $Category_Cause_of_Action, $Name_Address, $Terminated, $next_hearing_date_value, $Remarks,$Last_Date);

        if ($update_stmt->execute()) {
            $success = 'Case updated successfully!';
            // Refresh case data
            $case['Types'] = $Types;
            $case['Case_No'] = $Case_No;
            $case['Province'] = $Province;
            $case['Districte'] = $District;
            $case['Filed_Date'] = $Filed_Date;
            $case['Court'] = $Court;
            $case['Category_Cause_of_Action'] = $Category_Cause_of_Action;
            $case['Name_Address'] = $Name_Address;
            $case['Terminated'] = $Terminated;
            $case['Next_Date'] = $Next_Date_value;
            $case['Remarks'] = $Remarks;
            $case['Last_Date'] = $Last_Date;
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('https://images.pexels.com/photos/5668772/pexels-photo-5668772.jpeg?auto=compress&cs=tinysrgb&w=1920');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            opacity: 0.08;
            z-index: 0;
        }

        .navbar {
            background: rgba(15, 32, 39, 0.95);
            backdrop-filter: blur(10px);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 20px;
            color: white;
        }

        .btn-back {
            padding: 10px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .container {
            max-width: 1000px;
            margin: 32px auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 3px solid #f0f0f0;
        }

        .form-header h2 {
            color: #1e3c72;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #666;
            font-size: 14px;
        }

        .case-id-badge {
            display: inline-block;
            padding: 8px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 8px;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            border-left: 4px solid #28a745;
            animation: slideIn 0.5s ease;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 24px;
            border-left: 4px solid #c33;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        label .required {
            color: #e74c3c;
        }

        input[type="text"],
        input[type="date"],
        select,
        textarea {
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 14px 32px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 15px;
            flex: 1;
        }

        .btn-submit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #8e9eab 0%, #eef2f3 100%);
            color: #333;
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(142, 158, 171, 0.4);
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 16px;
            }

            .form-container {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            ⚖️ Court Case Management
        </div>
        <div class="navbar-user">
            <a href="dashboard.php?case_no=<?php echo urlencode($case_no); ?>" class="btn-back">← Back to Case</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>✏️ Edit Case</h2>
                <p>Update case information</p>
                <span class="case-id-badge">Case ID: <?php echo htmlspecialchars($case['case_no)']); ?></span>
            </div>

            <?php if ($success): ?>
                <div class="success-message">✓ <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message">✗ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="Types">Types <span class="required">*</span></label>
                        <select id="Types" name="Types" required>
                            <option value="">Select Type</option>
                            <option value="Filed by the CEA" <?php echo ($case['Types'] === 'Filed by the CEA') ? 'selected' : ''; ?>>Filed by the CEA</option>
                            <option value="Filed against the CEA - Pending" <?php echo ($case['Types'] === 'Filed against the CEA - Pending') ? 'selected' : ''; ?>>Filed against the CEA - Pending</option>
                    </div>

                    <div class="form-group">
                        <label for="Case_No">Case No <span class="required">*</span></label>
                        <input type="text" id="Case_No" name="Case_No" required
                               value="<?php echo htmlspecialchars($case['Case_No']); ?>"
                               placeholder="Enter case title">
                    </div>

                    <div class="form-group">
                        <label for="Name_Address">Name Address <span class="required">*</span></label>
                        <input type="text" id="Name_Address" name="Name_Address" required
                               value="<?php echo htmlspecialchars($case['Name_Address']); ?>"
                               placeholder="Enter case title">
                    </div>

                    <div class="form-group">
                        <label for="Province">Province <span class="required">*</span></label>
                        <input type="text" id="Province" name="Province" required
                               value="<?php echo htmlspecialchars($case['Province']); ?>"
                               placeholder="Enter plaintiff name">
                    </div>

                    <div class="form-group">
                        <label for="dDistrict">District <span class="required">*</span></label>
                        <input type="text" id="District" name="District" required
                               value="<?php echo htmlspecialchars($case['District']); ?>"
                               placeholder="Enter defendant name">
                    </div>

                    <div class="form-group">
                        <label for="Filed_Date">Filed Date <span class="required">*</span></label>
                        <input type="date" id="Filed_Date" name="Filed_Date" required
                               value="<?php echo htmlspecialchars($case['Filed_Date']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="Court">Court <span class="required">*</span></label>
                        <input type="text" id="Court" name="cCourt" required
                               value="<?php echo htmlspecialchars($case['Court']); ?>"
                               placeholder="e.g., District Court A">
                    </div>

                    <div class="form-group">
                        <label for="Category_Cause_of_Action">Category/Cause of Action</label>
                        <input type="text" id="Category_Cause_of_Action" name="Category_Cause_of_Action"
                               value="<?php echo htmlspecialchars($case['Category_Cause_of_Action']); ?>"
                               placeholder="e.g., Hon. Judge Smith">
                    </div>

                    <div class="form-group">
                        <label for="Next_Date">Next Date</label>
                        <input type="date" id="Next_Date" name="Next_Date"
                               value="<?php echo htmlspecialchars($case['Next_Date'] ?? ''); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="Terminated">Terminated</label>
                        <textarea id="Terminated" name="Terminated"
                                  placeholder="Enter detailed Terminated"><?php echo htmlspecialchars($case['Terminated']); ?></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="Remarks">Remarks</label>
                        <textarea id="Remarks" name="Remarks"
                                  placeholder="Enter detailed Remarks"><?php echo htmlspecialchars($case['Remarks']); ?></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="Last_Date">Last Date</label>
                        <textarea id="Last_Date" name="Last_Date"
                                  placeholder="Enter detailed Last_Date"><?php echo htmlspecialchars($case['Last_Date']); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">💾 Update Case</button>
                    <a href="dashboard.php?case_no=<?php echo urlencode($case_id); ?>" class="btn btn-cancel">✕ Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
