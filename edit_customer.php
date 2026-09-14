<?php
include 'config.php';

$customer = null;
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $customer = fetchMasterCustomerData($search);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Customer Address & Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 900px;">
    
    <div id="uiResponseAlert" class="alert d-none" role="alert"></div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white p-3">
            <h4 class="mb-0 fs-5">
                <i class="fas fa-user-gear me-2"></i>Edit Customer Profile: 
                <span class="text-warning"><?php echo htmlspecialchars($customer['CM_CODE'] ?? 'Not Found', ENT_QUOTES, 'UTF-8'); ?></span>
            </h4>
        </div>
        <div class="card-body p-4">
            <?php if ($customer): ?>
            <form id="editCustomerForm" onsubmit="event.preventDefault();">
                <input type="hidden" id="CM_CODE" value="<?php echo htmlspecialchars($customer['CM_CODE'], ENT_QUOTES, 'UTF-8'); ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Full Name</label>
                        <input type="text" id="CM_NAME" class="form-control" value="<?php echo htmlspecialchars($customer['CM_NAME'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold text-secondary">NIC Number</label>
                        <input type="text" id="CM_NIC" class="form-control" value="<?php echo htmlspecialchars($customer['CM_NIC'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold text-secondary">Date of Birth</label>
                        <?php 
                            // Convert back to clean standard input element variant format (Y-m-d) if raw date conversion parameters match
                            $formattedDob = !empty($customer['CM_DOB']) ? date('Y-m-d', strtotime($customer['CM_DOB'])) : '';
                        ?>
                        <input type="date" id="CM_DOB" class="form-control" value="<?php echo $formattedDob; ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Email Address</label>
                        <input type="email" id="CM_EMAIL" class="form-control" value="<?php echo htmlspecialchars($customer['CM_EMAIL'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="name@domain.com">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold text-secondary">Mobile (Read Only)</label>
                        <input type="text" class="form-control bg-light text-muted" value="<?php echo htmlspecialchars($customer['CM_MOBILE'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                    </div>

                    <div class="col-md-6 mb-3"><label class="form-label fw-semibold text-secondary">Address Line 1</label><input type="text" id="CM_ADD1" class="form-control" value="<?php echo htmlspecialchars($customer['CM_ADD1'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-semibold text-secondary">Address Line 2</label><input type="text" id="CM_ADD2" class="form-control" value="<?php echo htmlspecialchars($customer['CM_ADD2'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-semibold text-secondary">Address Line 3</label><input type="text" id="CM_ADD3" class="form-control" value="<?php echo htmlspecialchars($customer['CM_ADD3'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-semibold text-secondary">Address Line 4</label><input type="text" id="CM_ADD4" class="form-control" value="<?php echo htmlspecialchars($customer['CM_ADD4'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                </div>

                <div class="mt-3 border-top pt-3 d-flex justify-content-end">
                    <button type="button" id="btnSubmitUpdate" onclick="saveCustomer()" class="btn btn-success px-5 fw-bold">
                        <i class="fas fa-cloud-arrow-up me-2"></i>Update Records
                    </button>
                </div>
            </form>
            <?php else: ?>
                <div class="alert alert-warning mb-0 border-start border-warning border-3">
                    <i class="fas fa-circle-exclamation me-2"></i>Please supply a operational search filter argument query to modify customer records.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
async function saveCustomer() {
    const btn = document.getElementById('btnSubmitUpdate');
    const alertBox = document.getElementById('uiResponseAlert');
    
    // Clear previous alert states
    alertBox.classList.add('d-none');
    alertBox.className = "alert";

    // Client-side execution field verification
    const nameField = document.getElementById('CM_NAME').value.trim();
    if (!nameField) {
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = '<i class="fas fa-triangle-exclamation me-2"></i>Full Name field is required.';
        return;
    }

    // Toggle interactive engine execution load barrier
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Core Master...';

    const payload = {
        key: 'ASB2026SECRET',
        CM_CODE: document.getElementById('CM_CODE').value,
        CM_NAME: nameField,
        CM_NIC: document.getElementById('CM_NIC').value.trim(),
        CM_DOB: document.getElementById('CM_DOB').value,
        CM_EMAIL: document.getElementById('CM_EMAIL').value.trim(),
        CM_ADD1: document.getElementById('CM_ADD1').value.trim(),
        CM_ADD2: document.getElementById('CM_ADD2').value.trim(),
        CM_ADD3: document.getElementById('CM_ADD3').value.trim(),
        CM_ADD4: document.getElementById('CM_ADD4').value.trim()
    };

    try {
        const response = await fetch('update_customer.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error(`HTTP network error returned with status code: ${response.status}`);
        }

        const result = await response.json();
        alertBox.classList.remove('d-none');

        if (result.success) {
            alertBox.classList.add('alert-success');
            alertBox.innerHTML = `<i class="fas fa-circle-check me-2"></i>${result.message || 'Records updated successfully!'}`;
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = `<i class="fas fa-triangle-exclamation me-2"></i>${result.message || 'An error occurred during verification processing.'}`;
        }
    } catch (error) {
        console.error("Pipeline connectivity error:", error);
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = '<i class="fas fa-server me-2"></i>Network Gateway error occurred. Infrastructure could not write changes to target node.';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-cloud-arrow-up me-2"></i>Update Records';
    }
}
</script>
</body>
</html>