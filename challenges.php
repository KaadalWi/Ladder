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
      $challengesReceived = $db->prepare("
         select * from challenge
            join player on challenger = username
            where challengee = :challengee and accepted isnull;");
      $challengesReceived->execute(array(":challengee"=>$_SESSION['user']->username));
      $challengesSent = $db->prepare("
         select * from challenge
            join player on challengee = username
            where challenger = :challenger and accepted isnull;");
      $challengesSent->execute(array(":challenger"=>$_SESSION['user']->username));
      $challengeAccepted = $db->prepare("
         select challenger, challengee, issued, accepted, scheduled,
            p1.username as challenger_username, p1.name as challenger_name,
            p2.username as challengee_username, p2.name as challengee_name
            from challenge
            join player as p1 on challenger = p1.username
            join player as p2 on challengee = p2.username
            where (challenger = :username or challengee = :username)
               and not (accepted isnull);");
      $challengeAccepted->execute(array(":username"=>$_SESSION['user']->username));
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
      <script type="text/javascript" src="/challenge/respondToChallenge.js"></script>
      <script type="text/javascript" src="/challenge/reportValidation.js"></script>
      <script type="text/javascript" src="/start/popups.js"></script>
   </head>
   <body class="w3-black">

      <!-- Icon Bar (Sidebar - hidden on small screens) -->
      <nav class="w3-sidebar w3-bar-block w3-small w3-hide-small w3-center">
         <!-- Avatar image in top left corner -->
         <a href="/ladder.php#ladder" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <img src="/icons/ladder_icon.jpg" style="width:100%">
            <p>LADDER</p>
         </a>
         <a href="/challenges.php#challenges" class="w3-bar-item w3-button w3-padding-large w3-black">
            <i class="fa fa-envelope w3-xxlarge"></i>
            <p>CHALLENGES</p>
         </a>
         <a href="/games.php#games" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-newspaper-o w3-xxlarge"></i>
            <p>GAMES</p>
         </a>
         <a href="/matches.php#matches" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-table w3-xxlarge"></i>
            <p>MATCHES</p>
         </a>
         <a href="/account.php#account" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
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
            <a href="/ladder.php#ladder" class="w3-bar-item w3-button" style="width:16.5% !important">HOME</a>
            <a href="/challenges.php#challenges" class="w3-bar-item w3-button w3-grey" style="width:15% !important">CHAL</a>
            <a href="/games.php#games" class="w3-bar-item w3-button" style="width:16% !important">GAME</a>
            <a href="/matches.php#matches" class="w3-bar-item w3-button" style="width:18.5% !important">MATCH</a>
            <a href="/account.php#account" class="w3-bar-item w3-button" style="width:15% !important">ACCT</a>
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

         <!-- Challenges -->
         <div class="w3-content w3-center w3-text-grey w3-padding-64" id="challenges">
            <div class="w3-large w3-justified">
               <?php
                  $rowColours = array("w3-dark-grey", "w3-grey");

                  if ($challenges = $challengeAccepted->fetch(PDO::FETCH_ASSOC))
                  {
                     $row = 0;
                     $rowColour = $rowColours[$row % 2];
                     echo "
               <h2 class='w3-text-light-grey'>Accepted Challenge</h2>
               <table class='w3-table-all w3-centered w3-hoverable w3-hide-small'>
                  <tr class='w3-black'>
                     <th>Challenger</th>
                     <th>Challengee</th>
                     <th>Scheduled For</th>
                  </tr>
                  <tr class='$rowColour'>
                     <td>" . htmlspecialchars($challenges['challenger_name']) . "</td>
                     <td>" . htmlspecialchars($challenges['challengee_name']) . "</td>
                     <td>" . htmlspecialchars($challenges['scheduled']) . "</td>
                  </tr>
               </table>

               <table class='w3-table-all w3-centered w3-responsive w3-hoverable w3-hide-medium w3-hide-large'>
                  <tr class='w3-black'>
                     <th>Challenger</th>
                     <th>Challengee</th>
                     <th>Scheduled For</th>
                  </tr>
                  <tr class='$rowColour'>
                     <td>" . htmlspecialchars($challenges['challenger_name']) . "</td>
                     <td>" . htmlspecialchars($challenges['challengee_name']) . "</td>
                     <td>" . htmlspecialchars($challenges['scheduled']) . "</td>
                  </tr>
               </table>
               <br />
               <a class='w3-bar-item w3-button w3-padding-large w3-hover-black'
                  onclick='openPopup(\"report_popup\")'>
                  <button class='w3-button w3-light-grey w3-padding-large w3-section'>
                     <i class='fa fa-book'></i> REPORT SCORES
                  </button>
               </a>";
                  }
                  else
                  {
                     echo "
               <h2 class='w3-text-light-grey'>Challenges Received</h2>
               <form method='post' id='challengeResponse' name='challengeResponse'>
                  <input type='text' style='display: none;' id='challenger' name='challenger' />
                  <input type='text' style='display: none;' id='scheduled' name='scheduled' />
                  <table class='w3-table-all w3-centered w3-hoverable w3-hide-small'>
                     <tr class='w3-black'>
                        <th>Challenger</th>
                        <th>Issued On</th>
                        <th>Scheduled For</th>
                        <th>Respond To Challenge</th>
                     </tr>";
                     $challenges = $challengesReceived->fetchAll(PDO::FETCH_ASSOC);
                     $row = 0;
                     foreach($challenges as $resultRow)
                     {
                        $rowColour = $rowColours[$row % 2];
                        echo "
                     <tr class='$rowColour'>
                        <td>" . htmlspecialchars($resultRow['name']) . "</td>
                        <td>" . htmlspecialchars($resultRow['issued']) . "</td>
                        <td>" . htmlspecialchars($resultRow['scheduled']) . "</td>
                        <td>
                           <button class='w3-button w3-tiny w3-light-grey w3-padding-small' type='submit'
                              onclick='submitResponse(\"" . htmlspecialchars($resultRow['challenger'])
                           . "\", \"" . htmlspecialchars($resultRow['scheduled']) . "\")'
                              formaction='/acceptChallenge.php' >
                              Accept
                           </button>
                           <button class='w3-button w3-tiny w3-light-grey w3-padding-small' type='submit'
                              onclick='submitResponse(\"" . htmlspecialchars($resultRow['challenger'])
                           . "\", \"" . htmlspecialchars($resultRow['scheduled']) . "\")'
                              formaction='/rejectChallenge.php' >
                              Reject
                           </button>
                        </td>
                     </tr>";
                        $row++;
                     }
                     echo "
                  </table>
                  <table class='w3-table-all w3-centered w3-responsive w3-hoverable w3-hide-medium w3-hide-large'>
                     <tr class='w3-black'>
                        <th>Challenger</th>
                        <th>Issued On</th>
                        <th>Scheduled For</th>
                        <th>Respond To Challenge</th>
                     </tr>";
                     $row = 0;
                     foreach($challenges as $resultRow)
                     {
                        $rowColour = $rowColours[$row % 2];
                        echo "
                     <tr class='$rowColour'>
                        <td>" . htmlspecialchars($resultRow['name']) . "</td>
                        <td>" . htmlspecialchars($resultRow['issued']) . "</td>
                        <td>" . htmlspecialchars($resultRow['scheduled']) . "</td>
                        <td>
                           <button class='w3-button w3-tiny w3-light-grey w3-padding-small' type='submit'
                              onclick='submitResponse(\"" . htmlspecialchars($resultRow['challenger'])
                           . "\", \"" . htmlspecialchars($resultRow['scheduled']) . "\")'
                              formaction='/acceptChallenge.php' >
                              Accept
                           </button>
                           <button class='w3-button w3-tiny w3-light-grey w3-padding-small' type='submit'
                              onclick='submitResponse(\"" . htmlspecialchars($resultRow['challenger'])
                           . "\", \"" . htmlspecialchars($resultRow['scheduled']) . "\")'
                              formaction='/rejectChallenge.php' >
                              Reject
                           </button>
                        </td>
                     </tr>";
                        $row++;
                     }
                     echo "
                  </table>
               </form>";
                     if ($challengesReceived->rowCount() == 0)
                     {
                        echo "
               <h3 class='w3-text-light-grey'>NONE</h3>";
                     }
                     echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;

                     echo "
               <h2 class='w3-text-light-grey'>Challenges Sent</h2>
               <table class='w3-table-all w3-centered w3-hoverable w3-hide-small'>
                  <tr class='w3-black'>
                     <th>Challengee</th>
                     <th>Issued On</th>
                     <th>Scheduled For</th>
                  </tr>";
                     $challenges = $challengesSent->fetchAll(PDO::FETCH_ASSOC);
                     $row = 0;
                     foreach ($challenges as $resultRow)
                     {
                        $rowColour = $rowColours[$row % 2];
                        echo "
                  <tr class='$rowColour'>
                     <td>" . htmlspecialchars($resultRow['name']) . "</td>
                     <td>" . htmlspecialchars($resultRow['issued']) . "</td>
                     <td>" . htmlspecialchars($resultRow['scheduled']) . "</td>
                  </tr>";
                        $row++;
                     }
                     echo "
               </table>
               <table class='w3-table-all w3-centered w3-responsive w3-hoverable w3-hide-medium w3-hide-large'>
                  <tr class='w3-black'>
                     <th>Challengee</th>
                     <th>Issued On</th>
                     <th>Scheduled For</th>
                  </tr>";
                     $row = 0;
                     foreach ($challenges as $resultRow)
                     {
                        $rowColour = $rowColours[$row % 2];
                        echo "
                  <tr class='$rowColour'>
                     <td>" . htmlspecialchars($resultRow['name']) . "</td>
                     <td>" . htmlspecialchars($resultRow['issued']) . "</td>
                     <td>" . htmlspecialchars($resultRow['scheduled']) . "</td>
                  </tr>";
                        $row++;
                     }
                     echo "
               </table>";
                     if ($challengesSent->rowCount() == 0)
                     {
                        echo "
               <h3 class='w3-text-light-grey'>NONE</h3>" . PHP_EOL;
                     }
                  }

                  $challengeAccepted->closeCursor();
                  $challengesReceived->closeCursor();
                  $challengesSent->closeCursor();
               ?>
            </div>
         </div>

         <!-- Report Scores -->
         <div id="report_popup" class="w3-modal">
            <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-dark-grey" style="max-width:600px">
               <div class="w3-center"><br />
                  <span onclick="closePopup('report_popup', true);"
                     class="w3-button w3-xlarge w3-hover-black w3-display-topright" title="Close">
                     &times;
                  </span>
               </div>
               <form class="w3-container" id="report" name="report"
                  action="/reportScores.php" method="post">
                  <input type="text" style="display: none;" id="challenger_username" name="p1"
                     value=<?php echo '"' . htmlspecialchars($challenges["challenger"]) . '"' ?> />
                  <input type="text" style="display: none;" id="challengee_username" name="p2"
                     value=<?php echo '"' . htmlspecialchars($challenges["challengee"]) . '"' ?> />
                  <input type="text" style="display: none;" id="scheduled" name="scheduled"
                     value=<?php echo '"' . htmlspecialchars($challenges["scheduled"]) . '"' ?> />
                  <h3 class="w3-xlarge w3-center w3-text-light-grey">Report Scores</h3>
                  <table class="w3-table w3-centered">
                     <tr>
                        <th>Game</th>
                        <th><?php echo htmlspecialchars($challenges["challenger_name"]); ?>'s Score</th>
                        <th><?php echo htmlspecialchars($challenges["challengee_name"]); ?>'s Score</th>
                     </tr>
                     <tr id="game_1">
                        <td class="w3-large">1</td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p1_score_1" id="p1_score_1" required />
                        </td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p2_score_1" id="p2_score_1" required />
                        </td>
                     </tr>
                     <tr id="game_2">
                        <td class="w3-large">2</td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p1_score_2" id="p1_score_2" required />
                        </td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p2_score_2" id="p2_score_2" required />
                        </td>
                     </tr>
                     <tr id="game_3">
                        <td class="w3-large">3</td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p1_score_3" id="p1_score_3" required />
                        </td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p2_score_3" id="p2_score_3" required />
                        </td>
                     </tr>
                     <tr id="game_4" style="display: none;">
                        <td class="w3-large">4</td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p1_score_4" id="p1_score_4" />
                        </td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p2_score_4" id="p2_score_4" />
                        </td>
                     </tr>
                     <tr id="game_5" style="display: none;">
                        <td class="w3-large">5</td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p1_score_5" id="p1_score_5" />
                        </td>
                        <td>
                           <input class="w3-input w3-round-large" type="text"
                              name="p2_score_5" id="p2_score_5" />
                        </td>
                     </tr>
                  </table>
                  <p class="w3-large">
                     <button class="w3-button w3-block w3-grey w3-hover-black w3-padding-large" type="submit"
                        onclick="validateSubmission('report');">
                        <i class="fa fa-book"></i> REPORT
                     </button>
                  </p>
               </form>
               <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                  <button onclick="closePopup('report_popup', true);" type="button"
                     class="w3-button w3-dark-grey w3-hover-black">
                     Cancel
                  </button>
               </div>
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

      <script type="text/javascript">
         <!--
         // Setup password validation for account update and delete
         scoreValidation();
         // -->
      </script>
   </body>
</html>