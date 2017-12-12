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
      <script type="text/javascript" src="/account/popups.js"></script>
      <script type="text/javascript" src="/account/validation.js"></script>
   </head>
   <body class="w3-black">

      <!-- Icon Bar (Sidebar - hidden on small screens) -->
      <nav class="w3-sidebar w3-bar-block w3-small w3-hide-small w3-center">
         <!-- Avatar image in top left corner -->
         <a href="/ladder.php#ladder" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <img src="/icons/ladder_icon.jpg" style="width:100%">
            <p>LADDER</p>
         </a>
         <a href="/challenges.php#challenges" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-envelope w3-xxlarge"></i>
            <p>CHALLENGES</p>
         </a>
         <a href="/games.php" class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-newspaper-o w3-xxlarge"></i>
            <p>GAMES</p>
         </a>
         <a class="w3-bar-item w3-button w3-padding-large w3-hover-black">
            <i class="fa fa-table w3-xxlarge"></i>
            <p>MATCHES</p>
         </a>
         <a href="/account.php#account" class="w3-bar-item w3-button w3-padding-large w3-black">
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
            <a href="/challenges.php#challenges" class="w3-bar-item w3-button" style="width:15% !important">CHAL</a>
            <a href="/games.php" class="w3-bar-item w3-button" style="width:16% !important">GAME</a>
            <a class="w3-bar-item w3-button" style="width:18.5% !important">MATCH</a>
            <a href="/account.php#account" class="w3-bar-item w3-button w3-grey" style="width:15% !important">ACCT</a>
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

         <!-- Account Info -->
         <div class="w3-content w3-center w3-text-grey w3-padding-64" id="account">
            <div class="w3-large w3-justified">
               <ul class="w3-ul w3-border w3-responsive">
                  <li class="w3-black">
                     <h2 class="w3-text-light-grey">
                        <?php echo htmlspecialchars($_SESSION['user']->name); ?>'s Account
                     </h2>
                  </li>
                  <li class="w3-dark-grey">Username: <?php echo htmlspecialchars($_SESSION['user']->username); ?></li>
                  <li class="w3-grey">Rank: <?php echo htmlspecialchars($_SESSION['user']->rank); ?></li>
                  <li class="w3-dark-grey">Email: <?php echo htmlspecialchars($_SESSION['user']->email); ?></li>
                  <li class="w3-grey">Phone: <?php echo htmlspecialchars($_SESSION['user']->phone); ?></li>
                  <li class="w3-dark-grey">Password: **********</li>
               </ul>
               <br />
               <a class="w3-bar-item w3-button w3-padding-large w3-hover-black"
                  onclick="openPopup('update_popup');">
                  <button class="w3-button w3-light-grey w3-padding-large w3-section">
                     <i class="fa fa-book"></i> UPDATE INFO
                  </button>
               </a>
               <br />
               <a class="w3-bar-item w3-button w3-padding-large w3-hover-black"
                  onclick="openPopup('unregister_popup');">
                  <button class="w3-button w3-light-grey w3-padding-large w3-section">
                     <i class="fa fa-minus-circle"></i> UNREGISTER
                  </button>
               </a>
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

         <!-- Update Account -->
         <div id="update_popup" class="w3-modal">
            <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-dark-grey" style="max-width:600px">
               <div class="w3-center"><br />
                  <span onclick="closePopup('update_popup', true);"
                     class="w3-button w3-xlarge w3-hover-black w3-display-topright" title="Close">
                     &times;
                  </span>
               </div>
               <form class="w3-container" id="update" name="update"
                  action="/Secure/updateAccount.php" method="post">
                  <h3 class="w3-xlarge w3-center w3-text-light-grey">Update Account</h3>
                  <p>
                     <input class="w3-input w3-padding-16 w3-round-large" type="email"
                        name="email" id="email" placeholder="  Email (e.g. person@email.com)"
                        pattern="^\w+([\.\-!#$%&'*+/=?^`{|}~]?\w+)*@\w+([\.\-]?\w+)*(\.\w{2,3})+$"
                        oninput="validateElement(this);" />
                  </p>
                  <p>
                     <input class="w3-input w3-padding-16 w3-round-large" type="tel"
                        name="phone" id="phone" placeholder="  Phone Number (e.g. XXX-XXX-XXXX)"
                        pattern="^\d{10}$"
                        oninput="validateElement(this);" />
                  </p>
                  <p>
                     <input class="w3-input w3-padding-16 w3-round-large" type="password"
                        name="password_new" id="password_new" placeholder="  New Password" 
                        oninput="validateElement(this);" />
                  </p>
                  <p>
                     <input class="w3-input w3-padding-16 w3-round-large" type="password"
                        id="password_new_confirm" placeholder="  Confirm New Password" />
                  </p>
                  <p>
                     <input class="w3-input w3-padding-16 w3-round-large" type="password"
                        name="password" id="password" placeholder="* Password" required
                        oninput="validateElement(this);" />
                  </p>
                  <p class="w3-text-light-grey">* Required field</p>
                  <p class="w3-large">
                     <button class="w3-button w3-block w3-grey w3-hover-black w3-padding-large" type="submit"
                        onclick="validateSubmission('update');">
                        <i class="fa fa-book"></i> UPDATE
                     </button>
                  </p>
               </form>
               <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                  <button onclick="closePopup('update_popup', true);" type="button"
                     class="w3-button w3-dark-grey w3-hover-black">
                     Cancel
                  </button>
               </div>
            </div>
         </div>

         <!-- Unregister -->
         <div id="unregister_popup" class="w3-modal">
            <div class="w3-modal-content w3-card-4 w3-animate-zoom w3-dark-grey" style="max-width:600px">
               <div class="w3-center"><br />
                  <span onclick="closePopup('unregister_popup', true);"
                     class="w3-button w3-xlarge w3-hover-black w3-display-topright" title="Close">
                     &times;
                  </span>
               </div>
               <form class="w3-container" id="register" name="unregister"
                  action="/Secure/unregister.php" method="post">
                  <h3 class="w3-xlarge w3-center w3-text-light-grey">Unregister</h3>
                  <p>
                     <input class="w3-input w3-padding-16 w3-round-large" type="password"
                        name="password" id="password_unregister" placeholder="* Password" required
                        oninput="validateElement(this);" />
                  </p>
                  <p>
                     <input class="w3-input w3-padding-16 w3-round-large" type="password"
                        id="password_unregister_confirm" placeholder="* Confirm Password" required />
                  </p>
                  <p class="w3-text-light-grey">* Required fields</p>
                  <p class="w3-large">
                     <button class="w3-button w3-block w3-grey w3-hover-black w3-padding-large" type="submit"
                        onclick="validateSubmission('unregister');">
                        <i class="fa fa-minus-circle"></i> UNREGISTER
                     </button>
                  </p>
               </form>
               <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
                  <button onclick="closePopup('unregister_popup', true);" type="button"
                     class="w3-button w3-dark-grey w3-hover-black">
                     Cancel
                  </button>
               </div>
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
         passwordUpdateValidation();
         passwordUnregisterValidation();
         // -->
      </script>
   </body>
</html>