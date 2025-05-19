<?php
include 'config.php';

$title = $author = $genre = $year = $quantity = "";
$edit_mode = false;
$id = 0;
$book_cover = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["book_title"];
    $author = $_POST["author"];
    $genre = $_POST["genre"];
    $year = $_POST["year"];
    $quantity = $_POST["quantity"];

    

    if (isset($_FILES["book_cover"]) && $_FILES["book_cover"]["tmp_name"]) {
        $image = addslashes(file_get_contents($_FILES["book_cover"]["tmp_name"]));
    }

    if (!empty($_POST["id"])) {
        // Update
        $id = $_POST["id"];
        $sql = "UPDATE library SET book_title='$title', author_name='$author', genre='$genre', publication_year='$year', quantity='$quantity'";
        if (isset($image)) $sql .= ", book_cover='$image'";
        $sql .= " WHERE id=$id";
        $conn->query($sql);
    } else {
        // Create
        $sql = "INSERT INTO library (book_title, author_name, genre, publication_year, quantity, book_cover) 
                VALUES ('$title', '$author', '$genre', '$year', '$quantity', '$image')";
        $conn->query($sql);
    }
    header("Location: index.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    $conn->query("DELETE FROM library WHERE id=$id");
    header("Location: index.php");
    exit();
}

if (isset($_GET["edit"])) {
    $edit_mode = true;
    $id = $_GET["edit"];
    $result = $conn->query("SELECT * FROM library WHERE id=$id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row["book_title"];
        $author = $row["author_name"];
        $genre = $row["genre"];
        $year = $row["publication_year"];
        $quantity = $row["quantity"];
        $book_cover = $row["book_cover"];
    }
}

$result = $conn->query("SELECT * FROM library");
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 500px;
        }
        form input, form select {
            display: block;
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            background-color: #fff;
            border-collapse: collapse;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #007bff;
            color: white;
        }
        table img {
            border-radius: 5px;
        }
        a {
            text-decoration: none;
            color: #007bff;
            margin-right: 8px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <title>Library System</title>
</head>
<body>
    <h2><?= $edit_mode ? "Edit Book" : "Add New Book" ?></h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $edit_mode ? $id : '' ?>">
        <label>Title:</label><input type="text" name="book_title" value="<?= $title ?>"><br>
        <label>Author:</label><input type="text" name="author" value="<?= $author ?>"><br>
        <label>Genre:</label>
        <select name="genre">
            <option <?= $genre == 'Fiction' ? 'selected' : '' ?>>Fiction</option>
            <option <?= $genre == 'Non-fiction' ? 'selected' : '' ?>>Non-fiction</option>
            <option <?= $genre == 'Biography' ? 'selected' : '' ?>>Biography</option>
            <option <?= $genre == 'Science' ? 'selected' : '' ?>>Science</option>
        </select><br>
        <label>Publication Year:</label><input type="month" name="year" value="<?= isset($year) ? $year : '' ?>" min="1900-01" max="2099-12"><br>
        <label>Quantity:</label><input type="number" name="quantity" value="<?= $quantity ?>"><br>
        <label>Book Cover:</label><input type="file" name="book_cover"><br>
        <button type="submit"><?= $edit_mode ? "Update" : "Add" ?> Book</button>
    </form>

    <h2>All Books</h2>
    <table border="1">
        <tr>
            <th>Title</th><th>Author</th><th>Genre</th><th>Year</th><th>Qty</th><th>Cover</th><th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row["book_title"] ?></td>
                <td><?= $row["author_name"] ?></td>
                <td><?= $row["genre"] ?></td>
                <td><?= $row["publication_year"] ?></td>
                <td><?= $row["quantity"] ?></td>
                <td>
                    <?php if ($row["book_cover"]): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($row["book_cover"]) ?>" width="50">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?edit=<?= $row["id"] ?>">Edit</a> |
                    <a href="?delete=<?= $row["id"] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
