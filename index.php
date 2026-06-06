<?php
// Database Configurations
$db_host = "travelapp-database.cklo6m6m05cp.us-east-1.rds.amazonaws.com";
$db_user = "admin";
$db_pass = "travelapp-admin123";
$db_name = "travelapp";

// Connect to the RDS instance
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check database connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// Process the form submission (POST Process)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['publish'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $story = $conn->real_escape_string($_POST['story']);
    
    if (!empty($title) && !empty($story)) {
        $sql = "INSERT INTO entries (title, story) VALUES ('$title', '$story')";
        $conn->query($sql);
        // Refresh page to show new data
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CloudVoyage - Dynamic Travel Journal</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; }
        header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 2rem; text-align: center; }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
        .upload-section { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; }
        .card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-content { padding: 1.5rem; }
        .btn { background-color: #007bff; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; }
        input[type="text"], textarea { width: 100%; padding: 0.5rem; margin: 0.5rem 0 1rem 0; box-sizing: border-box; }
    </style>
</head>
<body>
    <header>
        <h1>🌍 CloudVoyage</h1>
        <p>A Live, Dynamic AWS Cloud-Hosted Travel Journal</p>
    </header>
    <div class="container">
        <div class="upload-section">
            <form method="POST" action="index.php">
                <h3>Create a New Travel Entry</h3>
                <label>Destination Title</label>
                <input type="text" name="title" required placeholder="e.g., Summer in Kyoto, Japan">
                <label>Journal Story</label>
                <textarea rows="3" name="story" required placeholder="Share your experience..."></textarea>
                <button type="submit" name="publish" class="btn">Publish to AWS RDS</button>
            </form>
        </div>
        
        <h2>Recent Live Journals</h2>
        <div class="grid">
            <?php
            $result = $conn->query("SELECT * FROM entries ORDER BY created_at DESC");
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=500" alt="Travel Image">';
                    echo '<div class="card-content">';
                    echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['story']) . '</p>';
                    echo '<small style="color: #888;">Stored via RDS at: ' . $row['created_at'] . '</small>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No journals published yet. Try typing above to push live data to your cloud database!</p>';
            }
            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>