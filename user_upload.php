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

        return $conn;
    } else {
        echo "Error creating table: " . $conn->error . "\n";
        exit(1);
    }
}

// Capitalize the first letter of each word
function capitalize($str) {
    return ucwords(strtolower($str));
}

// Validate email address
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
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
$password = isset($options['p']) ? $options['p'] : '1234';
$database = 'test';

// Check for --create_table option, here we will just create table only, no more action required.
if (isset($options['create_table'])) {
    createUsersTable($host, $username, $password, $database);
    exit(0);
}

// Start processing file
if (!isset($options['file'])) {
    echo "Error: Missing --file option. Use --help for usage information.\n";
    exit(1);
}

// Check for --dry_run option
$dryRun = isset($options['dry_run']);

//Get file path
$csvFilePath = $options['file'];

// Open and read the CSV file
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Create users table
    $conn = createUsersTable($host, $username, $password, $database);

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $name = $conn->real_escape_string(capitalize($data[0]));
        $surname = $conn->real_escape_string(capitalize($data[1]));
        $email = filter_var(strtolower($data[2]), FILTER_SANITIZE_EMAIL);
    
        // Validate email address
        if (validateEmail($email)) {
            if (!$dryRun) {
                $email = $conn->real_escape_string($email);
                $sql = "INSERT INTO users (name, surname, email) VALUES ('$name', '$surname', '$email')";

                try {
                    $conn->query($sql);
                }
                catch (Exception $e) {
                    echo "Error inserting " . $name . ' ' . $surname . ": " . $e->getMessage() . "\n";
                }
            } else {
                echo "Dry run: " . $name . ' ' . $surname . " is not inserted into the database.\n";
            }
        } else {
            echo "Invalid email format: $email. Skipping record.\n";
        }
    }
    echo "Finished";
    fclose($handle);
    
    // Close the database connection
    $conn->close();
} else {
    // Throw error if file not open.
    echo "Error opening file: $csvFilePath\n";
}

?>
