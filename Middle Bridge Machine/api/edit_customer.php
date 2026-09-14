<?php
include 'config.php';

$customer = null;
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $customer = fetchMasterCustomerData($search);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer Address & Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Edit Customer Profile: <?php echo $customer['CM_CODE'] ?? 'Not Found'; ?></h4>
        </div>
        <div class="card-body">
            <?php if ($customer): ?>
            <form id="editCustomerForm">
                <input type="hidden" id="CM_CODE" value="<?php echo $customer['CM_CODE']; ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Full Name</label>
                        <input type="text" id="CM_NAME" class="form-control" value="<?php echo $customer['CM_NAME']; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>NIC Number</label>
                        <input type="text" id="CM_NIC" class="form-control" value="<?php echo $customer['CM_NIC']; ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Date of Birth</label>
                        <input type="date" id="CM_DOB" class="form-control" value="<?php echo $customer['CM_DOB']; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Email Address</label>
                        <input type="email" id="CM_EMAIL" class="form-control" value="<?php echo $customer['CM_EMAIL'] ?? ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Mobile (Read Only)</label>
                        <input type="text" class="form-control" value="<?php echo $customer['CM_MOBILE']; ?>" readonly>
                    </div>

                    <div class="col-md-6 mb-3"><label>Address Line 1</label><input type="text" id="CM_ADD1" class="form-control" value="<?php echo $customer['CM_ADD1']; ?>"></div>
                    <div class="col-md-6 mb-3"><label>Address Line 2</label><input type="text" id="CM_ADD2" class="form-control" value="<?php echo $customer['CM_ADD2']; ?>"></div>
                    <div class="col-md-6 mb-3"><label>Address Line 3</label><input type="text" id="CM_ADD3" class="form-control" value="<?php echo $customer['CM_ADD3']; ?>"></div>
                    <div class="col-md-6 mb-3"><label>Address Line 4</label><input type="text" id="CM_ADD4" class="form-control" value="<?php echo $customer['CM_ADD4']; ?>"></div>
                </div>

                <button type="button" onclick="saveCustomer()" class="btn btn-success px-5">Update Records</button>
            </form>
            <?php else: ?>
                <div class="alert alert-warning">Please search for a valid customer first.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
async function saveCustomer() {
    const payload = {
        key: 'ASB2026SECRET',
        CM_CODE: document.getElementById('CM_CODE').value,
        CM_NAME: document.getElementById('CM_NAME').value,
        CM_NIC: document.getElementById('CM_NIC').value,
        CM_DOB: document.getElementById('CM_DOB').value,
        CM_EMAIL: document.getElementById('CM_EMAIL').value,
        CM_ADD1: document.getElementById('CM_ADD1').value,
        CM_ADD2: document.getElementById('CM_ADD2').value,
        CM_ADD3: document.getElementById('CM_ADD3').value,
        CM_ADD4: document.getElementById('CM_ADD4').value
    };

    const response = await fetch('update_customer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });

    const result = await response.json();
    alert(result.message);
}
</script>
</body>
</html>