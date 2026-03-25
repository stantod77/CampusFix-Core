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
    $sql = "SELECT ticket_id, title, description, location, severity_level, status, category, created_at FROM tickets ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $all_tickets = [];
    while($row = $result->fetch_assoc()) { $all_tickets[] = $row; }

} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>CampusFix Admin Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --navy: #0b1f3a; }
        body { background: #f4f7f9; font-family: sans-serif; }
        .navbar { background: white; border-bottom: 3px solid var(--navy); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .cf-logo { background: var(--navy); color: white; padding: 5px 12px; border-radius: 8px; font-weight: 800; }
        .nav-pills .nav-link.active { background-color: var(--navy); border-radius: 10px; }
        .nav-link { color: var(--navy); font-weight: 600; }
        .btn-logout { background-color: var(--navy); color: white !important; border-radius: 8px; font-weight: 600; padding: 6px 15px; }
        .stat-card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .main-card { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: white; }
        .badge-open { background: #ffc107; color: #000; }
        .badge-resolved { background: #28a745; color: #fff; }
        .btn { border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <span class="navbar-brand font-weight-bold" style="color:var(--navy);">
            <span class="cf-logo">CF</span> CampusFix
        </span>
        <div class="ml-auto">
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">
    <ul class="nav nav-pills mb-4 justify-content-center" id="pills-tab" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#content-dash">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#content-queue">All</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#content-reports">Reports</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="content-dash">
            <div class="row mb-4 text-center">
                <div class="col-md-4"><div class="card stat-card p-3"><small class="text-muted">OPEN TICKETS</small><h2 class="font-weight-bold"><?php echo $total_open; ?></h2></div></div>
                <div class="col-md-4"><div class="card stat-card p-3"><small class="text-muted">EMERGENCY (P5)</small><h2 class="font-weight-bold text-danger"><?php echo $high_severity; ?></h2></div></div>
                <div class="col-md-4"><div class="card stat-card p-3"><small class="text-muted">NEW TODAY</small><h2 class="font-weight-bold text-success"><?php echo $today_count; ?></h2></div></div>
            </div>
            <div class="card main-card p-4">
                <h5 class="font-weight-bold mb-4">Live Issue Distribution</h5>
                <div style="height: 300px;"><canvas id="categoryChart"></canvas></div>
            </div>
        </div>

        <div class="tab-pane fade" id="content-queue">
            <div class="card main-card p-4">
                <h5 class="font-weight-bold mb-3">Live Work Queue</h5>
                <table id="queueTable" class="table table-hover w-100">
                    <thead><tr><th>ID</th><th>User</th><th>Category</th><th>Room</th><th>Sev</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach($all_tickets as $row): ?>
                        <?php $s = strtolower($row['status'] ?? 'open'); ?>
                        <tr>
                            <td>#<?php echo $row['ticket_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['category'] ?? 'Unassigned'); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo $row['severity_level']; ?></td>
                            <td><span class="badge <?php echo ($s == 'resolved') ? 'badge-resolved' : 'badge-open'; ?>"><?php echo ucfirst($s); ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#viewModal" data-id="<?php echo $row['ticket_id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-loc="<?php echo htmlspecialchars($row['location']); ?>" data-status="<?php echo $s; ?>" data-desc="<?php echo htmlspecialchars($row['description']); ?>">View</button>
                                <?php if($s !== 'resolved'): ?>
                                    <button onclick="updateStatus(<?php echo $row['ticket_id']; ?>, 'resolved')" class="btn btn-sm btn-success">Resolve</button>
                                <?php else: ?>
                                    <button onclick="updateStatus(<?php echo $row['ticket_id']; ?>, 'open')" class="btn btn-sm btn-danger">Open</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="content-reports">
            <div class="card main-card p-4">
                <h5 class="font-weight-bold mb-4">Data Analysis & Filtering</h5>
                <div class="row align-items-end mb-4">
                    <div class="col-md-3">
                        <label class="small font-weight-bold">CATEGORY</label>
                        <select id="repCat" class="form-control"><option value="">All</option><option>Electrical</option><option>Plumbing</option><option>IT Support</option><option>General</option></select>
                    </div>
                    <div class="col-md-2">
                        <label class="small font-weight-bold">SEVERITY</label>
                        <select id="repSev" class="form-control"><option value="">All</option><option>5</option><option>4</option><option>3</option><option>2</option><option>1</option></select>
                    </div>
                    <div class="col-md-3">
                        <label class="small font-weight-bold">STATUS</label>
                        <select id="repStat" class="form-control"><option value="">All</option><option value="Open">Open</option><option value="Resolved">Resolved</option></select>
                    </div>
                    <div class="col-md-4">
                        <button id="runReport" class="btn btn-primary w-100 shadow-sm" style="background:var(--navy); border:none;">Show</button>
                    </div>
                </div>
                
                <div id="reportArea" style="display:none;">
                    <hr>
                    <table id="reportTable" class="table table-sm table-bordered w-100">
                        <thead class="thead-light"><tr><th>ID</th><th>User</th><th>Category</th><th>Room</th><th>Sev</th><th>Status</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow">
      <div class="modal-header bg-dark text-white p-4"><h5 class="modal-title font-weight-bold" id="mID">DETAILS</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
      <div class="modal-body p-4">
        <p><strong>User:</strong> <span id="mTitle"></span> | <strong>Status:</strong> <span id="mStatus" class="badge"></span></p>
        <p><strong>Room:</strong> <span id="mLoc"></span></p><hr>
        <p><strong>Issue:</strong></p>
        <div id="mDesc" class="p-3 bg-light rounded border-left" style="border-left:5px solid var(--navy); white-space: pre-wrap;"></div>
      </div>
      <div class="modal-footer bg-light" id="mFooter"></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Initialize Queue (Live)
    var qTable = $('#queueTable').DataTable({ "order": [[ 0, "desc" ]] });

    // 2. Initialize Report Table (Hidden at start)
    var rTable = $('#reportTable').DataTable({ "paging": false, "searching": true, "info": false });

    // 3. Chart
    var ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($cat_labels); ?>,
            datasets: [{
                label: 'Volume',
                data: <?php echo json_encode($cat_data); ?>,
                backgroundColor: <?php echo json_encode(array_slice($colors, 0, count($cat_labels))); ?>,
                borderRadius: 8
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 4. REPORT LOGIC - Independent from Queue
    $('#runReport').on('click', function(){
        $('#reportArea').show();
        rTable.clear();

        // Feed the report table from the queue data
        qTable.rows().every(function(){
            var d = this.data();
            // Columns: 0:ID, 1:User, 2:Cat, 3:Room, 4:Sev, 5:Status(HTML)
            var cleanStatus = $(d[5]).text(); 
            
            // Check filters
            var catMatch = ($('#repCat').val() == "" || d[2] == $('#repCat').val());
            var sevMatch = ($('#repSev').val() == "" || d[4] == $('#repSev').val());
            var statMatch = ($('#repStat').val() == "" || cleanStatus == $('#repStat').val());

            if(catMatch && sevMatch && statMatch) {
                rTable.row.add([d[0], d[1], d[2], d[3], d[4], cleanStatus]);
            }
        });
        rTable.draw();
    });

    // Modal populate
    $('#viewModal').on('show.bs.modal', function (e) {
        var b = $(e.relatedTarget);
        var id = b.data('id');
        var s = b.data('status');
        $(this).find('#mID').text('TICKET #' + id);
        $(this).find('#mTitle').text(b.data('title'));
        $(this).find('#mLoc').text(b.data('loc'));
        $(this).find('#mDesc').text(b.data('desc'));
        $(this).find('#mStatus').text(s.toUpperCase()).removeClass('badge-resolved badge-open').addClass(s=='resolved'?'badge-resolved':'badge-open');
        var footer = $(this).find('#mFooter').empty();
        footer.append('<button class="btn btn-outline-secondary" data-dismiss="modal">Close</button>');
        footer.append(s === 'resolved' ? '<button onclick="updateStatus('+id+',\'open\')" class="btn btn-danger">Re-Open</button>' : '<button onclick="updateStatus('+id+',\'resolved\')" class="btn btn-success">Resolve</button>');
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
