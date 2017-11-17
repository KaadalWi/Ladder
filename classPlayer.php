<?php
   class Player
   {
      public $name;
      public $email;
      public $phone;
      public $username;
      public $rank;
      
      function __construct($playerName, $playerEmail, $playerPhone, $playerUsername, $playerRank)
      {
         $this->name = $playerName;
         $this->email = $playerEmail;
         $this->phone = $playerPhone;
         $this->username = $playerUsername;
         $this->rank = $playerRank;
      }
   }
?>