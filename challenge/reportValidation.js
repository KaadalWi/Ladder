// Validates all the elements of the given form
function validateSubmission(formId)
{
   var currentForm = document.getElementById(formId);
   var allValid = true;

   for (var i = 0; i < currentForm.elements.length; i++)
   {
      if (!validateElement(currentForm.elements[i]))
      {
         allValid = false;
      }
   }

   return allValid;
}

// Validates the given form element
function validateElement(formElement)
{
   var isValid = false;

   // If the form element is invalid, set its background colour to indicate error
   if (!formElement.checkValidity())
   {
      formElement.style.backgroundColor = "#DF7F7F";
   }
   // Else if valid, set its background colour to default
   else
   {
      formElement.style.backgroundColor = "";
      isValid = true;
   }

   return isValid;
}

function checkScores(formId)
{
   var p1_wins = 0;
   var p2_wins = 0;
   var allValid = true;
   var p1_score_1 = document.getElementById("p1_score_1");
   var p2_score_1 = document.getElementById("p2_score_1");
   var p1_score_2 = document.getElementById("p1_score_2");
   var p2_score_2 = document.getElementById("p2_score_2");
   var p1_score_3 = document.getElementById("p1_score_3");
   var p2_score_3 = document.getElementById("p2_score_3");
   var p1_score_4 = document.getElementById("p1_score_4");
   var p2_score_4 = document.getElementById("p2_score_4");
   var p1_score_5 = document.getElementById("p1_score_5");
   var p2_score_5 = document.getElementById("p2_score_5");
   allValid = allValid && validateElement(p1_score_1);
   allValid = allValid && validateElement(p2_score_1);
   allValid = allValid && validateElement(p1_score_2);
   allValid = allValid && validateElement(p2_score_2);
   allValid = allValid && validateElement(p1_score_3);
   allValid = allValid && validateElement(p2_score_3);

   if (allValid)
   {
      p1_wins += (parseInt(p1_score_1.value) > parseInt(p2_score_1.value)) ? 1 : 0;
      p2_wins += (parseInt(p2_score_1.value) > parseInt(p1_score_1.value)) ? 1 : 0;
      p1_wins += (parseInt(p1_score_2.value) > parseInt(p2_score_2.value)) ? 1 : 0;
      p2_wins += (parseInt(p2_score_2.value) > parseInt(p1_score_2.value)) ? 1 : 0;
      p1_wins += (parseInt(p1_score_3.value) > parseInt(p2_score_3.value)) ? 1 : 0;
      p2_wins += (parseInt(p2_score_3.value) > parseInt(p1_score_3.value)) ? 1 : 0;

      if (p1_wins < 3 && p2_wins < 3)
      {
         document.getElementById("game_4").style.display = "";
         p1_score_4.required = true;
         p2_score_4.required = true;
         allValid = allValid && validateElement(p1_score_4);
         allValid = allValid && validateElement(p2_score_4);

         if (allValid)
         {
            p1_wins += (parseInt(p1_score_4.value) > parseInt(p2_score_4.value)) ? 1 : 0;
            p2_wins += (parseInt(p2_score_4.value) > parseInt(p1_score_4.value)) ? 1 : 0;

            if (p1_wins < 3 && p2_wins < 3)
            {
               document.getElementById("game_5").style.display = "";
               p1_score_5.required = true;
               p2_score_5.required = true;
            }
            else
            {
               document.getElementById("game_5").style.display = "none";
               p1_score_5.required = false;
               p1_score_5.value = "";
               p2_score_5.required = false;
               p2_score_5.value = "";
            }
         }
      }
      else
      {
         document.getElementById("game_4").style.display = "none";
         p1_score_4.required = false;
         p1_score_4.value = "";
         p2_score_4.required = false;
         p2_score_4.value = "";
         document.getElementById("game_5").style.display = "none";
         p1_score_5.required = false;
         p1_score_5.value = "";
         p2_score_5.required = false;
         p2_score_5.value = "";
      }
   }
}

// Sets up password validation for unregistering
function scoreValidation()
{
   var p1_score_1 = document.getElementById("p1_score_1");
   var p2_score_1 = document.getElementById("p2_score_1");
   var p1_score_2 = document.getElementById("p1_score_2");
   var p2_score_2 = document.getElementById("p2_score_2");
   var p1_score_3 = document.getElementById("p1_score_3");
   var p2_score_3 = document.getElementById("p2_score_3");
   var p1_score_4 = document.getElementById("p1_score_4");
   var p2_score_4 = document.getElementById("p2_score_4");
   var p1_score_5 = document.getElementById("p1_score_5");
   var p2_score_5 = document.getElementById("p2_score_5");

   // Ensures inputs are numbers and that they don't violate score rules
   p1_score_1.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p1_score_1.value)) || !Number.isInteger(parseInt(p1_score_1.value)) || parseInt(p1_score_1.value) < 0)
         {
            p1_score_1.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_1.value), parseInt(p2_score_1.value)) < 15 || Math.abs(parseInt(p1_score_1.value) - parseInt(p2_score_1.value)) < 2
            || (Math.max(parseInt(p1_score_1.value), parseInt(p2_score_1.value)) > 15 && Math.abs(parseInt(p1_score_1.value) - parseInt(p2_score_1.value)) != 2))
         {
            p1_score_1.setCustomValidity("Invalid scores");
            p2_score_1.setCustomValidity("Invalid scores");
            validateElement(p2_score_1);
         }
         else
         {
            p1_score_1.setCustomValidity("");
            p2_score_1.setCustomValidity("");
            validateElement(p2_score_1);
         }

         validateElement(p1_score_1);
         checkScores('report');
      }
   );

   p1_score_2.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p1_score_2.value)) || !Number.isInteger(parseInt(p1_score_2.value)) || parseInt(p1_score_2.value) < 0)
         {
            p1_score_2.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_2.value), parseInt(p2_score_2.value)) < 15 || Math.abs(parseInt(p1_score_2.value) - parseInt(p2_score_2.value)) < 2
            || (Math.max(parseInt(p1_score_2.value), parseInt(p2_score_2.value)) > 15 && Math.abs(parseInt(p1_score_2.value) - parseInt(p2_score_2.value)) != 2))
         {
            p1_score_2.setCustomValidity("Invalid scores");
            p2_score_2.setCustomValidity("Invalid scores");
            validateElement(p2_score_2);
         }
         else
         {
            p1_score_2.setCustomValidity("");
            p2_score_2.setCustomValidity("");
            validateElement(p2_score_2);
         }

         validateElement(p1_score_2);
         checkScores('report');
      }
   );

   p1_score_3.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p1_score_3.value)) || !Number.isInteger(parseInt(p1_score_3.value)) || parseInt(p1_score_3.value) < 0)
         {
            p1_score_3.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_3.value), parseInt(p2_score_3.value)) < 15 || Math.abs(parseInt(p1_score_3.value) - parseInt(p2_score_3.value)) < 2
            || (Math.max(parseInt(p1_score_3.value), parseInt(p2_score_3.value)) > 15 && Math.abs(parseInt(p1_score_3.value) - parseInt(p2_score_3.value)) != 2))
         {
            p1_score_3.setCustomValidity("Invalid scores");
            p2_score_3.setCustomValidity("Invalid scores");
            validateElement(p2_score_3);
         }
         else
         {
            p1_score_3.setCustomValidity("");
            p2_score_3.setCustomValidity("");
            validateElement(p2_score_3);
         }

         validateElement(p1_score_3);
         checkScores('report');
      }
   );

   p1_score_4.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p1_score_4.value)) || !Number.isInteger(parseInt(p1_score_4.value)) || parseInt(p1_score_4.value) < 0)
         {
            p1_score_4.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_4.value), parseInt(p2_score_4.value)) < 15 || Math.abs(parseInt(p1_score_4.value) - parseInt(p2_score_4.value)) < 2
            || (Math.max(parseInt(p1_score_4.value), parseInt(p2_score_4.value)) > 15 && Math.abs(parseInt(p1_score_4.value) - parseInt(p2_score_4.value)) != 2))
         {
            p1_score_4.setCustomValidity("Invalid scores");
            p2_score_4.setCustomValidity("Invalid scores");
            validateElement(p2_score_4);
         }
         else
         {
            p1_score_4.setCustomValidity("");
            p2_score_4.setCustomValidity("");
            validateElement(p2_score_4);
         }

         validateElement(p1_score_4);
         checkScores('report');
      }
   );

   p1_score_5.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p1_score_5.value)) || !Number.isInteger(parseInt(p1_score_5.value)) || parseInt(p1_score_5.value) < 0)
         {
            p1_score_5.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_5.value), parseInt(p2_score_5.value)) < 15 || Math.abs(parseInt(p1_score_5.value) - parseInt(p2_score_5.value)) < 2
            || (Math.max(parseInt(p1_score_5.value), parseInt(p2_score_5.value)) > 15 && Math.abs(parseInt(p1_score_5.value) - parseInt(p2_score_5.value)) != 2))
         {
            p1_score_5.setCustomValidity("Invalid scores");
            p2_score_5.setCustomValidity("Invalid scores");
            validateElement(p2_score_5);
         }
         else
         {
            p1_score_5.setCustomValidity("");
            p2_score_5.setCustomValidity("");
            validateElement(p2_score_5);
         }

         validateElement(p1_score_5);
         checkScores('report');
      }
   );

   p2_score_1.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p2_score_1.value)) || !Number.isInteger(parseInt(p2_score_1.value)) || parseInt(p2_score_1.value) < 0)
         {
            p2_score_1.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_1.value), parseInt(p2_score_1.value)) < 15 || Math.abs(parseInt(p1_score_1.value) - parseInt(p2_score_1.value)) < 2
            || (Math.max(parseInt(p1_score_1.value), parseInt(p2_score_1.value)) > 15 && Math.abs(parseInt(p1_score_1.value) - parseInt(p2_score_1.value)) != 2))
         {
            p1_score_1.setCustomValidity("Invalid scores");
            p2_score_1.setCustomValidity("Invalid scores");
            validateElement(p1_score_1);
         }
         else
         {
            p1_score_1.setCustomValidity("");
            p2_score_1.setCustomValidity("");
            validateElement(p1_score_1);
         }

         validateElement(p2_score_1);
         checkScores('report');
      }
   );

   p2_score_2.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p2_score_2.value)) || !Number.isInteger(parseInt(p2_score_2.value)) || parseInt(p2_score_2.value) < 0)
         {
            p2_score_2.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_2.value), parseInt(p2_score_2.value)) < 15 || Math.abs(parseInt(p1_score_2.value) - parseInt(p2_score_2.value)) < 2
            || (Math.max(parseInt(p1_score_2.value), parseInt(p2_score_2.value)) > 15 && Math.abs(parseInt(p1_score_2.value) - parseInt(p2_score_2.value)) != 2))
         {
            p1_score_2.setCustomValidity("Invalid scores");
            p2_score_2.setCustomValidity("Invalid scores");
            validateElement(p1_score_2);
         }
         else
         {
            p1_score_2.setCustomValidity("");
            p2_score_2.setCustomValidity("");
            validateElement(p1_score_2);
         }

         validateElement(p2_score_2);
         checkScores('report');
      }
   );

   p2_score_3.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p2_score_3.value)) || !Number.isInteger(parseInt(p2_score_3.value)) || parseInt(p2_score_3.value) < 0)
         {
            p2_score_3.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_3.value), parseInt(p2_score_3.value)) < 15 || Math.abs(parseInt(p1_score_3.value) - parseInt(p2_score_3.value)) < 2
            || (Math.max(parseInt(p1_score_3.value), parseInt(p2_score_3.value)) > 15 && Math.abs(parseInt(p1_score_3.value) - parseInt(p2_score_3.value)) != 2))
         {
            p1_score_3.setCustomValidity("Invalid scores");
            p2_score_3.setCustomValidity("Invalid scores");
            validateElement(p1_score_3);
         }
         else
         {
            p1_score_3.setCustomValidity("");
            p2_score_3.setCustomValidity("");
            validateElement(p1_score_3);
         }

         validateElement(p2_score_3);
         checkScores('report');
      }
   );

   p2_score_4.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p2_score_4.value)) || !Number.isInteger(parseInt(p2_score_4.value)) || parseInt(p2_score_4.value) < 0)
         {
            p2_score_4.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_4.value), parseInt(p2_score_4.value)) < 15 || Math.abs(parseInt(p1_score_4.value) - parseInt(p2_score_4.value)) < 2
            || (Math.max(parseInt(p1_score_4.value), parseInt(p2_score_4.value)) > 15 && Math.abs(parseInt(p1_score_4.value) - parseInt(p2_score_4.value)) != 2))
         {
            p1_score_4.setCustomValidity("Invalid scores");
            p2_score_4.setCustomValidity("Invalid scores");
            validateElement(p1_score_4);
         }
         else
         {
            p1_score_4.setCustomValidity("");
            p2_score_4.setCustomValidity("");
            validateElement(p1_score_4);
         }

         validateElement(p2_score_4);
         checkScores('report');
      }
   );

   p2_score_5.addEventListener("input", function (event)
      {
         if (isNaN(parseInt(p2_score_5.value)) || !Number.isInteger(parseInt(p2_score_5.value)) || parseInt(p2_score_5.value) < 0)
         {
            p2_score_5.setCustomValidity("Enter a non-negative integer");
         }
         else if (Math.max(parseInt(p1_score_5.value), parseInt(p2_score_5.value)) < 15 || Math.abs(parseInt(p1_score_5.value) - parseInt(p2_score_5.value)) < 2
            || (Math.max(parseInt(p1_score_5.value), parseInt(p2_score_5.value)) > 15 && Math.abs(parseInt(p1_score_5.value) - parseInt(p2_score_5.value)) != 2))
         {
            p1_score_5.setCustomValidity("Invalid scores");
            p2_score_5.setCustomValidity("Invalid scores");
            validateElement(p1_score_5);
         }
         else
         {
            p1_score_5.setCustomValidity("");
            p2_score_5.setCustomValidity("");
            validateElement(p1_score_5);
         }

         validateElement(p2_score_5);
         checkScores('report');
      }
   );
}