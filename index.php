<?php
// Data file path
$dataFile = 'data.json';

// Function to read all data
function readData($dataFile) {
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode([]));
    }
    return json_decode(file_get_contents($dataFile), true);
}

// Function to write data
function writeData($dataFile, $data) {
    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

// Fetch all data
$data = readData($dataFile);

// Handle Add/Update/Delete operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($action === 'add') {
        // Add new data
        $newData = [
            'first_name' => $_POST['first_name'],
            'last_name'  => $_POST['last_name'],
            'email'      => $email,
            'address'    => $_POST['address'],
            'mobile'     => $_POST['mobile']
        ];

        // Check for duplicate email
        foreach ($data as $entry) {
            if ($entry['email'] === $email) {
                $error = "Email must be unique!";
                break;
            }
        }

        if (!isset($error)) {
            $data[] = $newData;
            writeData($dataFile, $data);
            header("Location: index.php");
            exit;
        }
    } elseif ($action === 'update') {
        // Update data
        foreach ($data as &$entry) {
            if ($entry['email'] === $email) {
                $entry['first_name'] = $_POST['first_name'];
                $entry['last_name']  = $_POST['last_name'];
                $entry['address']    = $_POST['address'];
                $entry['mobile']     = $_POST['mobile'];
                break;
            }
        }
        writeData($dataFile, $data);
        header("Location: index.php");
        exit;
    } elseif ($action === 'delete') {
        // Delete data
        $data = array_filter($data, fn($entry) => $entry['email'] !== $email);
        writeData($dataFile, $data);
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP CRUD with JSON</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">PHP CRUD with JSON</h2>

    <!-- Add / Edit Form -->
    <div class="card mb-4">
        <div class="card-header">Add / Edit Entry</div>
        <div class="card-body">
            <form method="POST" action="index.php">
                <input type="hidden" name="action" value="add" id="form-action">
                <!-- <input type="hidden" name="email" id="email"> -->

                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" name="address" id="address" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile</label>
                    <input type="text" name="mobile" id="mobile" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Mobile</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $entry): ?>
            <tr>
                <td><?= htmlspecialchars($entry['first_name']) ?></td>
                <td><?= htmlspecialchars($entry['last_name']) ?></td>
                <td><?= htmlspecialchars($entry['email']) ?></td>
                <td><?= htmlspecialchars($entry['address']) ?></td>
                <td><?= htmlspecialchars($entry['mobile']) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editEntry(<?= htmlspecialchars(json_encode($entry)) ?>)">Edit</button>
                    <form method="POST" action="index.php" style="display:inline-block;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($entry['email']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function editEntry(entry) {
        document.getElementById('form-action').value = 'update';
        document.getElementById('email').value = entry.email;
        document.getElementById('first_name').value = entry.first_name;
        document.getElementById('last_name').value = entry.last_name;
        document.getElementById('address').value = entry.address;
        document.getElementById('mobile').value = entry.mobile;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
