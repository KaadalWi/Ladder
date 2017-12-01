// Makes the given popup visible
function openPopup(popupId)
{
   document.getElementById(popupId).style.display = "block";
}

// Hides the given popup, and clears it if prompted
function closePopup(popupId, clearForm)
{
   var popupForm;
   document.getElementById(popupId).style.display = "none";
   
   if (clearForm)
   {
      // Reset the popup's form
      popupForm = document.getElementById(popupId.replace("_popup", ""));
      popupForm.reset();
      
      // For each form element, remove any error indicator
      for (var i = 0; i < popupForm.elements.length; i++)
      {
         popupForm.elements[i].style.backgroundColor = "";
      }
   }
}