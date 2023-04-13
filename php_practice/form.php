<html>

<body>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        Name: <input type="text" name="name" id="">
        <input type="submit" value="Submit">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_REQUEST['name'];
        if (!$name) echo "no name";
        else echo $name;
    }
    ?>


</body>



</html>