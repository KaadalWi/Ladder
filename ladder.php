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
      $challengeable = array();

      foreach ($challenging->fetchAll(PDO::FETCH_ASSOC) as $challengeRow)
      {
         $challengeable[$challengeRow['challengee_username']] = $challengeRow['potential_challengee'];
      }
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
      <script type="text/javascript" src="/challenge/popup.js"></script>
      <script type="text/javascript" src="/challenge/dateValidation.js"></script>
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
               <?php echo htmlspecialchars($_SESSION['user']->name); ?>
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
                     <!--<th>&nbsp;</th>-->
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
                        echo "
                  <tr class='$rowColour'>" . PHP_EOL;
                        echo '
                     <td>' . htmlspecialchars($resultRow['rank']) . '</td>' . PHP_EOL;
                        echo '
                     <td>' . htmlspecialchars($resultRow['name']) . PHP_EOL;
                        if ($challengeable[$resultRow['username']])
                        {
                           echo '
                        <br />
                        <button class="w3-button w3-tiny w3-light-grey w3-padding-small"
                           onclick="openPopup(\'challenge_popup\', \''
                              . htmlspecialchars($challengeable[$resultRow['username']]) . '\', \''
                              . htmlspecialchars($resultRow['username']) . '\')">
                           Challenge
                        </button>
                     </td>' . PHP_EOL;
                        }
                        else
                        {
                           echo '
                        &nbsp;
                     </td>' . PHP_EOL;
                        }
                        echo '
                     <td>' . sprintf("%.2f", $resultRow['match_win_percentage']) . '</td>' . PHP_EOL;
                        echo '
                     <td>' . sprintf("%.2f", $resultRow['game_win_percentage']) . '</td>' . PHP_EOL;
                        echo '
                     <td>' . sprintf("%.2f", $resultRow['average_win_margin']) . '</td>' . PHP_EOL;
                        echo '
                     <td>' . sprintf("%.2f", $resultRow['match_loss_percentage']) . '</td>' . PHP_EOL;
                        echo '
                     <td>' . sprintf("%.2f", $resultRow['game_loss_percentage']) . '</td>' . PHP_EOL;
                        echo '
                     <td>' . sprintf("%.2f", $resultRow['average_loss_margin']) . '</td>' . PHP_EOL;
                        echo '
                  </tr>' . PHP_EOL;
                        $row++;
                     }

                     $ladderStats->closeCursor();
                     $challenging->closeCursor();
                  ?>
               </table>
            </div>
         </div>

         <div id="challenge_popup" class="w3-modal">
            <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-dark-grey" style="max-width:600px">
               <div class="w3-center"><br />
                  <span onclick="closePopup('challenge_popup');"
                     class="w3-button w3-xlarge w3-hover-black w3-display-topright" title="Close">
                     &times;
                  </span>
               </div>
               <form class="w3-container w3-center" id="challenge" name="challenge" onreset="validateYear();"
                  action="/issueChallenge.php" method="post">
                  <h3 class="w3-xlarge w3-center w3-text-light-grey">Challenge <span id="challengee_name"></span></h3>
                  <input type="text" style="display: none;" id="challengee_username" name="challengee_username" />
                  <input type="text" style="display: none;" id="match_time" name="match_time" />
                  <p>
                     <span class="w3-large w3-text-light-grey"> Match Time:</span>
                     <div class="w3-dropdown-click">
                        <select class="w3-select w3-border w3-padding-16 w3-round-large w3-dropdown-click"
                           id="match_year" onchange="validateDay();" required></select>
                     </div>
                     <div class="w3-dropdown-click">
                        <select class="w3-select w3-border w3-padding-16 w3-round-large w3-dropdown-click"
                           id="match_month" onchange="validateDay();" required>
                           <option value="01">&nbsp;January</option>
                           <option value="02">&nbsp;February</option>
                           <option value="03">&nbsp;March</option>
                           <option value="04">&nbsp;April</option>
                           <option value="05">&nbsp;May</option>
                           <option value="06">&nbsp;June</option>
                           <option value="07">&nbsp;July</option>
                           <option value="08">&nbsp;August</option>
                           <option value="09">&nbsp;September</option>
                           <option value="10">&nbsp;October</option>
                           <option value="11">&nbsp;November</option>
                           <option value="12">&nbsp;December</option>
                        </select>
                     </div>
                     <div class="w3-dropdown-click">
                        <select class="w3-select w3-border w3-padding-16 w3-round-large w3-dropdown-click"
                           id="match_day" required>
                           <option value="01">&nbsp;01</option>
                           <option value="02">&nbsp;02</option>
                           <option value="03">&nbsp;03</option>
                           <option value="04">&nbsp;04</option>
                           <option value="05">&nbsp;05</option>
                           <option value="06">&nbsp;06</option>
                           <option value="07">&nbsp;07</option>
                           <option value="08">&nbsp;08</option>
                           <option value="09">&nbsp;09</option>
                           <option value="10">&nbsp;10</option>
                           <option value="11">&nbsp;11</option>
                           <option value="12">&nbsp;12</option>
                           <option value="13">&nbsp;13</option>
                           <option value="14">&nbsp;14</option>
                           <option value="15">&nbsp;15</option>
                           <option value="16">&nbsp;16</option>
                           <option value="17">&nbsp;17</option>
                           <option value="18">&nbsp;18</option>
                           <option value="19">&nbsp;19</option>
                           <option value="20">&nbsp;20</option>
                           <option value="21">&nbsp;21</option>
                           <option value="22">&nbsp;22</option>
                           <option value="23">&nbsp;23</option>
                           <option value="24">&nbsp;24</option>
                           <option value="25">&nbsp;25</option>
                           <option value="26">&nbsp;26</option>
                           <option value="27">&nbsp;27</option>
                           <option value="28">&nbsp;28</option>
                           <option value="29" id="challenge_form_day_29">&nbsp;29</option>
                           <option value="30" id="challenge_form_day_30">&nbsp;30</option>
                           <option value="31" id="challenge_form_day_31">&nbsp;31</option>
                        </select>
                     </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                     <div class="w3-dropdown-click">
                        <select class="w3-select w3-border w3-padding-16 w3-round-large w3-dropdown-click"
                           id="match_hour" required>
                           <option value="00">&nbsp;00</option>
                           <option value="01">&nbsp;01</option>
                           <option value="02">&nbsp;02</option>
                           <option value="03">&nbsp;03</option>
                           <option value="04">&nbsp;04</option>
                           <option value="05">&nbsp;05</option>
                           <option value="06">&nbsp;06</option>
                           <option value="07">&nbsp;07</option>
                           <option value="08">&nbsp;08</option>
                           <option value="09">&nbsp;09</option>
                           <option value="10">&nbsp;10</option>
                           <option value="11">&nbsp;11</option>
                           <option value="12">&nbsp;12</option>
                           <option value="13">&nbsp;13</option>
                           <option value="14">&nbsp;14</option>
                           <option value="15">&nbsp;15</option>
                           <option value="16">&nbsp;16</option>
                           <option value="17">&nbsp;17</option>
                           <option value="18">&nbsp;18</option>
                           <option value="19">&nbsp;19</option>
                           <option value="20">&nbsp;20</option>
                           <option value="21">&nbsp;21</option>
                           <option value="22">&nbsp;22</option>
                           <option value="23">&nbsp;23</option>
                        </select>
                     </div>&nbsp;:&nbsp;
                     <div class="w3-dropdown-click">
                        <select class="w3-select w3-border w3-padding-16 w3-round-large w3-dropdown-click"
                           id="match_minute" required>
                           <option value="00">&nbsp;00</option>
                           <option value="01">&nbsp;01</option>
                           <option value="02">&nbsp;02</option>
                           <option value="03">&nbsp;03</option>
                           <option value="04">&nbsp;04</option>
                           <option value="05">&nbsp;05</option>
                           <option value="06">&nbsp;06</option>
                           <option value="07">&nbsp;07</option>
                           <option value="08">&nbsp;08</option>
                           <option value="09">&nbsp;09</option>
                           <option value="10">&nbsp;10</option>
                           <option value="11">&nbsp;11</option>
                           <option value="12">&nbsp;12</option>
                           <option value="13">&nbsp;13</option>
                           <option value="14">&nbsp;14</option>
                           <option value="15">&nbsp;15</option>
                           <option value="16">&nbsp;16</option>
                           <option value="17">&nbsp;17</option>
                           <option value="18">&nbsp;18</option>
                           <option value="19">&nbsp;19</option>
                           <option value="20">&nbsp;20</option>
                           <option value="21">&nbsp;21</option>
                           <option value="22">&nbsp;22</option>
                           <option value="23">&nbsp;23</option>
                           <option value="24">&nbsp;24</option>
                           <option value="25">&nbsp;25</option>
                           <option value="26">&nbsp;26</option>
                           <option value="27">&nbsp;27</option>
                           <option value="28">&nbsp;28</option>
                           <option value="29">&nbsp;29</option>
                           <option value="30">&nbsp;30</option>
                           <option value="31">&nbsp;31</option>
                           <option value="32">&nbsp;32</option>
                           <option value="33">&nbsp;33</option>
                           <option value="34">&nbsp;34</option>
                           <option value="35">&nbsp;35</option>
                           <option value="36">&nbsp;36</option>
                           <option value="37">&nbsp;37</option>
                           <option value="38">&nbsp;38</option>
                           <option value="39">&nbsp;39</option>
                           <option value="40">&nbsp;40</option>
                           <option value="41">&nbsp;41</option>
                           <option value="42">&nbsp;42</option>
                           <option value="43">&nbsp;43</option>
                           <option value="44">&nbsp;44</option>
                           <option value="45">&nbsp;45</option>
                           <option value="46">&nbsp;46</option>
                           <option value="47">&nbsp;47</option>
                           <option value="48">&nbsp;48</option>
                           <option value="49">&nbsp;49</option>
                           <option value="50">&nbsp;50</option>
                           <option value="51">&nbsp;51</option>
                           <option value="52">&nbsp;52</option>
                           <option value="53">&nbsp;53</option>
                           <option value="54">&nbsp;54</option>
                           <option value="55">&nbsp;55</option>
                           <option value="56">&nbsp;56</option>
                           <option value="57">&nbsp;57</option>
                           <option value="58">&nbsp;58</option>
                           <option value="59">&nbsp;59</option>
                        </select>
                     </div>&nbsp;:&nbsp;
                     <div class="w3-dropdown-click">
                        <select class="w3-select w3-border w3-padding-16 w3-round-large w3-dropdown-click"
                           id="match_second" required>
                           <option value="00">&nbsp;00</option>
                           <option value="01">&nbsp;01</option>
                           <option value="02">&nbsp;02</option>
                           <option value="03">&nbsp;03</option>
                           <option value="04">&nbsp;04</option>
                           <option value="05">&nbsp;05</option>
                           <option value="06">&nbsp;06</option>
                           <option value="07">&nbsp;07</option>
                           <option value="08">&nbsp;08</option>
                           <option value="09">&nbsp;09</option>
                           <option value="10">&nbsp;10</option>
                           <option value="11">&nbsp;11</option>
                           <option value="12">&nbsp;12</option>
                           <option value="13">&nbsp;13</option>
                           <option value="14">&nbsp;14</option>
                           <option value="15">&nbsp;15</option>
                           <option value="16">&nbsp;16</option>
                           <option value="17">&nbsp;17</option>
                           <option value="18">&nbsp;18</option>
                           <option value="19">&nbsp;19</option>
                           <option value="20">&nbsp;20</option>
                           <option value="21">&nbsp;21</option>
                           <option value="22">&nbsp;22</option>
                           <option value="23">&nbsp;23</option>
                           <option value="24">&nbsp;24</option>
                           <option value="25">&nbsp;25</option>
                           <option value="26">&nbsp;26</option>
                           <option value="27">&nbsp;27</option>
                           <option value="28">&nbsp;28</option>
                           <option value="29">&nbsp;29</option>
                           <option value="30">&nbsp;30</option>
                           <option value="31">&nbsp;31</option>
                           <option value="32">&nbsp;32</option>
                           <option value="33">&nbsp;33</option>
                           <option value="34">&nbsp;34</option>
                           <option value="35">&nbsp;35</option>
                           <option value="36">&nbsp;36</option>
                           <option value="37">&nbsp;37</option>
                           <option value="38">&nbsp;38</option>
                           <option value="39">&nbsp;39</option>
                           <option value="40">&nbsp;40</option>
                           <option value="41">&nbsp;41</option>
                           <option value="42">&nbsp;42</option>
                           <option value="43">&nbsp;43</option>
                           <option value="44">&nbsp;44</option>
                           <option value="45">&nbsp;45</option>
                           <option value="46">&nbsp;46</option>
                           <option value="47">&nbsp;47</option>
                           <option value="48">&nbsp;48</option>
                           <option value="49">&nbsp;49</option>
                           <option value="50">&nbsp;50</option>
                           <option value="51">&nbsp;51</option>
                           <option value="52">&nbsp;52</option>
                           <option value="53">&nbsp;53</option>
                           <option value="54">&nbsp;54</option>
                           <option value="55">&nbsp;55</option>
                           <option value="56">&nbsp;56</option>
                           <option value="57">&nbsp;57</option>
                           <option value="58">&nbsp;58</option>
                           <option value="59">&nbsp;59</option>
                        </select>
                     </div>
                  </p>
                  <p class="w3-large">
                     <button class="w3-button w3-block w3-grey w3-hover-black w3-padding-large" type="submit"
                        onclick="submitTime();">
                        <i class="fa fa-arrow-right"></i> CONFIRM
                     </button>
                  </p>
               </form>
               <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                  <button onclick="closePopup('challenge_popup');" type="button"
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

   </body>
</html>