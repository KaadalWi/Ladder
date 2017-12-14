<?php
   require("classPlayer.php");
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
   require("dbConnection.php");

   try
   {
      // Prepare an insertion query
      $reportGame = $db->prepare("
         insert into game (winner, loser, played, number, winner_score, loser_score)
            values (:winner, :loser, :played, :number, :winner_score, :loser_score);");

      // Prepare get from match after inserts
      $match = $db->prepare("
         select winner, loser, p_winner.rank as winner_rank, p_loser.rank as loser_rank
            from match_view
            join player as p_winner on winner = p_winner.username
            join player as p_loser on loser = p_loser.username
            where (winner = :p1 or winner = :p2)
               and (loser = :p1 or loser = :p2)
               and played = :played;");

      // Prepare set of players to change ranks
      $toUpdate = $db->prepare("
         select username, rank from player
            where (rank - :loser_rank) < (:winner_rank - :loser_rank)
               and (rank - :loser_rank) >= 0;");
      $updatePlayer = $db->prepare("
         update player set rank = :rank
            where username = :username;");

      // Delete challenge
      $deleteChallenge = $db->prepare("
         delete from challenge
            where challenger = :challenger and challengee = :challengee
            and scheduled = :scheduled;");

      // Execute inserts
      $winner;
      $winner_score;
      $loser;
      $loser_score;

      $db->beginTransaction();
      if (((int) $_POST["p1_score_1"]) > ((int) $_POST["p2_score_1"]))
      {
         $winner = $_POST["p1"];
         $winner_score = (int) $_POST["p1_score_1"];
         $loser = $_POST["p2"];
         $loser_score = (int) $_POST["p2_score_1"];
      }
      else
      {
         $winner = $_POST["p2"];
         $winner_score = (int) $_POST["p2_score_1"];
         $loser = $_POST["p1"];
         $loser_score = (int) $_POST["p1_score_1"];
      }
      $reportGame->execute(array(":winner"=>htmlspecialchars_decode($winner),
         ":loser"=>htmlspecialchars_decode($loser),
         ":played"=>htmlspecialchars_decode($_POST["scheduled"]), ":number"=>1,
         ":winner_score"=>htmlspecialchars_decode($winner_score),
         ":loser_score"=>htmlspecialchars_decode($loser_score)));
      // Check the results - should be one row
      if ($reportGame->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         $db->rollBack();
         echo '
            <script type="text/javascript">
               <!--
               alert("Report failed.");
               window.history.back();
               // -->
            </script>';
         $reportGame->closeCursor();
         $match->closeCursor();
         $toUpdate->closeCursor();
         $updatePlayer->closeCursor();
         die();
      }

      if (((int) $_POST["p1_score_2"]) > ((int) $_POST["p2_score_2"]))
      {
         $winner = $_POST["p1"];
         $winner_score = (int) $_POST["p1_score_2"];
         $loser = $_POST["p2"];
         $loser_score = (int) $_POST["p2_score_2"];
      }
      else
      {
         $winner = $_POST["p2"];
         $winner_score = (int) $_POST["p2_score_2"];
         $loser = $_POST["p1"];
         $loser_score = (int) $_POST["p1_score_2"];
      }
      $reportGame->execute(array(":winner"=>htmlspecialchars_decode($winner),
         ":loser"=>htmlspecialchars_decode($loser),
         ":played"=>htmlspecialchars_decode($_POST["scheduled"]), ":number"=>2,
         ":winner_score"=>htmlspecialchars_decode($winner_score),
         ":loser_score"=>htmlspecialchars_decode($loser_score)));
      // Check the results - should be one row
      if ($reportGame->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         $db->rollBack();
         echo '
            <script type="text/javascript">
               <!--
               alert("Report failed.");
               window.history.back();
               // -->
            </script>';
         $reportGame->closeCursor();
         $match->closeCursor();
         $toUpdate->closeCursor();
         $updatePlayer->closeCursor();
         die();
      }

      if (((int) $_POST["p1_score_3"]) > ((int) $_POST["p2_score_3"]))
      {
         $winner = $_POST["p1"];
         $winner_score = (int) $_POST["p1_score_3"];
         $loser = $_POST["p2"];
         $loser_score = (int) $_POST["p2_score_3"];
      }
      else
      {
         $winner = $_POST["p2"];
         $winner_score = (int) $_POST["p2_score_3"];
         $loser = $_POST["p1"];
         $loser_score = (int) $_POST["p1_score_3"];
      }
      $reportGame->execute(array(":winner"=>htmlspecialchars_decode($winner),
         ":loser"=>htmlspecialchars_decode($loser),
         ":played"=>htmlspecialchars_decode($_POST["scheduled"]), ":number"=>3,
         ":winner_score"=>htmlspecialchars_decode($winner_score),
         ":loser_score"=>htmlspecialchars_decode($loser_score)));
      // Check the results - should be one row
      if ($reportGame->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         $db->rollBack();
         echo '
            <script type="text/javascript">
               <!--
               alert("Report failed.");
               window.history.back();
               // -->
            </script>';
         $reportGame->closeCursor();
         $match->closeCursor();
         $toUpdate->closeCursor();
         $updatePlayer->closeCursor();
         die();
      }

      if ($_POST["p1_score_4"])
      {
         if (((int) $_POST["p1_score_4"]) > ((int) $_POST["p2_score_4"]))
         {
            $winner = $_POST["p1"];
            $winner_score = (int) $_POST["p1_score_4"];
            $loser = $_POST["p2"];
            $loser_score = (int) $_POST["p2_score_4"];
         }
         else
         {
            $winner = $_POST["p2"];
            $winner_score = (int) $_POST["p2_score_4"];
            $loser = $_POST["p1"];
            $loser_score = (int) $_POST["p1_score_4"];
         }
         $reportGame->execute(array(":winner"=>htmlspecialchars_decode($winner),
            ":loser"=>htmlspecialchars_decode($loser),
            ":played"=>htmlspecialchars_decode($_POST["scheduled"]), ":number"=>4,
            ":winner_score"=>htmlspecialchars_decode($winner_score),
            ":loser_score"=>htmlspecialchars_decode($loser_score)));
         // Check the results - should be one row
         if ($reportGame->rowCount() != 1)
         {
            // Can check the status to see if we violated the unique
            // constraint on username and alert the user
            $db->rollBack();
            echo '
               <script type="text/javascript">
                  <!--
                  alert("Report failed.");
                  window.history.back();
                  // -->
               </script>';
            $reportGame->closeCursor();
            $match->closeCursor();
            $toUpdate->closeCursor();
            $updatePlayer->closeCursor();
            die();
         }

         if ($_POST["p1_score_5"])
         {
            if (((int) $_POST["p1_score_5"]) > ((int) $_POST["p2_score_5"]))
            {
               $winner = $_POST["p1"];
               $winner_score = (int) $_POST["p1_score_5"];
               $loser = $_POST["p2"];
               $loser_score = (int) $_POST["p2_score_5"];
            }
            else
            {
               $winner = $_POST["p2"];
               $winner_score = (int) $_POST["p2_score_5"];
               $loser = $_POST["p1"];
               $loser_score = (int) $_POST["p1_score_5"];
            }
            $reportGame->execute(array(":winner"=>htmlspecialchars_decode($winner),
               ":loser"=>htmlspecialchars_decode($loser),
               ":played"=>htmlspecialchars_decode($_POST["scheduled"]), ":number"=>5,
               ":winner_score"=>htmlspecialchars_decode($winner_score),
               ":loser_score"=>htmlspecialchars_decode($loser_score)));
            // Check the results - should be one row
            if ($reportGame->rowCount() != 1)
            {
               // Can check the status to see if we violated the unique
               // constraint on username and alert the user
               $db->rollBack();
               echo '
                  <script type="text/javascript">
                     <!--
                     alert("Report failed.");
                     window.history.back();
                     // -->
                  </script>';
               $reportGame->closeCursor();
               $match->closeCursor();
               $toUpdate->closeCursor();
               $updatePlayer->closeCursor();
               die();
            }
         }
      }

      // Get the match results
      $match->execute(array(":p1"=>htmlspecialchars_decode($_POST["p1"]),
         ":p2"=>htmlspecialchars_decode($_POST["p2"]),
         ":played"=>htmlspecialchars_decode($_POST["scheduled"])));

      // Check the results - should be one row
      if ($match->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         $db->rollBack();
         echo '
            <script type="text/javascript">
               <!--
               alert("Report failed.");
               window.history.back();
               // -->
            </script>';
         $reportGame->closeCursor();
         $match->closeCursor();
         $toUpdate->closeCursor();
         $updatePlayer->closeCursor();
         die();
      }

      // Check which player won
      $matchResults = $match->fetch(PDO::FETCH_ASSOC);
      if ($matchResults["winner_rank"] > $matchResults["loser_rank"])
      {
         $toUpdate->execute(array(":loser_rank"=>$matchResults["loser_rank"],
            ":winner_rank"=>$matchResults["winner_rank"]));

         foreach ($toUpdate->fetchAll(PDO::FETCH_ASSOC) as $rowResult)
         {
            $updatePlayer->execute(array(":username"=>$rowResult["username"],
               ":rank"=>($rowResult["rank"] + 1)));
            // Check the results - should be one row
            if ($updatePlayer->rowCount() != 1)
            {
               // Can check the status to see if we violated the unique
               // constraint on username and alert the user
               $db->rollBack();
               echo '
                  <script type="text/javascript">
                     <!--
                     alert("Report failed.");
                     window.history.back();
                     // -->
                  </script>';
               $reportGame->closeCursor();
               $match->closeCursor();
               $toUpdate->closeCursor();
               $updatePlayer->closeCursor();
               die();
            }
         }

         $updatePlayer->execute(array(":username"=>$matchResults["winner"],
            ":rank"=>$matchResults["loser_rank"]));

         // Check the results - should be one row
         if ($updatePlayer->rowCount() != 1)
         {
            // Can check the status to see if we violated the unique
            // constraint on username and alert the user
            $db->rollBack();
            echo '
               <script type="text/javascript">
                  <!--
                  alert("Report failed.");
                  window.history.back();
                  // -->
               </script>';
            $reportGame->closeCursor();
            $match->closeCursor();
            $toUpdate->closeCursor();
            $updatePlayer->closeCursor();
            die();
         }
      }

      // Delete completed challenge
      $deleteChallenge->execute(array(":challenger"=>htmlspecialchars_decode($_POST["p1"]),
         ":challengee"=>htmlspecialchars_decode($_POST["p2"]),
         ":scheduled"=>htmlspecialchars_decode($_POST["scheduled"])));

      echo '<script>alert("SUCCESS!!");</script>';
   }
   catch (PDOException $e)
   {
      $db->rollBack();
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Report failed. Please try again.");
            window.history.back();
            // -->
         </script>';
      $reportGame->closeCursor();
      $match->closeCursor();
      $toUpdate->closeCursor();
      $updatePlayer->closeCursor();
      $db->close();
      die();
   }

   $db->commit();
   $reportGame->closeCursor();
   $match->closeCursor();
   $toUpdate->closeCursor();
   $updatePlayer->closeCursor();
   
   // Success - send them somewhere! 
   echo '
      <script type="text/javascript">
         <!--
         window.location="/games.php#games";
         // -->
      </script>';
?>