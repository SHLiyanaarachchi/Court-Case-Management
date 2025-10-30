<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Fetch statistics
$total_cases_query = "SELECT COUNT(*) as total FROM cases";
$total_cases_result = $conn->query($total_cases_query);
$total_cases = $total_cases_result->fetch_assoc()['total'];

$active_cases_query = "SELECT COUNT(*) as total FROM cases WHERE case_status = 'Active'";
$active_cases_result = $conn->query($active_cases_query);
$active_cases = $active_cases_result->fetch_assoc()['total'];

$settled_cases_query = "SELECT COUNT(*) as total FROM cases WHERE case_status = 'Settled'";
$settled_cases_result = $conn->query($settled_cases_query);
$settled_cases = $settled_cases_result->fetch_assoc()['total'];

$under_trial_query = "SELECT COUNT(*) as total FROM cases WHERE case_status = 'Under Trial'";
$under_trial_result = $conn->query($under_trial_query);
$under_trial_cases = $under_trial_result->fetch_assoc()['total'];

$upcoming_hearings_query = "SELECT COUNT(*) as total FROM cases WHERE next_hearing_date >= CURDATE()";
$upcoming_hearings_result = $conn->query($upcoming_hearings_query);
$upcoming_hearings = $upcoming_hearings_result->fetch_assoc()['total'];

$case_types_query = "SELECT case_type, COUNT(*) as count FROM cases GROUP BY case_type ORDER BY count DESC";
$case_types_result = $conn->query($case_types_query);
$case_types = [];
while ($row = $case_types_result->fetch_assoc()) {
    $case_types[] = $row;
}

// Fetch all case IDs for dropdown
$cases_query = "SELECT case_id, case_title FROM cases ORDER BY case_id";
$cases_result = $conn->query($cases_query);
$all_cases = [];
while ($row = $cases_result->fetch_assoc()) {
    $all_cases[] = $row;
}

// Get selected case details
$selected_case = null;
if (isset($_GET['case_id']) && !empty($_GET['case_id'])) {
    $case_id = $_GET['case_id'];
    $stmt = $conn->prepare("SELECT * FROM cases WHERE case_id = ?");
    $stmt->bind_param("s", $case_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $selected_case = $result->fetch_assoc();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Court Case Management System</title>
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
            background-image: url('https://images.pexels.com/photos/5669619/pexels-photo-5669619.jpeg?auto=compress&cs=tinysrgb&w=1920');
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
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 20px;
            color: white;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-logout {
            padding: 10px 24px;
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 65, 108, 0.4);
        }

        .container {
            max-width: 1400px;
            margin: 32px auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        .search-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 32px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            margin-bottom: 32px;
            animation: fadeInDown 0.6s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .search-header h2 {
            color: #1e3c72;
            font-size: 26px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .search-form {
            display: flex;
            gap: 16px;
            align-items: end;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .select-wrapper {
            position: relative;
        }

        select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            background: #fafafa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn-search {
            padding: 14px 32px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        .case-details {
            background: rgba(255, 255, 255, 0.95);
            padding: 32px;
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

        .case-details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px solid #f0f0f0;
        }

        .case-details-header h3 {
            color: #1e3c72;
            font-size: 24px;
        }

        .case-actions {
            display: flex;
            gap: 12px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-edit:hover {
            box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }

        .btn-delete:hover {
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .detail-item {
            padding: 16px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .detail-label {
            font-size: 13px;
            color: #666;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-settled {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-trial {
            background: #fff3cd;
            color: #856404;
        }

        .no-case {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-case-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .stats-section {
            margin-bottom: 32px;
            animation: fadeInDown 0.6s ease;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            font-size: 80px;
        }

        .stat-card.card-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .stat-card.card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .stat-card.card-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .stat-card.card-warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }

        .stat-card.card-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 12px;
            display: block;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        .case-types-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .case-types-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 3px solid #f0f0f0;
        }

        .case-types-header h3 {
            color: #1e3c72;
            font-size: 20px;
            margin: 0;
        }

        .case-types-list {
            display: grid;
            gap: 16px;
        }

        .case-type-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .case-type-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .case-type-name {
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }

        .case-type-count {
            font-size: 20px;
            font-weight: 700;
            color: #667eea;
            background: white;
            padding: 6px 16px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 16px;
            }

            .action-buttons {
                width: 100%;
            }

            .btn {
                flex: 1;
            }

            .stats-grid {
                grid-template-columns: 1fr;
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
            <div class="user-info">
                <span>👤 <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card card-primary">
                    <span class="stat-icon">📊</span>
                    <div class="stat-value"><?php echo $total_cases; ?></div>
                    <div class="stat-label">Total Cases</div>
                </div>

                <div class="stat-card card-success">
                    <span class="stat-icon">✅</span>
                    <div class="stat-value"><?php echo $active_cases; ?></div>
                    <div class="stat-label">Active Cases</div>
                </div>

                <div class="stat-card card-warning">
                    <span class="stat-icon">⚖️</span>
                    <div class="stat-value"><?php echo $under_trial_cases; ?></div>
                    <div class="stat-label">Under Trial</div>
                </div>

                <div class="stat-card card-info">
                    <span class="stat-icon">🤝</span>
                    <div class="stat-value"><?php echo $settled_cases; ?></div>
                    <div class="stat-label">Settled Cases</div>
                </div>

                <div class="stat-card card-danger">
                    <span class="stat-icon">📅</span>
                    <div class="stat-value"><?php echo $upcoming_hearings; ?></div>
                    <div class="stat-label">Upcoming Hearings</div>
                </div>
            </div>

            <?php if (!empty($case_types)): ?>
            <div class="case-types-section">
                <div class="case-types-header">
                    <span style="font-size: 24px;">📈</span>
                    <h3>Cases by Type</h3>
                </div>
                <div class="case-types-list">
                    <?php foreach ($case_types as $type): ?>
                    <div class="case-type-item">
                        <span class="case-type-name"><?php echo htmlspecialchars($type['case_type']); ?></span>
                        <span class="case-type-count"><?php echo $type['count']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="search-section">
            <div class="search-header">
                <h2>🔍 Search Cases</h2>
                <div class="action-buttons">
                    <a href="add_case.php" class="btn btn-primary">+ Add New Case</a>
                </div>
            </div>

            <form method="GET" action="" class="search-form">
                <div class="form-group">
                    <label for="case_id">Select or Type Case ID</label>
                    <div class="select-wrapper">
                        <select name="case_id" id="case_id" required>
                            <option value="">-- Select a Case ID --</option>
                            <?php foreach ($all_cases as $case): ?>
                                <option value="<?php echo htmlspecialchars($case['case_id']); ?>"
                                    <?php echo (isset($_GET['case_id']) && $_GET['case_id'] === $case['case_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($case['case_id']) . ' - ' . htmlspecialchars($case['case_title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-search">Search Case</button>
            </form>
        </div>

        <?php if ($selected_case): ?>
            <div class="case-details">
                <div class="case-details-header">
                    <h3>📋 Case Details</h3>
                    <div class="case-actions">
                        <a href="edit_case.php?case_id=<?php echo urlencode($selected_case['case_id']); ?>" class="btn btn-edit">✏️ Edit</a>
                        <a href="delete_case.php?case_id=<?php echo urlencode($selected_case['case_id']); ?>"
                           class="btn btn-delete"
                           onclick="return confirm('Are you sure you want to delete this case? This action cannot be undone.');">
                           🗑️ Delete
                        </a>
                    </div>
                </div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Case ID</div>
                        <div class="detail-value"><?php echo htmlspecialchars($selected_case['case_id']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Case Title</div>
                        <div class="detail-value"><?php echo htmlspecialchars($selected_case['case_title']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Case Type</div>
                        <div class="detail-value"><?php echo htmlspecialchars($selected_case['case_type']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Status</div>
                        <div class="detail-value">
                            <?php
                            $status = $selected_case['case_status'];
                            $status_class = 'status-active';
                            if (strpos(strtolower($status), 'settled') !== false) {
                                $status_class = 'status-settled';
                            } elseif (strpos(strtolower($status), 'trial') !== false) {
                                $status_class = 'status-trial';
                            }
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Plaintiff Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($selected_case['plaintiff_name']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Defendant Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($selected_case['defendant_name']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Filing Date</div>
                        <div class="detail-value"><?php echo date('F d, Y', strtotime($selected_case['filing_date'])); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Court Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($selected_case['court_name']); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Judge Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($selected_case['judge_name'] ?: 'Not Assigned'); ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Next Hearing Date</div>
                        <div class="detail-value">
                            <?php echo $selected_case['next_hearing_date'] ? date('F d, Y', strtotime($selected_case['next_hearing_date'])) : 'Not Scheduled'; ?>
                        </div>
                    </div>

                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <div class="detail-label">Case Description</div>
                        <div class="detail-value"><?php echo nl2br(htmlspecialchars($selected_case['case_description'])); ?></div>
                    </div>
                </div>
            </div>
        <?php elseif (isset($_GET['case_id'])): ?>
            <div class="case-details">
                <div class="no-case">
                    <div class="no-case-icon">❌</div>
                    <h3>Case Not Found</h3>
                    <p>The selected case ID does not exist in the system.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="case-details">
                <div class="no-case">
                    <div class="no-case-icon">📂</div>
                    <h3>Select a Case</h3>
                    <p>Please select a case ID from the dropdown above to view its details.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
