<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Types = trim($_POST['Types'] ?? '');
    $Case_No= trim($_POST['Case_No'] ?? '');
    $Province = trim($_POST['Province'] ?? '');
    $District = trim($_POST['District'] ?? '');
    $Filed_Date = trim($_POST['Filed_Date'] ?? '');
    $Court = trim($_POST['Court'] ?? '');
    $Category_Cause_of_Action = trim($_POST['Category_Cause_of_Action'] ?? '');
    $Plaintiff_Name_Address = trim($_POST['Plaintiff_Name_Address'] ?? '');
    $Defendant_Name_Address = trim($_POST['Defendant_Name_Address'] ?? '');
    $Terminated = trim($_POST['Terminated'] ?? '');
    $Next_Date = trim($_POST['Next_Date'] ?? '');
    $Remarks = trim($_POST['Remarks'] ?? '');
    $Last_Date = trim($_POST['Last_Date'] ?? '');


    // Validation
    if (empty($Case_No) || empty($Types) || empty($Province) || empty($District) ||
        empty($Filed_Date) || empty($Court) || empty($Category_Cause_of_Action) || 
        empty($Defendant_Name_Address) ||empty($Plaintiff_Name_Address) || empty($Terminated) || empty($Case_status) || empty($Next_Date) || empty($Remarks)|| empty($Last_Date)) {
        $error = 'Please fill in all required fields';
    } else {
        // Check if case ID already exists
        $check_stmt = $conn->prepare("SELECT Case_No FROM cases WHERE Case_No = ?");
        $check_stmt->bind_param("s", $Case_No);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = 'Case ID already exists. Please use a different Case ID.';
        } else {
            // Insert new case
            $stmt = $conn->prepare("INSERT INTO cases (Case_No, Types, Province, District, Filed_Date, Court, Category_Cause_of_Action, Defendant_Name_Address,Plaintiff_Name_Address, Terminated, Next_Date, Remarks,Last_Date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $Next_Date_value = !empty($Next_Date) ? $Next_Date : null;

            $stmt->bind_param("sssssssssss", $Case_No, $Types, $Province, $District, $Filed_Date, $Court, $Category_Cause_of_Action, $Defendant_Name_Address,$Plaintiff_Name_Address, $Terminated, $Remarks, $next_hearing_date_value, $Last_Date);

            if ($stmt->execute()) {
                $success = 'Case added successfully!';
                // Clear form
                $_POST = array();
            } else {
                $error = 'Error adding case: ' . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Case - Court Case Management System</title>
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
            background-image: url('https://images.pexels.com/photos/6077326/pexels-photo-6077326.jpeg?auto=compress&cs=tinysrgb&w=1920');
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        .btn-reset {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
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
            <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h2>➕ Add New Case</h2>
                <p>Fill in all required information to register a new court case</p>
            </div>

            <?php if ($success): ?>
                <div class="success-message">✓ <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message">✗ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="Case_No">Case No <span class="required">*</span></label>
                        <input type="text" id="Case_No" name="Case_No" required
                               value="<?php echo htmlspecialchars($_POST['Case_No'] ?? ''); ?>"
                               placeholder="e.g., CIV-2024-001">
                    </div>

                    <div class="form-group">
                        <label for="Province">Province <span class="required">*</span></label>
                        <input type="text" id="Province" name="Province" required
                               value="<?php echo htmlspecialchars($_POST['Province'] ?? ''); ?>"
                               placeholder="Enter case Province">
                        
                    </div>

                    <div class="form-group full-width">
                        <label for="Types">Types  <span class="required">*</span></label>
                        <select id="Types" name="Types" required>
                            <option value="">Select Type</option>
                            <option value="Filed by the CEA" <?php echo ($case['Types'] === 'Filed by the CEA') ? 'selected' : ''; ?>>Filed by the CEA</option>
                            <option value="Filed against the CEA - Pending" <?php echo ($case['Types'] === 'Filed against the CEA - Pending') ? 'selected' : ''; ?>>Filed against the CEA - Pending</option>
                       </select>
                    </div>

                    <div class="form-group">
                        <label for="District">District <span class="required">*</span></label>
                        <input type="text" id="pDistrict" name="District" required
                               value="<?php echo htmlspecialchars($_POST['District'] ?? ''); ?>"
                               placeholder="Enter District">
                    </div>

                    <div class="form-group">
                        <label for="Filed_Date">Filed Date <span class="required">*</span></label>
                        <input type="date" id="Filed_Date" name="Filed_Date" required
                               value="<?php echo htmlspecialchars($_POST['Filed_Date'] ?? ''); ?>"
                               placeholder="Enter Filed Date">
                    </div>

                    <div class="form-group">
                        <label for="Court">Court <span class="required">*</span></label>
                        <input type="date" id="Court" name="Court" required
                               value="<?php echo htmlspecialchars($_POST['Court'] ?? ''); ?>"
                               placeholder="Enter Courte">
                    </div>

                    <div class="form-group">
                        <label for="Category_Cause_of_Action">Category/Cause of Action <span class="required">*</span></label>
                        <input type="text" id="Category_Cause_of_Action" name="Category_Cause_of_Action" required
                               value="<?php echo htmlspecialchars($_POST['Category_Cause_of_Action'] ?? ''); ?>"
                               placeholder="e.g., Category/Cause of Action">
                    </div>

                    <div class="form-group">
                        <label for="Plaintiff_Name_Address">Plaintiff Name Address</label>
                        <input type="text" id="Plaintiff_Name_Address" name="Plaintiff_Name_Address
                               value="<?php echo htmlspecialchars($_POST['Plaintiff_Name_Address'] ?? ''); ?>"
                               placeholder="e.g., Name Address">
                    </div>

                    <div class="form-group">
                        <label for="Defendant_Name_Address">Defendant Name Address</label>
                        <input type="text" id="Defendant_Name_Address" name="Defendant_Name_Address"
                               value="<?php echo htmlspecialchars($_POST['Defendant_Name_Address'] ?? ''); ?>"
                               placeholder="e.g., Name Address">
                    </div>

                    <div class="form-group">
                        <label for="Terminated">Terminated <span class="required">*</span></label>
                        <input type="text" id="Terminated" name="Terminated"
                               value="<?php echo htmlspecialchars($_POST['Terminated'] ?? ''); ?>"
                               placeholder="e.g., Terminated">
                    </div>

                    <div class="form-group">
                        <label for="Remarks">Remarks</label>
                        <input type="date" id="Remarks" name="Remarks"
                               value="<?php echo htmlspecialchars($_POST['Remarks'] ?? ''); ?>">
                    </div>

                    <div class="form-group full-width">
                        <label for="Next_Date">Next Date</label>
                        <textarea id="date" name="Next_Date" id="Next_Date"
                                  placeholder="Enter Next Date"><?php echo htmlspecialchars($_POST['Next_Date'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="Last_Date">Last Date</label>
                        <textarea id="date" name="Last_Date" id="Last_Date"
                                  placeholder="Enter Next Date"><?php echo htmlspecialchars($_POST['Last_Date'] ?? ''); ?></textarea>
                    </div>

                      <div class="form-group">
                        <label for="judge_name">Judge Name</label>
                        <input type="text" id="judge_name" name="judge_name"
                               value="<?php echo htmlspecialchars($_POST['judge_name'] ?? ''); ?>"
                               placeholder="e.g., Hon. Judge Smith">
                    </div>
                    <div class="form-group">
                        <label for="case_status">Case Status <span class="required">*</span></label>
                        <select id="case_status" name="case_status" required>
                            <option value="">Select Status</option>
                            <option value="Active" <?php echo (isset($_POST['case_status']) && $_POST['case_status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Under Trial" <?php echo (isset($_POST['case_status']) && $_POST['case_status'] === 'Under Trial') ? 'selected' : ''; ?>>Under Trial</option>
                            <option value="Settled" <?php echo (isset($_POST['case_status']) && $_POST['case_status'] === 'Settled') ? 'selected' : ''; ?>>Settled</option>
                            <option value="Closed" <?php echo (isset($_POST['case_status']) && $_POST['case_status'] === 'Closed') ? 'selected' : ''; ?>>Closed</option>
                            <option value="Pending" <?php echo (isset($_POST['case_status']) && $_POST['case_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        </select>
                    </div>

                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-submit">💾 Save Case</button>
                    <button type="reset" class="btn btn-reset">🔄 Reset Form</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
