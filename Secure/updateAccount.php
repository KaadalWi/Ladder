<?php
   require("../classPlayer.php");
   session_start();
   if (!isset($_SESSION["user"]))
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("Welcome! Please log in.");
            window.location="/";
            // -->
         </script>';
      die();
   }
   require("../dbConnection.php");
   
   try
   {
      $query = $db->prepare("select * from player where username = :username;");
      $query->execute(array(":username"=>$_SESSION['user']->username));
   }
   catch (PDOException $e)
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Update failed. Please try again.");
            window.history.back();
            // -->
         </script>';
      $query->closeCursor();
      $db->close();
      die();
   }
   
   $player = $query->fetch(PDO::FETCH_ASSOC);
   if (sha1($_POST['password']) == $player['password'])
   {
      try
      {
         $db->beginTransaction();

         if ($_POST["email"] != "" && !is_null($_POST["email"]))
         {
            $query = $db->prepare("update player set email = :email where username = :username;");
            $query->execute(array(":email"=>$_POST["email"], ":username"=>$_SESSION['user']->username));

            if ($query->rowCount() != 1)
            {
               echo '
                  <script type="text/javascript">
                     <!--
                     alert("Update failed.");
                     window.history.back();
                     // -->
                  </script>';
               $db->rollBack();
               $query->closeCursor();
               die();
            }
         }
         else
         {
            $_POST["email"] = $_SESSION["user"]->email;
         }

         if ($_POST["phone"] != "" && !is_null($_POST["phone"]))
         {
            $query = $db->prepare("update player set phone = :phone where username = :username;");
            $query->execute(array(":phone"=>$_POST["phone"], ":username"=>$_SESSION['user']->username));

            if ($query->rowCount() != 1)
            {
               echo '
                  <script type="text/javascript">
                     <!--
                     alert("Update failed.");
                     window.history.back();
                     // -->
                  </script>';
               $db->rollBack();
               $query->closeCursor();
               die();
            }
         }
         else
         {
            $_POST["phone"] = $_SESSION["user"]->phone;
         }

         if ($_POST["password_new"] != "" && !is_null($_POST["password_new"]))
         {
            $query = $db->prepare("update player set password = :password where username = :username;");
            $query->execute(array(":password"=>sha1($_POST["password_new"]), ":username"=>$_SESSION['user']->username));

            if ($query->rowCount() != 1)
            {
               echo '
                  <script type="text/javascript">
                     <!--
                     alert("Update failed.");
                     window.history.back();
                     // -->
                  </script>';
               $db->rollBack();
               $query->closeCursor();
               die();
            }
         }

         $db->commit();
         $_SESSION["user"]->email = $_POST["email"];
         $_SESSION["user"]->phone = $_POST["phone"];
      }
      catch (PDOException $e)
      {
         echo '
            <script type="text/javascript">
               <!--
               alert("FATAL ERROR: Update failed. Please try again.");
               window.history.back();
               // -->
            </script>';
         $db->rollBack();
         $query->closeCursor();
         $db->close();
         die();
      }
   }
   else
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("Incorrect password. Please try again.");
            window.history.back();
            // -->
         </script>';
      $query->closeCursor();
      die();
   }

   $query->closeCursor();
   
   // Success - send them somewhere! 
   echo '
      <script type="text/javascript">
         <!--
         window.location="/account.php#account";
         // -->
      </script>';
?>