// Validates all the elements of the given form
function validateSubmission(formId)
{
   var currentForm = document.getElementById(formId);

   for (var i = 0; i < currentForm.elements.length; i++)
   {
      validateElement(currentForm.elements[i]);
   }
}

// Validates the given form element
function validateElement(formElement)
{
   // If the form element is invalid, set its background colour to indicate error
   if (!formElement.checkValidity())
   {
      formElement.style.backgroundColor = "#DF7F7F";
   }
   // Else if valid, set its background colour to default
   else
   {
      formElement.style.backgroundColor = "";
   }
}

// Sets up password validation for update
function passwordUpdateValidation()
{
   var password = document.getElementById("password_new");
   var passwordConfirm = document.getElementById("password_new_confirm");

   // Compares password to passwordConfirm on validation of password
   password.addEventListener("input", function (event)
      {
         if (passwordConfirm.value != password.value)
         {
            passwordConfirm.setCustomValidity("Enter matching password to confirm");
         }
         else
         {
            passwordConfirm.setCustomValidity("");
         }

         validateElement(passwordConfirm);
      }
   );

   // Compares password to passwordConfirm on validation of passwordConfirm
   passwordConfirm.addEventListener("input", function (event)
      {
         if (passwordConfirm.value != password.value)
         {
            passwordConfirm.setCustomValidity("Enter matching password to confirm");
         }
         else
         {
            passwordConfirm.setCustomValidity("");
         }

         validateElement(passwordConfirm);
      }
   );
}

// Sets up password validation for unregistering
function passwordUnregisterValidation()
{
   var password = document.getElementById("password_unregister");
   var passwordConfirm = document.getElementById("password_unregister_confirm");

   // Compares password to passwordConfirm on validation of password
   password.addEventListener("input", function (event)
      {
         if (password.value != "" && passwordConfirm.value != password.value)
         {
            passwordConfirm.setCustomValidity("Enter matching password to confirm");
         }
         else
         {
            passwordConfirm.setCustomValidity("");
         }

         validateElement(passwordConfirm);
      }
   );

   // Compares password to passwordConfirm on validation of passwordConfirm
   passwordConfirm.addEventListener("input", function (event)
      {
         if (password.value != "" && passwordConfirm.value != password.value)
         {
            passwordConfirm.setCustomValidity("Enter matching password to confirm");
         }
         else
         {
            passwordConfirm.setCustomValidity("");
         }

         validateElement(passwordConfirm);
      }
   );
}