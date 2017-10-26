function validateSubmission(formId)
{
   var currentForm = document.getElementById(formId);

   for (var i = 0; i < currentForm.elements.length; i++)
   {
      validateElement(currentForm.elements[i]);
   }
}

function validateElement(formElement)
{
   if (!formElement.checkValidity())
   {
      formElement.style.backgroundColor = "#DF7F7F";
   }
   else
   {
      formElement.style.backgroundColor = "";
   }
}

function passwordRegisterValidation()
{
   var password = document.getElementById("password_register");
   var passwordConfirm = document.getElementById("password_confirm");

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