<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Check if case_id is provided
if (!isset($_GET['case_id']) || empty($_GET['case_id'])) {
    header('Location: dashboard.php');
    exit();
}

$case_id = $_GET['case_id'];
$success = '';
$error = '';

// Fetch case details
$stmt = $conn->prepare("SELECT * FROM cases WHERE case_id = ?");
$stmt->bind_param("s", $case_id);
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
    

    $Case_No.= trim($_POST['Case_No.'] ?? '');
    $Province = trim($_POST['Province'] ?? '');
    $District = trim($_POST['District'] ?? '');
    $Filed_Date = trim($_POST['Filed_Date'] ?? '');
    $Court = $_POST['Court'] ?? '';
    $Category_Cause_of_Action = trim($_POST['Category_Cause_of_Action'] ?? '');
    $Name_Address = trim($_POST['Name_Address'] ?? '');
    $Terminated = trim($_POST['Terminated'] ?? '');
    $Next_Date = trim($_POST['Next_Date'] ?? '');
    $Remarks = $trim['Remarks'] ?? '';
    $Last_Date = $trim['Last_Date'] ?? '';

    // Validation
    if (empty($Province) || empty($District || empty($Case_No.) ||
        empty($Court) || empty($Category_Cause_of_Action) || empty($Name_Address) || empty($Terminated)|| empty($Next_Date)|| empty($Remarks)|| empty($Last_Date)) {
        $error = 'Please fill in all required fields';
    } else {
        // Update case
        $update_stmt = $conn->prepare("UPDATE cases SET Province = ?, District = ?, Case_No. = ?, Filed_Date = ?, Court = ?, Category_Cause_of_Action = ?, Name_Address = ?, Terminateds = ?, Next_Date = ?, Remarks = ? WHERE Last_Date = ?");

        $next_hearing_date_value = !empty($next_hearing_date) ? $next_hearing_date : null;

        $update_stmt->bind_param("sssssssssss", $Province, $District, $Case_No., $Filed_Date, $Court, $Category_Cause_of_Action, $Name_Address, $Terminated, $Next_Date, $Remarks, $Last_Date);

        if ($update_stmt->execute()) {
            $success = 'Case updated successfully!';
            // Refresh case data
            $case['Province'] = $Province;
            $case['District'] = $District;
            $case['Case_No.'] = $Case_No.;
            $case['Filed_Date'] = $Filed_Date;
            $case['Court'] = $Court;
            $case['Category_Cause_of_Action'] = $Category_Cause_of_Action;
            $case['Name_Address'] = $Name_Addresse;
            $case['Terminated'] = $Terminated
            $case['Next_Date'] = $Next_Date;
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
            <a href="dashboard.php?Case_No.=<?php echo urlencode($case_id); ?>" class="btn-back">← Back to Case</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>✏️ Edit Case</h2>
                <p>Update case information</p>
                <span class="case-id-badge">Case ID: <?php echo htmlspecialchars($case['case_id']); ?></span>
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
                        <label for="case_title">Case Title <span class="required">*</span></label>
                        <input type="text" id="case_title" name="case_title" required
                               value="<?php echo htmlspecialchars($case['case_title']); ?>"
                               placeholder="Enter case title">
                    </div>

                    <div class="form-group">
                        <label for="case_type">Case Type <span class="required">*</span></label>
                        <select id="case_type" name="case_type" required>
                            <option value="">Select Case Type</option>
                            <option value="Civil" <?php echo ($case['case_type'] === 'Civil') ? 'selected' : ''; ?>>Civil</option>
                            <option value="Criminal" <?php echo ($case['case_type'] === 'Criminal') ? 'selected' : ''; ?>>Criminal</option>
                            <option value="Family" <?php echo ($case['case_type'] === 'Family') ? 'selected' : ''; ?>>Family</option>
                            <option value="Commercial" <?php echo ($case['case_type'] === 'Commercial') ? 'selected' : ''; ?>>Commercial</option>
                            <option value="Constitutional" <?php echo ($case['case_type'] === 'Constitutional') ? 'selected' : ''; ?>>Constitutional</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="case_status">Case Status <span class="required">*</span></label>
                        <select id="case_status" name="case_status" required>
                            <option value="">Select Status</option>
                            <option value="Active" <?php echo ($case['case_status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Under Trial" <?php echo ($case['case_status'] === 'Under Trial') ? 'selected' : ''; ?>>Under Trial</option>
                            <option value="Settled" <?php echo ($case['case_status'] === 'Settled') ? 'selected' : ''; ?>>Settled</option>
                            <option value="Closed" <?php echo ($case['case_status'] === 'Closed') ? 'selected' : ''; ?>>Closed</option>
                            <option value="Pending" <?php echo ($case['case_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="plaintiff_name">Plaintiff Name <span class="required">*</span></label>
                        <input type="text" id="plaintiff_name" name="plaintiff_name" required
                               value="<?php echo htmlspecialchars($case['plaintiff_name']); ?>"
                               placeholder="Enter plaintiff name">
                    </div>

                    <div class="form-group">
                        <label for="defendant_name">Defendant Name <span class="required">*</span></label>
                        <input type="text" id="defendant_name" name="defendant_name" required
                               value="<?php echo htmlspecialchars($case['defendant_name']); ?>"
                               placeholder="Enter defendant name">
                    </div>

                    <div class="form-group">
                        <label for="filing_date">Filing Date <span class="required">*</span></label>
                        <input type="date" id="filing_date" name="filing_date" required
                               value="<?php echo htmlspecialchars($case['filing_date']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="court_name">Court Name <span class="required">*</span></label>
                        <input type="text" id="court_name" name="court_name" required
                               value="<?php echo htmlspecialchars($case['court_name']); ?>"
                               placeholder="e.g., District Court A">
                    </div>

                    <div class="form-group">
                        <label for="judge_name">Judge Name</label>
                        <input type="text" id="judge_name" name="judge_name"
                               value="<?php echo htmlspecialchars($case['judge_name']); ?>"
                               placeholder="e.g., Hon. Judge Smith">
                    </div>

                    <div class="form-group">
                        <label for="next_hearing_date">Next Hearing Date</label>
                        <input type="date" id="next_hearing_date" name="next_hearing_date"
                               value="<?php echo htmlspecialchars($case['next_hearing_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="case_description">Case Description</label>
                        <textarea id="case_description" name="case_description"
                                  placeholder="Enter detailed case description"><?php echo htmlspecialchars($case['case_description']); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">💾 Update Case</button>
                    <a href="dashboard.php?case_id=<?php echo urlencode($case_id); ?>" class="btn btn-cancel">✕ Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
