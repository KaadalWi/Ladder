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
      $ladderStats = $db->prepare("select * from stats_view;");
      $ladderStats->execute();
      $challenging = $db->prepare("
         select * from potential_challengees_view
            where username = :username");
      $challenging->execute(array(":username"=>$_SESSION['user']->username));
   }
   catch (PDOException $e)
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Data failed to load.");
            window.location.reload();
            // -->
         </script>';
      $db->close();
      die();
   }
?>

<!DOCTYPE html>
<html>
   <head>
      <title>The Ladder</title>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css" />
      <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
      <link rel="icon" href="/icons/ladder_icon.jpg" type="image/x-icon" />
      <link rel="shortcut icon" href="/icons/ladder_icon.jpg" type="image/x-icon" />
      <style>
         body, h1,h2,h3,h4,h5,h6 {font-family: "Montserrat", sans-serif}
         .w3-row-padding img {margin-bottom: 12px}
         /* Set the width of the sidebar to 120px */
         .w3-sidebar {width: 120px;background: #222;}
         /* Add a left margin to the "page content" that matches the width of the sidebar (120px) */
         #main {margin-left: 120px}
         /* Remove margins from "page content" on small screens */
         @media only screen and (max-width: 600px) {#main {margin-left: 0}}
      </style>
   </head>
   <body class="w3-black">

      <!-- Icon Bar (Sidebar - hidden on small screens) -->
      <nav class="w3-sidebar w3-bar-block w3-small w3-hide-small w3-center">
         <!-- Avatar image in top left corner -->
         <a href="/ladder.php" class="w3-bar-item w3-button w3-padding-large w3-black">
            <img src="/icons/ladder_icon.jpg" style="width:100%">
            <p>LADDER</p>
         </a>
         <a class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-envelope w3-xxlarge"></i>
            <p>CHALLENGES</p>
         </a>
         <a class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-newspaper-o w3-xxlarge"></i>
            <p>GAMES</p>
         </a>
         <a class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-table w3-xxlarge"></i>
            <p>MATCHES</p>
         </a>
         <a class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-user w3-xxlarge"></i>
            <p>ACCOUNT</p>
         </a>
         <a href="/logout.php" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-arrow-circle-o-left w3-xxlarge"></i>
            <p>LOGOUT</p>
         </a>
      </nav>

      <!-- Navbar on small screens (Hidden on medium and large screens) -->
      <div class="w3-top w3-hide-large w3-hide-medium" id="myNavbar">
         <div class="w3-bar w3-black w3-opacity w3-center w3-small">
            <a href="/ladder.php" class="w3-bar-item w3-button w3-grey" style="width:16.5% !important">HOME</a>
            <a class="w3-bar-item w3-button" style="width:15% !important">CHAL</a>
            <a class="w3-bar-item w3-button" style="width:16% !important">GAME</a>
            <a class="w3-bar-item w3-button" style="width:18.5% !important">MATCH</a>
            <a class="w3-bar-item w3-button" style="width:15% !important">ACCT</a>
            <a href="/logout.php" class="w3-bar-item w3-button" style="width:19% !important">LGOUT</a>
         </div>
      </div>

      <!-- Page Content -->
      <div class="w3-padding-large" id="main">
         <!-- Header/Home -->
         <header class="w3-container w3-padding-32 w3-center w3-black" id="home">
            <h1 class="w3-jumbo"><span class="w3-xxlarge">The <br /></span>Ladder</h1>
            <p>Veni. Vidi. Vici.</p>
            <img src="/icons/ladder_icon.jpg" class="w3-image" width="992" height="1108">
         </header>

         <!-- Ladder -->
         <div class="w3-content w3-center w3-text-grey w3-padding-64" id="ladder">
            <h2 class="w3-text-light-grey">
               Welcome to the Ladder,
               <?php echo $_SESSION['user']->name; ?>
            </h2>
            <div class="w3-large w3-justified">
               <!--<p>
                  Challenge other players.<br />
                  Play intense matches.<br />
                  Rise through the ranks of heroes.<br />
                  Reach the Ladder's peak.
               </p>-->
               <table class="w3-table-all w3-centered w3-responsive w3-hoverable">
                  <tr class="w3-black">
                     <th>Rank</th>
                     <th>Name</th>
                     <th>Match Win Ratio</th>
                     <th>Game Win Ratio</th>
                     <th>Avg Win Margin</th>
                     <th>Match Loss Ratio</th>
                     <th>Game Loss Ratio</th>
                     <th>Avg Loss Margin</th>
                  </tr>
                  <?php
                     $rowColours = array("w3-dark-grey", "w3-grey");
                     $row = 0;
                     foreach($ladderStats->fetchAll(PDO::FETCH_ASSOC) as $resultRow)
                     {
                        $rowColour = $rowColours[$row % 2];
                        echo "<tr class='$rowColour'>" . PHP_EOL;
                        echo '<td>' . $resultRow['rank'] . '</td>' . PHP_EOL;
                        echo '<td>' . $resultRow['name'] . '</td>' . PHP_EOL;
                        echo '<td>' . sprintf("%.2f", $resultRow['match_win_percentage']) . '</td>' . PHP_EOL;
                        echo '<td>' . sprintf("%.2f", $resultRow['game_win_percentage']) . '</td>' . PHP_EOL;
                        echo '<td>' . sprintf("%.2f", $resultRow['average_win_margin']) . '</td>' . PHP_EOL;
                        echo '<td>' . sprintf("%.2f", $resultRow['match_loss_percentage']) . '</td>' . PHP_EOL;
                        echo '<td>' . sprintf("%.2f", $resultRow['game_loss_percentage']) . '</td>' . PHP_EOL;
                        echo '<td>' . sprintf("%.2f", $resultRow['average_loss_margin']) . '</td>' . PHP_EOL;
                        echo '</tr>' . PHP_EOL;
                        $row++;
                     }

                     $ladderStats->closeCursor();
                     $challenging->closeCursor();
                  ?>
               </table>
            </div>
         </div>

         <!-- About -->
         <div class="w3-content w3-center w3-text-grey w3-padding-64" id="about">
            <h2 class="w3-text-light-grey">About</h2>
            <div class="w3-large">
               <p>
                  Games are won by the first player to score 15 points and to win by at least 2.
                  Matches consist of the best of 5 games between players.
                  If the low ranking player wins, they take the high rank; all players who were
                  between the low ranking player and high ranking player, including the match
                  loser, lose a rank.
                  Challenges may be issued to players who are up to 3 ranks above and who are
                  not involved in a prior engagement. 
               </p>
               <p>
                  We are not liable for any injuries sustained while playing.
               </p>
            </div>
         </div>

         <!-- Footer -->
         <footer class="w3-content w3-padding-64 w3-text-light-grey w3-center w3-xlarge">
            <p class="w3-medium">
               <a href="mailto:kwilcox15@georgefox.edu" target="_blank" class="w3-hover-text-green">Contact us</a>
            </p>
            <p class="w3-medium">
               Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank" class="w3-hover-text-green">w3.css</a>
            </p>
         <!-- End footer -->
         </footer>

      <!-- END PAGE CONTENT -->
      </div>

   </body>
</html>