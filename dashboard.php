<?php
include('auth_check.php');

$servername = "localhost";
$username = "www-data";
$password = "";
$dbname = "campusfix_db";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // STATS
    $total_open = $conn->query("SELECT COUNT(*) as c FROM tickets WHERE status='open'")->fetch_assoc()['c'];
    $high_severity = $conn->query("SELECT COUNT(*) as c FROM tickets WHERE severity_level=5 AND status='open'")->fetch_assoc()['c'];
    $today_count = $conn->query("SELECT COUNT(*) as c FROM tickets WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['c'];

    // CHART DATA
    $cat_query = $conn->query("SELECT IFNULL(NULLIF(category, ''), 'Unassigned') as cat_label, COUNT(*) as count FROM tickets GROUP BY cat_label");
    $cat_labels = []; $cat_data = [];
    $colors = ['#0b1f3a', '#007bff', '#28a745', '#ffc107', '#17a2b8', '#6610f2'];
    while($row = $cat_query->fetch_assoc()){
        $cat_labels[] = $row['cat_label'];
        $cat_data[] = $row['count'];
    }

    // PULL DATA ONCE
    $sql = "SELECT ticket_id, title, description, location, severity_level, status, category, DATE_FORMAT(created_at, '%b %d, %H:%i') as date_submitted FROM tickets ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $all_tickets = [];
    while($row = $result->fetch_assoc()) { $all_tickets[] = $row; }

} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <style>
        :root { --campus-navy: #0b1f3a; }
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .top-nav { background: white; border-bottom: 2px solid var(--campus-navy); padding: 10px 30px; display: flex; align-items: center; justify-content: space-between; }
        .logo-container { display: flex; align-items: center; font-weight: bold; color: var(--campus-navy); font-size: 1.2rem; }
        .cf-box { background: var(--campus-navy); color: white; padding: 2px 8px; border-radius: 4px; margin-right: 10px; font-size: 0.9rem; }
        .btn-logout { background: var(--campus-navy); color: white; border-radius: 4px; padding: 5px 20px; font-weight: bold; border: none; cursor: pointer; }
        .dashboard-nav { display: flex; justify-content: center; gap: 20px; padding: 20px; }
        .nav-item-custom { font-weight: bold; color: var(--campus-navy); padding: 5px 25px; cursor: pointer; border-radius: 50px; }
        .nav-item-active { background: var(--campus-navy); color: white !important; }
        .tab-section { display: none; }
        .tab-section.active { display: block; }
        .stat-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); text-align: center; }
        .stat-title { font-size: 0.8rem; font-weight: bold; color: #6c757d; text-transform: uppercase; }
        .stat-value { font-size: 2.2rem; font-weight: bold; color: #333; margin: 5px 0; }
        .table-container { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 30px; margin: 0 20px; }
        .badge-open { background-color: #ffc107; color: #000; font-weight: bold; padding: 4px 8px; }
        .badge-resolved { background-color: #28a745; color: #fff; font-weight: bold; padding: 4px 8px; }
    </style>
</head>
<body>

<div class="top-nav">
    <div class="logo-container"><span class="cf-box">CF</span> CampusFix</div>
    <a href="logout.php" class="btn btn-logout">Logout</a>
</div>

<div class="dashboard-nav">
    <div id="tab-dashboard" class="nav-item-custom nav-item-active" onclick="switchTab('dashboard')">Dashboard</div>
    <div id="tab-all" class="nav-item-custom" onclick="switchTab('all')">All</div>
    <div id="tab-reports" class="nav-item-custom" onclick="switchTab('reports')">Reports</div>
</div>

<div class="container-fluid">
    <div id="section-dashboard" class="tab-section active">
        <div class="row px-3 mb-4">
            <div class="col-md-4"><div class="stat-card"><div class="stat-title">Open Tickets</div><div class="stat-value">14</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-title">Emergency</div><div class="stat-value text-danger">3</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="stat-title">Today</div><div class="stat-value text-success">5</div></div></div>
        </div>
        <div class="table-container">
            <h5 class="font-weight-bold mb-4">Live Issue Distribution</h5>
            <canvas id="issueChart" style="max-height: 250px;"></canvas>
        </div>
    </div>

    <div id="section-all" class="tab-section">
        <div class="table-container">
            <h5 class="font-weight-bold mb-4">Live Work Queue</h5>
            <table id="reportTable" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th><th>User</th><th>Category</th><th>Room</th><th>Sev</th><th>Status</th><th>Date Submitted</th><th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="section-reports" class="tab-section">
        <div class="table-container">
            <h5 class="font-weight-bold mb-4">Data Analysis & Filtering</h5>
            <div class="row">
                <div class="col-md-3"><label class="font-weight-bold small">CATEGORY</label><select class="form-control"><option>All</option></select></div>
                <div class="col-md-3"><label class="font-weight-bold small">SEVERITY</label><select class="form-control"><option>All</option></select></div>
                <div class="col-md-3"><label class="font-weight-bold small">STATUS</label><select class="form-control"><option>All</option></select></div>
                <div class="col-md-3"><label>&nbsp;</label><button class="btn btn-block btn-logout">Show</button></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mID">TICKET DETAILS</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>Requester:</strong> <span id="mTitle"></span></p>
                <p><strong>Building/Room:</strong> <span id="mLoc"></span></p>
                <p><strong>Severity:</strong> <span id="mSev"></span></p>
                <p><strong>Date Submitted:</strong> <span id="mDate"></span></p>
                <p><strong>Description:</strong></p>
                <div id="mDesc" class="p-2 border rounded bg-light"></div>
            </div>
            <div class="modal-footer" id="mFooter"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function switchTab(tabId) {
    $('.tab-section').removeClass('active');
    $('.nav-item-custom').removeClass('nav-item-active');
    $('#section-' + tabId).addClass('active');
    $('#tab-' + tabId).addClass('nav-item-active');
    if(tabId === 'all') { $('#reportTable').DataTable().columns.adjust().draw(); }
}

$(document).ready(function() {
    var rTable = $('#reportTable').DataTable({ "order": [[ 0, "desc" ]] });

    var ctx = document.getElementById('issueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Electrical', 'General', 'IT Support', 'Plumbing'],
            datasets: [{ label: 'Volume', data: [2, 12, 3, 3], backgroundColor: ['#0b1f3a', '#007bff', '#28a745', '#ffc107'] }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    fetch('api/list_tickets.php').then(r => r.json()).then(data => {
        rTable.clear();
        data.forEach(d => {
            var badge = d[5] === 'resolved' ? 'badge-resolved' : 'badge-open';
            rTable.row.add([
                '#'+d[0], 
                '<b>'+d[1]+'</b>', 
                'General', 
                d[2], 
                d[4],
                '<span class="badge '+badge+'">'+d[5].toUpperCase()+'</span>',
                d[6],
                '<div>' +
                    '<button class="btn btn-sm btn-outline-info mr-1" data-toggle="modal" data-target="#viewModal" ' +
                    'data-id="'+d[0]+'" data-title="'+d[1]+'" data-loc="'+d[2]+'" data-desc="'+d[3]+'" data-sev="'+d[4]+'" data-status="'+d[5]+'" data-date="'+d[6]+'">View</button>' +
                    (d[5] === 'resolved' ? '' : '<button onclick="updateStatus('+d[0]+',\'resolved\')" class="btn btn-sm btn-success">Resolve</button>') +
                '</div>'
            ]);
        });
        rTable.draw();
    });

    $('#viewModal').on('show.bs.modal', function (e) {
        var b = $(e.relatedTarget);
        var id = b.data('id');
        var s = b.data('status');
        $(this).find('#mID').text('TICKET #' + id);
        $(this).find('#mTitle').text(b.data('title'));
        $(this).find('#mLoc').text(b.data('loc'));
        $(this).find('#mSev').text(b.data('sev'));
        $(this).find('#mDate').text(b.data('date'));
        $(this).find('#mDesc').text(b.data('desc'));
        
        var footer = $(this).find('#mFooter').empty();
        footer.append('<button class="btn btn-secondary" data-dismiss="modal">Close</button>');
        if(s !== 'resolved') {
            footer.append('<button onclick="updateStatus('+id+',\'resolved\')" class="btn btn-success">Resolve</button>');
        } else {
            footer.append('<button onclick="updateStatus('+id+',\'open\')" class="btn btn-warning">Re-Open</button>');
        }
    });
});

function updateStatus(id, s) {
    if(confirm('Change status to ' + s + '?')) {
        fetch('api/resolve_ticket.php?id=' + id + '&status=' + s)
        .then(r => r.json())
        .then(data => { if(data.success) location.reload(); });
    }
}
</script>
</body>
</html>
