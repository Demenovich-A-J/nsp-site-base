<?php
if(isset($_POST['submit'])){

   $name = $_POST['name'];
   $email = $_POST['email'];
   $age = $_POST['age'];

   echo "name : ".$name."<br>";
   echo "email : ".$email."<br>";
   echo "age : ".$age;
}

?>
