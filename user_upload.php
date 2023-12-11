<?php
// Function to create/rebuild the users table
function createUsersTable($host, $username, $password, $database) {
    // Connect to MySQL
    $conn = new mysqli($host, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }

    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        surname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        UNIQUE KEY unique_email (email) 
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Users table created successfully.\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
        exit(1);
    }
}

// Parse command line options
$options = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

// Check for --help option
if (isset($options['help'])) {
    echo "Usage: php user_upload.php\n 
    --file [csv_file_name]\t name of csv file to be parsed\n
    --create_table\t\t create users table and no action will be taken\n
    --dry_run\t\t\t run the script without inserting into database
    -u\t\t\t\t MySQL username
    -p\t\t\t\t MySQL password
    -h\t\t\t\t MySQL host\n";
    exit(0);
}

// Get Database credentials
$host = isset($options['h']) ? $options['h'] : 'localhost';
$username = isset($options['u']) ? $options['u'] : 'root';
$password = isset($options['p']) ? $options['p'] : '';
$database = 'test';

// Check for --create_table option
if (isset($options['create_table'])) {
    createUsersTable($host, $username, $password, $database);
    exit(0);
}
?>
