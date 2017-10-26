function validateSubmission(formId)
{
   var currentForm = document.getElementById(formId);

   for (var i = 0; i < currentForm.elements.length; i++)
   {
      if (!currentForm.elements[i].checkValidity())
      {
         currentForm.elements[i].style.border = "2px solid";
         currentForm.elements[i].classList.add("w3-border-red");
      }
      else
      {
         currentForm.elements[i].style.border = "";
         currentForm.elements[i].classList.remove("w3-border-red");
      }
   }
}

function validateElement(formElement)
{
   if (!formElement.checkValidity())
   {
      formElement.style.border = "2px solid";
      formElement.classList.add("w3-border-red");
   }
   else
   {
      formElement.style.border = "";
      formElement.classList.remove("w3-border-red");
   }
}

function passwordRegisterValidation()
{
   var password = document.getElementById("password_register");
   var passwordConfirm = document.getElementById("password_confirm");

   passwordConfirm.addEventListener("input", function (event)
      {
         if (passwordConfirm.value != "" && passwordConfirm.value != password.value)
         {
            passwordConfirm.setCustomValidity("Enter matching password to confirm");
         }
         else
         {
            passwordConfirm.setCustomValidity("");
         }
      }
   );
}